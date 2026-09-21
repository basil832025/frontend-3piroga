@extends(front_view('layouts.app'))

@php
    $seoTitle = trim((string) ($product['seo_title'] ?? ''));
    if ($seoTitle === '') {
        $seoTitle = trim((string) ($product['title'] ?? ''));
    }
    $seoDescription = trim((string) ($product['seo_description'] ?? ''));
    if ($seoDescription === '') {
        $seoDescription = trim(strip_tags((string) ($product['description'] ?? '')));
    }
    if (mb_strlen($seoDescription) > 250) {
        $seoDescription = rtrim(mb_substr($seoDescription, 0, 247)) . '...';
    }
    $seoKeywords = trim((string) ($product['seo_keywords'] ?? ''));
    $ogImage = (string) ($product['main_image'] ?? '');
    $ogImage = $ogImage !== '' ? $ogImage : '';
@endphp

@section('title', $seoTitle)
@section('meta_description', $seoDescription)
@if($seoKeywords !== '')
    @section('meta_keywords', $seoKeywords)
@endif

@section('og_type', 'product')
@section('og_title', $seoTitle)
@section('og_description', $seoDescription)
@if($ogImage !== '')
    @section('og_image', $ogImage)
    @section('twitter_image', $ogImage)
@endif
@section('twitter_title', $seoTitle)
@section('twitter_description', $seoDescription)

@push('head')
    @include(front_view('seo.product-jsonld'), ['product' => $product, 'category' => $category ?? null, 'stats' => $stats ?? null, 'reviews' => $reviews ?? null])
@endpush

