<?php
namespace Basil832025\FrontendThreePiroga\Http\Controllers;

use App\Services\CartService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Shop\Product;
use App\Models\Shop\ProductCategory;
use App\Support\Presenters\ProductCardPresenter;

class CartController extends Controller
{
    public function __construct(private readonly CartService $cart) {}

// app/Http/Controllers/Front/CartController.php

    public function add(Request $r)
    {
        // Принимаем и product_id, и старое поле product
        $pid = (int) $r->input('product_id', 0);
        if (! $pid) {
            $pid = (int) $r->input('product', 0);
        }

        if (! $pid) {
            return back()->with('cart_error', 'Не передан идентификатор товара.');
        }

        $qty   = (int) $r->input('qty', 1);
        $price = $r->has('price') ? (float) $r->input('price') : null;

        // Если передан параметр set=true, устанавливаем абсолютное количество
        if ($r->boolean('set', false)) {
            $payload = $this->cart->setQty($pid, $qty, $price);
        } else {
            // Иначе добавляем/уменьшаем количество (может быть отрицательным для уменьшения)
            // CartService::add поддерживает отрицательные значения для уменьшения количества
            $payload = $this->cart->add($pid, $qty, $price);
        }

        // Для AJAX/JSON-запросов возвращаем JSON как раньше
        if ($r->expectsJson() || $r->wantsJson() || $r->ajax()) {
            return response()->json($payload);
        }

        // Для обычной формы (карточка товара) просто возвращаемся назад
        return back()->with('cart', $payload);
    }
public function page()
{
    $locale = app()->getLocale();
    $info = $this->cart->info();
    $relatedProducts = $this->relatedProductsForCart(['napitki', 'sousu-k-pirogam'], 3);
    $related = (new ProductCardPresenter($locale, null, true))->collection($relatedProducts);

    return view(front_view('cart.index'), [
        'items' => $info['items'] ?? [],
        'qty'   => (int)($info['qty'] ?? 0),
        'total' => (float)($info['total'] ?? $info['total_price'] ?? 0),
        'related' => $related,
    ]);
}

private function relatedProductsForCart(array $categorySlugs, int $limit)
{
    $picked = collect();

    foreach ($categorySlugs as $slug) {
        if ($picked->count() >= $limit) {
            break;
        }

        $categoryIds = $this->relatedCategoryIds([$slug]);
        if ($categoryIds === []) {
            continue;
        }

        $product = $this->relatedProductsQuery($categoryIds)
            ->when($picked->isNotEmpty(), fn ($query) => $query->whereNotIn('bs_products.id', $picked->pluck('id')->all()))
            ->inRandomOrder()
            ->first();

        if ($product) {
            $picked->push($product);
        }
    }

    if ($picked->count() < $limit) {
        $allCategoryIds = $this->relatedCategoryIds($categorySlugs);
        $extra = $this->relatedProductsQuery($allCategoryIds)
            ->when($picked->isNotEmpty(), fn ($query) => $query->whereNotIn('bs_products.id', $picked->pluck('id')->all()))
            ->inRandomOrder()
            ->limit($limit - $picked->count())
            ->get();

        $picked = $picked->merge($extra);
    }

    return $picked->values();
}

private function relatedProductsQuery(array $categoryIds)
{
    return Product::withListingCardRelations()
        ->active()
        ->cardListingSelect()
        ->MainProduct()
        ->where(function ($query) {
            $query->whereNull('is_imported')
                ->orWhere('is_imported', false);
        })
        ->where(function ($categoryQuery) use ($categoryIds) {
            $categoryQuery
                ->whereIn('category_id', $categoryIds)
                ->orWhereHas('categories', fn ($categories) => $categories->whereIn('bs_product_categories.id', $categoryIds));
        });
}

private function relatedCategoryIds(array $slugs): array
{
    return ProductCategory::query()
        ->whereIn('slug', $slugs)
        ->get()
        ->flatMap(fn (ProductCategory $category) => array_merge([$category->id], $category->getDescendantIds()))
        ->map(fn ($id) => (int) $id)
        ->unique()
        ->values()
        ->all();
}

public function remove(Request $r)
{
    $pid = $r->integer('product_id');
    $all = $r->boolean('all', false);

    $payload = $this->cart->remove($pid, $all);

    return response()->json($payload);
}

// HTML для сайдбара — ОТДЕЛЬНО
public function sidebar()
{
    $info = $this->cart->info();
    return view(front_view('partials.cart-sidebar'), [
        'items' => $info['items'] ?? [],
        'qty'   => (int)($info['qty'] ?? 0),
        'total' => (float)($info['total'] ?? $info['total_price'] ?? 0),
    ]);
}
public function info()
{
    return response()->json($this->cart->info());
}
}
