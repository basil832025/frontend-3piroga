<div data-cart-recommendations data-cart-recommendations-url="{{ $recommendationsUrl }}">
    <x-pages.catalog.partials.recommendations
        :title="st('cart.forgot_anything', 'Нічого не забули?')"
        :products="$products ?? []"
    />
</div>