@section('content')
    @php
        // данные для селектора
        $rows            = $product['variant_rows'];
        $characteristics = $product['characteristics'];
        $rootId          = $product['root_id'];
        $price           = $product['price'];
        $price_no_sale   = $product['old_price'];

        // подготовка стора
        $rootId  = $rootId ?? ($rows[0]['product_id'] ?? null);
        $rootKey = (string) ($rows[0]['variant_key'] ?? $rootId ?? '');
        $priceMap = [];
        $productIdMap = [];
        $metaMap = [];
        $productKeyMap = [];
        $articleMap = [];
        $badgeMap = [];
        $manualDiscountMap = [];
        foreach ($rows as $row) {
            $rowKey = (string) ($row['variant_key'] ?? $row['product_id'] ?? '');
            $priceMap[$rowKey] = [
                'price' => (float)($row['price'] ?? 0),
                'old'   => isset($row['old_price']) ? (float)$row['old_price'] : null,
            ];
            $productIdMap[$rowKey] = (int) ($row['cart_product_id'] ?? $row['product_id'] ?? 0);
            $metaMap[$rowKey] = is_array($row['cart_meta'] ?? null) ? $row['cart_meta'] : [];
            $productKeyMap[$rowKey] = trim((string) ($row['product_key'] ?? $row['article'] ?? $row['product_id']));
            $manualDiscountMap[$rowKey] = isset($row['manual_discount_percent']) && $row['manual_discount_percent'] !== null && $row['manual_discount_percent'] !== ''
                ? round((float) $row['manual_discount_percent'])
                : null;
            $articleMap[$rowKey] = trim((string)($row['article'] ?? ''));

            $badgeMap[$rowKey] = [
                'is_spicy' => (bool)($row['is_spicy'] ?? false),
                'is_new' => (bool)($row['is_new'] ?? false),
                'is_promo' => (bool)($row['is_promo'] ?? false),
                'is_hit' => (bool)($row['is_hit'] ?? false),
                'is_vegan' => (bool)($row['is_vegan'] ?? false),
                'is_product_of_day' => (bool)($row['is_product_of_day'] ?? false),
            ];
        }
        $defaultPrice = (float)($product['price'] ?? 0);
        $defaultArticle = trim((string)($product['article'] ?? ''));

        $discountLabel = st('product.badges.discount', 'Знижка');
        $badgeLabels = [
            'is_spicy' => st('product.badges.is_spicy', 'Гострий'),
            'is_new' => st('product.badges.is_new', 'Новинка'),
            'is_promo' => st('product.badges.is_promo', 'Акція'),
            'is_hit' => st('product.badges.is_hit', 'Хіт'),
            'is_vegan' => st('product.badges.is_vegan', 'Веган'),
            'is_product_of_day' => st('product.badges.is_product_of_day', 'Пиріг дня'),
        ];

        // расчет начальной скидки для начального варианта
        $initialDiscount = null;
        if ($rootKey && isset($priceMap[$rootKey])) {
            if (($manualDiscountMap[$rootKey] ?? null) !== null) {
                $initialDiscount = round((float) $manualDiscountMap[$rootKey]);
            } else {
                $initialPriceData = $priceMap[$rootKey];
                $oldPrice = $initialPriceData['old'];
                $currentPrice = $initialPriceData['price'];
                if ($oldPrice !== null && $oldPrice > 0 && $currentPrice > 0 && $oldPrice > $currentPrice) {
                    $initialDiscount = round((($oldPrice - $currentPrice) / $oldPrice) * 100);
                }
            }
        }
    @endphp

    {{-- ИНИЦИАЛИЗАЦИЯ ОБЩЕГО STORE ДЛЯ ЦЕН --}}
    <div
        x-data
        x-init="
            Alpine.store('sku', {
                selected: '{{ $rootKey }}',
                prices: @js($priceMap),
                productIds: @js($productIdMap),
                metas: @js($metaMap),
                productKeys: @js($productKeyMap),
                articles: @js($articleMap),
                badges: @js($badgeMap),
                manualDiscounts: @js($manualDiscountMap),
                discountLabel: @js($discountLabel),
                badgeLabels: @js($badgeLabels),
                bonusPercent: {{ $bonusPercent ?? 0 }},
                minOrderSumForEarn: {{ $minOrderSumForEarn ?? 0 }},
                defaultArticle: @js($defaultArticle),
                fmt(v){ const n=Math.round(Number(v||0)); const parts=n.toFixed(2).split('.'); return { uah: parts[0].replace(/\B(?=(\d{3})+(?!\d))/g,' '), kop: parts[1] }; },
                price(){ const p=this.prices[this.selected]; return p?.price ?? {{ $defaultPrice }}; },
                old(){ const p=this.prices[this.selected]; return (p?.old && p.old > (p?.price ?? 0)) ? p.old : null; },
                selectedProductId(){
                    return Number(this.productIds[this.selected] ?? this.selected ?? 0);
                },
                selectedMeta(){
                    return this.metas[this.selected] ?? {};
                },
                sameVariant(item){
                    const productId = this.selectedProductId();
                    if (Number(item?.product_id ?? 0) !== productId) return false;
                    const selectedVolume = String(this.selectedMeta()?.volume ?? '').trim().toLowerCase();
                    if (!selectedVolume) return true;
                    const itemVolume = String(item?.meta?.volume ?? item?.meta?.cart_label ?? '').trim().toLowerCase();
                    return itemVolume === selectedVolume;
                },
                article(){
                    const value = this.articles[this.selected];
                    if (typeof value === 'string' && value.trim() !== '') return value;
                    return this.defaultArticle;
                },
                productKey(){
                    const value = this.productKeys[this.selected];
                    return typeof value === 'string' && value.trim() !== '' ? value : String(this.selected || '');
                },
                discountPercent(){
                    const manualDiscount = this.manualDiscounts[String(this.selected)] ?? null;
                    if (manualDiscount !== null && manualDiscount !== undefined && manualDiscount !== '') {
                        return Math.round(Number(manualDiscount));
                    }
                    const oldPrice = this.old();
                    const currentPrice = this.price();
                    if (oldPrice && oldPrice > 0 && currentPrice > 0 && oldPrice > currentPrice) {
                        return Math.round(((oldPrice - currentPrice) / oldPrice) * 100);
                    }
                    return null;
                },
                buildBadges(flags){
                    if (!flags) return [];

                    const items = [];
                    if (flags.is_spicy) {
                        items.push({ key: 'is_spicy', color: '#FF0013', textColor: '#FFFFFF', label: this.badgeLabels.is_spicy });
                    }
                    if (flags.is_new) {
                        items.push({ key: 'is_new', color: '#B91C1C', textColor: '#FFFFFF', label: this.badgeLabels.is_new });
                    }
                    if (flags.is_promo) {
                        items.push({ key: 'is_promo', color: '#FF7500', textColor: '#FFFFFF', label: this.badgeLabels.is_promo });
                    }
                    if (flags.is_hit) {
                        items.push({ key: 'is_hit', color: '#FFD700', textColor: '#19191A', label: this.badgeLabels.is_hit });
                    }
                    if (flags.is_vegan) {
                        items.push({ key: 'is_vegan', color: '#27AE60', textColor: '#FFFFFF', label: this.badgeLabels.is_vegan });
                    }
                    if (flags.is_product_of_day) {
                        items.push({ key: 'is_product_of_day', color: '#5D4037', textColor: '#FFFFFF', label: this.badgeLabels.is_product_of_day });
                    }
                    return items;
                },
                activeBadges(){
                    const flags = this.badges[String(this.selected)] ?? null;
                    return this.buildBadges(flags);
                },
                discountAmount(){
                    const oldPrice = this.old();
                    const currentPrice = this.price();
                    if (oldPrice && oldPrice > 0 && currentPrice > 0 && oldPrice > currentPrice) {
                        return oldPrice - currentPrice;
                    }
                    return 0;
                },
                bonusEarn(){
                    const currentPrice = this.price();
                    const discount = this.discountAmount();
                    const base = Math.max(currentPrice - discount, 0);
                    // Проверка минимальной суммы для начисления
                    if (this.minOrderSumForEarn > 0 && base < this.minOrderSumForEarn) {
                        return 0;
                    }
                    if (base <= 0 || this.bonusPercent <= 0) return 0;
                    // Рассчитываем бонусы (как в сервисе, округляем до 2 знаков)
                    const bonus = Math.round(base * this.bonusPercent / 100 * 100) / 100;
                    // Для отображения округляем до целого
                    return Math.round(bonus);
                },
            })
        "
    >

        <div class="mx-auto desk:w-[1198px] w-[357px] md:w-[736px] max-w-full">

            {{-- Хлебные крошки --}}
            <nav class="text-sm text-gray-500 my-4">
                <a href="{{ route('home') }}" class="hover:text-gray-700">{{ st('menu.home','Головна') }}</a>
                <span class="mx-2">→</span>
                <a href="{{ url('/' . ($category->slug ?? '')) }}" class="hover:text-gray-700">
                    {{ $category->title ?? $category->name ?? 'Категорія' }}
                </a>
                <span class="mx-2">→</span>
                <span class="text-gray-700">{{ $product['title'] }}</span>
            </nav>

            {{-- Кнопка "Назад" --}}
            <a href="javascript:history.back()"
               class="inline-flex items-center gap-2 text-[#333333] hover:text-[#FF7500] transition mb-4">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M15 18L9 12L15 6" stroke="#FF7500" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span class="font-intro font-bold text-[18px] leading-[22px]">{{ st('common.back', 'Назад') }}</span>
            </a>

            {{-- Верх: фото + инфо --}}
            <section class="grid grid-cols-1 xl:grid-cols-2 gap-6">
                {{-- Фото --}}
                <div>
                    <div class="relative w-full rounded-xl overflow-hidden bg-gray-50">
                        <div class="aspect-[4/3] lg:aspect-[16/12]">
                            <img src="{{ $product['main_image'] }}" alt="{{ $product['title'] }}"
                                 class="h-full w-full object-cover" loading="eager">
                        </div>
                        <div class="absolute right-[10px] top-[10px] z-10 flex flex-col items-end gap-1">
                            <span
                                x-show="$store.sku.discountPercent() !== null && $store.sku.discountPercent() > 0"
                                x-text="$store.sku.discountLabel + ' -' + $store.sku.discountPercent() + '%'"
                                x-cloak
                                class="rounded-[3px] bg-[#B91C1C] px-[10px] py-[4px] text-white font-intro font-bold text-[14px] leading-[16px]">
                            </span>

                            <template x-for="badge in $store.sku.activeBadges()" :key="badge.key">
                                <span
                                    x-text="badge.label"
                                    :style="`background:${badge.color};color:${badge.textColor};`"
                                    class="rounded-[3px] px-[10px] py-[4px] font-intro font-bold text-[14px] leading-[16px]"
                                ></span>
                            </template>
                        </div>
                    </div>
                </div>

                {{-- Информация / покупка --}}
                <div>
                    <h1 class="text-[#19191A] text-3xl md:text-4xl xl:text-[40px] leading-[100%] font-bold">
                        {{ $product['title'] }}
                    </h1>

                    {{-- ЦЕНА (теперь живёт тут, берётся из store) + КНОПКА --}}
                    <div class="mt-4 grid md:grid-cols-2 grid-cols-1 items-end gap-4">
                        <div>
                            {{-- старая цена --}}
                            <div class="text-[#9E9E9E] line-through" x-show="$store.sku.old()" x-cloak>
                                <span class="text-[22px] leading-[22px]" x-text="$store.sku.fmt($store.sku.old()).uah"></span>
                                <span class="text-[14px] leading-[14px] ml-1">грн</span>
                            </div>

                            {{-- текущая цена --}}
                            <div
                                class="font-semibold"
                                :class="$store.sku.old() ? 'text-[#DC2626]' : 'text-[#FF7500]'"
                            >
                                <span class="text-[28px] leading-[32px]" x-text="$store.sku.fmt($store.sku.price()).uah"></span>
                                <span class="text-sm font-medium ml-1">грн</span>
                            </div>
                        </div>

                        <div class="text-end" x-data="{
                            adding: false,
                            cartQty: 0,
                            async init() {
                                // Проверяем количество в корзине при инициализации
                                await this.checkCartQty();

                                // Слушаем обновления корзины
                                window.addEventListener('cart-updated', (e) => {
                                    if ($store.sku?.sameVariant(e?.detail?.item)) {
                                        this.cartQty = e.detail.item?.qty ?? 0;
                                    } else if (e?.detail?.items) {
                                        const item = e.detail.items.find(i => $store.sku?.sameVariant(i));
                                        if (item) {
                                            this.cartQty = item.qty ?? 0;
                                        }
                                    }
                                });

                                // Обновляем количество при смене варианта товара
                                $watch('$store.sku.selected', () => {
                                    this.checkCartQty();
                                });
                            },
                            async checkCartQty() {
                                try {
                                    const cache = window.__CART_CACHE__;
                                    let data;
                                    if (cache) {
                                        data = await cache.get();
                                    } else {
                                        const res = await fetch('{{ route('cart.info') }}', {
                                            headers: { 'Accept': 'application/json' }
                                        });
                                        data = await res.json();
                                    }
                                    const item = (data?.items ?? []).find(i => $store.sku?.sameVariant(i));
                                    this.cartQty = item?.qty ?? 0;
                                } catch (e) {
                                    this.cartQty = 0;
                                }
                            },
                            async addToCart() {
                                if (this.adding) return;
                                this.adding = true;

                                try {
                                    const pid = typeof $store.sku?.selectedProductId === 'function'
                                        ? $store.sku.selectedProductId()
                                        : '{{ $rootId ?? 0 }}';
                                    const price = typeof $store.sku?.price === 'function'
                                        ? Number($store.sku.price() || 0)
                                        : null;
                                    const data = await window.CartAPI.add('{{ route('cart.add') }}', {
                                        product_id: pid,
                                        qty: 1,
                                        price: price,
                                        meta: typeof $store.sku?.selectedMeta === 'function' ? $store.sku.selectedMeta() : {},
                                    });
                                    this.cartQty = data?.item?.qty ?? 1;
                                } catch (e) {
                                    console.error('Product page: CartAPI.add error', e);
                                    alert('Не вдалося додати до кошика');
                                } finally {
                                    this.adding = false;
                                }
                            },
                            async incrementQty() {
                                if (this.adding) return;
                                this.adding = true;

                                try {
                                    const pid = typeof $store.sku?.selectedProductId === 'function'
                                        ? $store.sku.selectedProductId()
                                        : '{{ $rootId ?? 0 }}';
                                    const price = typeof $store.sku?.price === 'function'
                                        ? Number($store.sku.price() || 0)
                                        : null;
                                    const data = await window.CartAPI.add('{{ route('cart.add') }}', {
                                        product_id: pid,
                                        qty: 1,
                                        price: price,
                                        meta: typeof $store.sku?.selectedMeta === 'function' ? $store.sku.selectedMeta() : {},
                                    });
                                    this.cartQty = data?.item?.qty ?? this.cartQty + 1;
                                } catch (e) {
                                    console.error('Product page: increment error', e);
                                    alert('Не вдалося оновити кількість');
                                } finally {
                                    this.adding = false;
                                }
                            },
                            async decrementQty() {
                                if (this.adding || this.cartQty <= 0) return;
                                this.adding = true;

                                try {
                                    const pid = typeof $store.sku?.selectedProductId === 'function'
                                        ? $store.sku.selectedProductId()
                                        : '{{ $rootId ?? 0 }}';
                                    const price = typeof $store.sku?.price === 'function'
                                        ? Number($store.sku.price() || 0)
                                        : null;
                                    const data = await window.CartAPI.add('{{ route('cart.add') }}', {
                                        product_id: pid,
                                        qty: -1,
                                        price: price,
                                        meta: typeof $store.sku?.selectedMeta === 'function' ? $store.sku.selectedMeta() : {},
                                    });
                                    this.cartQty = data?.item?.qty ?? Math.max(0, this.cartQty - 1);
                                } catch (e) {
                                    console.error('Product page: decrement error', e);
                                    alert('Не вдалося оновити кількість');
                                } finally {
                                    this.adding = false;
                                }
                            }
                        }">
                            {{-- Кнопка "Добавить в корзину" --}}
                            <button
                                x-show="cartQty === 0"
                                x-cloak
                                type="button"
                                class="inline-flex items-center gap-2 w-full md:w-[218px] justify-center bg-[#FF7500] hover:bg-orange-600 text-white font-semibold px-5 py-3 rounded-[4px] transition disabled:opacity-60"
                                :disabled="adding"
                                @click="addToCart"
                            >
                                <template x-if="!adding">
                                    <x-icons.cart class="h-5 w-5" />
                                </template>
                                <template x-if="adding">
                                    <svg class="h-5 w-5 animate-spin" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                              d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                    </svg>
                                </template>
                                {{ st('product.addcart','Додати в кошик') }}
                            </button>

                            {{-- Контролы количества --}}
                            <div
                                x-show="cartQty > 0"
                                x-cloak
                                class="w-full md:w-[218px] inline-flex items-center justify-between bg-[#FDDDA7] text-[#FF7500] h-10 rounded-[4px] px-1"
                            >
                                <button
                                    type="button"
                                    class="w-6 h-6 grid place-items-center text-xl leading-none"
                                    @click="decrementQty"
                                    :disabled="adding || cartQty <= 0"
                                    :aria-label="cartQty === 1 ? 'Видалити з кошика' : 'Зменшити кількість'"
                                >
                                    <svg x-show="cartQty > 1" class="w-4 h-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M5 12H19" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
                                    </svg>

                                    <svg x-show="cartQty === 1" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6h14zM14 11v6M10 11v6M3 6h18M8 6V4a2 2 0 012-2h4a2 2 0 012 2v2" stroke="currentColor" stroke-width="2" stroke-linecap="square"/>
                                    </svg>
                                </button>
                                <div class="w-8 text-center font-semibold" x-text="cartQty">1</div>
                                <button
                                    type="button"
                                    class="w-6 h-6 grid place-items-center text-xl leading-none"
                                    @click="incrementQty"
                                    :disabled="adding"
                                    aria-label="Збільшити кількість"
                                >
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M5 12H19" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
                                        <path d="M12 5V19" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>


                    {{-- бонусы отключены по задаче, вместо них показываем артикул --}}
                    <div class="mt-4 flex items-center gap-1 text-[#C04103] text-base">
                        <span class="whitespace-nowrap">{{ st('product.sku_label', 'Артикул') }}: <span class="text-[#C04103] font-semibold" x-text="$store.sku.article() || '123'">{{ $defaultArticle !== '' ? $defaultArticle : '123' }}</span></span>
                    </div>

                    {{-- Состав --}}
                    @php
                        $ingredientsSource = trim((string) ($product['ingredients_text'] ?? ''));

                        if ($ingredientsSource === '') {
                            $ingredientsSource = trim((string) ($product['short_desc'] ?? ''));
                        }

                        if ($ingredientsSource === '') {
                            $ingredientsSource = (string) ($product['description'] ?? '');
                        }

                        $ingredientsText = preg_replace('/\s+/u', ' ', strip_tags($ingredientsSource));
                        $ingredientsText = trim((string) $ingredientsText);

                    @endphp
                    @if($ingredientsText !== '')
                        <div class="mt-6">
                            <div class="text-[#666666] text-lg font-semibold mb-2">{{ st('product.ingredients','Склад') }}:</div>
                            <div class="prose prose-sm max-w-none text-base text-[#A9A9A9]">
                                {{ $ingredientsText }}
                            </div>
                        </div>
                    @endif

                    {{-- Кнопки-«пилюли» (варианты). Компонент БЕЗ цен, только переключает $store.sku.selected --}}
                    @if(!empty($rows))
                        <x-ui.rows-selector-detail
                            :rows="$rows"
                            :characteristics="$characteristics"
                            :root-id="$rootId"
                            store="sku"
                            :init-store="false"
                        />
                    @endif

                    <div
                        x-data="{
                            open: false,
                            popup: { top: 16, left: 16 },
                            payment() { return Number($store.sku?.price?.() || 0) / 3 },
                            format(value) { return Math.round(Number(value || 0)).toLocaleString('uk-UA') },
                            openInfo(event) {
                                const trigger = event.currentTarget.getBoundingClientRect();
                                const width = Math.min(320, window.innerWidth - 32);
                                const height = 285;
                                const left = Math.max(16, Math.min(trigger.right - width, window.innerWidth - width - 16));
                                const top = trigger.bottom + 8 + height <= window.innerHeight ? trigger.bottom + 8 : Math.max(16, trigger.top - height - 8);
                                this.popup = { top, left };
                                this.open = true;
                            },
                        }"
                        class="mt-4"
                    >
                        <button type="button" class="flex w-full items-center gap-2 rounded-lg bg-[#FFF1EB] px-3 py-2 text-left text-sm text-[#7A3418] transition hover:bg-[#FFE4D9]" @click="openInfo($event)" :aria-expanded="open" aria-haspopup="dialog">
                            <svg class="h-5 w-5 shrink-0 text-[#FF7500]" viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2" stroke="currentColor" stroke-width="2"/><path d="M3 10H21M7 15H10" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                            <span>{{ st('product.installment.prefix', 'Від') }}</span>
                            <strong><span x-text="format(payment())"></span> {{ st('cart.summary.currency_short', 'грн') }} × 3 {{ st('product.installment.dialog_payments', 'платежі') }}</strong>
                            <svg class="ml-auto h-4 w-4 shrink-0 text-[#FF7500]" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M9 5l7 7-7 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </button>

                        <div class="mt-2 flex items-center justify-center gap-5 text-xs leading-3 text-[#777]">
                            <div class="flex items-center gap-2"><span class="flex h-8 w-8 items-center justify-center rounded-full bg-[#242938] text-xs font-semibold text-white">M</span><span><strong class="block text-[#242938]">monobank</strong>{{ st('product.installment.monobank', 'Покупка частинами') }}</span></div>
                            <div class="flex items-center gap-2"><svg class="h-8 w-8" viewBox="0 0 40 40" fill="none" aria-hidden="true"><path d="M32.7059 32H20.6827C20.6827 31.029 20.6922 30.0756 20.6803 29.1211C20.6637 27.7979 20.4866 26.4969 20.0541 25.2395C19.075 22.3911 17.0156 20.7497 14.1114 20.0511C12.5999 19.6871 11.0611 19.6636 9.51867 19.6707C9.0279 19.673 8.53713 19.6707 8.03448 19.6707V8H32.7059V32ZM27.5986 27.0406V12.9771H13.1525V14.8521C20.3809 15.8595 24.6136 19.8421 25.6557 27.0417H27.5998L27.5986 27.0406Z" fill="#76AE42"/><path d="M8 31.9905V22.6246H17.649V31.9905H8Z" fill="black"/></svg><span><strong class="block text-[#242938]">ПриватБанк</strong>{{ st('product.installment.privatbank', 'Оплата частинами') }}</span></div>
                        </div>

                        <div x-show="open" x-cloak x-transition.opacity @keydown.escape.window="open = false" class="fixed inset-0" style="position: fixed; inset: 0; z-index: 100;" role="dialog" aria-modal="true" aria-label="{{ st('product.installment.dialog_title', 'Купити частинами') }}" @click.self="open = false">
                            <div x-transition.scale.origin.center class="relative rounded-2xl bg-white p-5 shadow-2xl" :style="`position: fixed; width: min(320px, calc(100vw - 32px)); max-width: calc(100vw - 32px); top: ${popup.top}px; left: ${popup.left}px;`">
                                <button type="button" class="absolute right-3 top-3 flex h-7 w-7 items-center justify-center rounded-full text-lg text-[#777] hover:bg-gray-100" @click="open = false" aria-label="{{ st('all.close', 'Закрити') }}">×</button>
                                <h3 class="pr-7 text-[18px] font-bold text-[#242938]">{{ st('product.installment.dialog_title', 'Купити частинами') }}</h3>
                                <div class="mt-4 space-y-3">
                                    <div class="flex items-center gap-3 rounded-xl bg-[#F7F7F7] p-3"><span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-[#242938] text-sm font-semibold text-white">M</span><div><strong class="block text-sm text-[#242938]">monobank</strong><span class="text-xs text-[#666]"><span x-text="format(payment())"></span> {{ st('cart.summary.currency_short', 'грн') }} × 3</span><span class="block text-xs text-[#777]">{{ st('product.installment.monobank', 'Покупка частинами') }}</span></div></div>
                                    <div class="flex items-center gap-3 rounded-xl bg-[#F7F7F7] p-3"><svg class="h-10 w-10 shrink-0" viewBox="0 0 40 40" fill="none" aria-hidden="true"><path d="M32.7059 32H20.6827C20.6827 31.029 20.6922 30.0756 20.6803 29.1211C20.6637 27.7979 20.4866 26.4969 20.0541 25.2395C19.075 22.3911 17.0156 20.7497 14.1114 20.0511C12.5999 19.6871 11.0611 19.6636 9.51867 19.6707C9.0279 19.673 8.53713 19.6707 8.03448 19.6707V8H32.7059V32ZM27.5986 27.0406V12.9771H13.1525V14.8521C20.3809 15.8595 24.6136 19.8421 25.6557 27.0417H27.5998L27.5986 27.0406Z" fill="#76AE42"/><path d="M8 31.9905V22.6246H17.649V31.9905H8Z" fill="black"/></svg><div><strong class="block text-sm text-[#242938]">ПриватБанк</strong><span class="text-xs text-[#666]"><span x-text="format(payment())"></span> {{ st('cart.summary.currency_short', 'грн') }} × 3</span><span class="block text-xs text-[#777]">{{ st('product.installment.privatbank', 'Оплата частинами') }}</span></div></div>
                                </div>
                                <p class="mt-4 text-center text-xs leading-4 text-[#777]">{{ st('product.installment.dialog_hint', 'Оформити оплату частинами можна під час оформлення замовлення.') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>


        {{-- Рекомендации --}}
            <x-pages.catalog.partials.recommendations
                :title="st('product.recommend','Рекомендуємо спробувати')"
                :products="$related"
            />
        </div>
    @include(front_view('pages.catalog.reviews'), ['product' => $rootId])

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const trackCurrentProduct = () => {
                    const skuStore = window.Alpine && typeof window.Alpine.store === 'function'
                        ? window.Alpine.store('sku')
                        : null;
                    if (!skuStore) {
                        return;
                    }

                    window.eSputnikTrackProductPage({
                        product_key: typeof skuStore.productKey === 'function' ? skuStore.productKey() : @js($product['product_key'] ?? $rootId),
                        price: typeof skuStore.price === 'function' ? skuStore.price() : {{ (float) ($product['price'] ?? 0) }},
                        isInStock: {{ !empty($product['in_stock']) ? 1 : 0 }},
                    });
                };

                trackCurrentProduct();

                window.addEventListener('variant-selected', () => {
                    trackCurrentProduct();
                });
            });
        </script>
    @endpush
@endsection
