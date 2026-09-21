@php
    use Illuminate\Support\Facades\Route;

    $locale = app()->getLocale();
    $isLocalized = in_array($locale, ['ru', 'en'], true);

    $checkoutUrl = $isLocalized
        ? (Route::has('localized.checkout')
            ? route('localized.checkout', ['locale' => $locale])
            : route('localized.cart.page', ['locale' => $locale]))
        : (Route::has('checkout') ? route('checkout') : (Route::has('cart.page') ? route('cart.page') : url('/cart')));

    $cartUrl = $isLocalized
        ? (Route::has('localized.cart.page')
            ? route('localized.cart.page', ['locale' => $locale])
            : url('/' . $locale . '/cart'))
        : (Route::has('cart.page') ? route('cart.page') : url('/cart'));

    $authShowUrl = $isLocalized
        ? (Route::has('localized.auth.show')
            ? route('localized.auth.show', ['locale' => $locale])
            : url('/' . $locale . '/auth'))
        : route('auth.show');

    $saveCheckoutUrl = $isLocalized
        ? (Route::has('localized.auth.save-checkout-url')
            ? route('localized.auth.save-checkout-url', ['locale' => $locale])
            : url('/' . $locale . '/auth/save-checkout-url'))
        : route('auth.save-checkout-url');

    $addUrl    = Route::has('cart.add')    ? route('cart.add')    : url('/cart/add');
    $removeUrl = Route::has('cart.remove') ? route('cart.remove') : url('/cart/remove');
@endphp

@if (empty($items))
    <div data-cart-empty class="p-6 text-center text-gray-500">
        {{ st('cart.empty', 'Кошик порожній') }}

    </div>
@else
    <div x-data="cartActions('{{ $addUrl }}','{{ $removeUrl }}')">
        <div data-cart-list>
            @foreach ($items as $it)
                @php
                    $pid   = (int) data_get($it, 'product_id');
                    $img   = data_get($it, 'image', asset('vendor/frontend-3piroga/images/noimg.png'));
                    $name  = data_get($it, 'name', st('cart.item.default_name', 'Товар'));
                    $sku   = data_get($it, 'sku');
                    $code2 = data_get($it, 'code2');
                    $article = $sku ?: $code2;
                    $var   = data_get($it, 'variant');
                    $q     = (int) data_get($it, 'qty', 1);
                    $p     = (float) data_get($it, 'price', 0);
                    $sum   = (float) (data_get($it, 'subtotal') ?? ($q * $p));
                    $oldUnitPrice = (float) data_get($it, 'old_price', 0);
                    $oldSum = (float) data_get($it, 'old_subtotal', 0);

                    if ($oldUnitPrice > 0 && $oldUnitPrice <= $p) {
                        $oldUnitPrice = 0;
                    }

                    if ($oldUnitPrice > 0 && $oldSum <= 0) {
                        $oldSum = $oldUnitPrice * $q;
                    }
                    
                    // Получаем характеристики с SVG иконками для размера и веса
                    $variantChars = collect(data_get($it, 'variant_chars', []))
                        ->filter(fn ($char) => is_array($char) && trim((string) ($char['value'] ?? '')) !== '')
                        ->values()
                        ->all();

                    if (empty($variantChars) && $pid) {
                        $product = \App\Models\Shop\Product::with([
                            'productCharacteristicValues.characteristic:id,slug,svg_image_id',
                            'productCharacteristicValues.characteristic.svgImage',
                            'productCharacteristicValues.characteristicValue'
                        ])->find($pid);
                        if ($product && $product->relationLoaded('productCharacteristicValues')) {
                            $vals = $product->productCharacteristicValues;
                            $keep = ['rozmir-pirogiv', 'rozmiri-insi', 'vaga', 'vaga-grami', 'vaga-setiv', 'obiem', 'obyem', 'volume', 'ml'];
                            foreach ($vals as $v) {
                                $char = $v->characteristic;
                                if (!$char) continue;
                                
                                $slug = (string) ($char->slug ?? '');
                                if ($slug === '' || !in_array($slug, $keep, true)) {
                                    continue;
                                }
                                $text = $v->value_text ?: ($v->characteristicValue?->value ?? null);
                                if ($text) {
                                    $svgUrl = $char->svgImage?->url ?? null;
                                    $variantChars[] = [
                                        'slug' => $slug,
                                        'value' => $text,
                                        'svg' => $svgUrl,
                                    ];
                                }
                            }
                        }
                    }
                @endphp

                <div
                    class="max-w-[753px] bg-white rounded-xl shadow-[0_8px_20px_rgba(0,0,0,0.05)] mb-4 p-4 sm:p-6 flex flex-wrap sm:flex-nowrap items-center justify-between gap-4"
                    data-cart-item="{{ $pid }}"
                >
                    {{-- Левая часть --}}
                    <div class="flex items-start gap-4 w-full sm:w-auto">
                        <img src="{{ $img }}" alt="" class="w-[100px] h-[80px] sm:w-[127px] sm:h-[102px] rounded-lg object-cover">
                        <div class="flex flex-col justify-between min-w-0">
                            <div class="text-[14px] leading-[16px] font-bold text-[#19191A]
                                [display:-webkit-box] [-webkit-line-clamp:2] [-webkit-box-orient:vertical] overflow-hidden">
                                {{ $name }}
                            </div>

                            @if($article)
                                <div class="text-[13px] leading-[16px] text-[#C04103] mt-1">
                                    {{ st('cart.item.sku_label', 'Артикул:') }} {{ $article }}
                                </div>
                            @endif

                            @if(!empty($variantChars))
                                <div class="flex flex-row md:flex-col items-center md:items-start gap-2 mt-1 text-xs text-gray-500">
                                    @foreach($variantChars as $char)
                                        <span class="inline-flex items-center gap-1">
                                    @if($char['svg'] ?? null)
                                        <img src="{{ $char['svg'] }}" alt="" aria-hidden="true" class="h-4 w-4 shrink-0 object-contain opacity-60">
                                            @endif
                                            <span>{{ $char['value'] }}</span>
                                        </span>
                                    @endforeach
                                </div>
                            @elseif($var)
                                <div class="text-xs text-gray-500 mt-1">{{ $var }}</div>
                            @endif
                        </div>
                    </div>

                    {{-- Правая часть --}}
                    <div class="w-full sm:w-auto flex items-center sm:items-center gap-4 sm:gap-6
                        justify-between sm:justify-end">
                        {{-- количество --}}
                        <div class="flex items-center gap-2 order-1">
                            <button
                                class="w-6 h-6 flex items-center justify-center rounded-full bg-[#FF7500] text-white text-[16px] leading-none"
                                @click="dec({{ $pid }})"
                                aria-label="Зменшити кількість"
                            >
                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M5 12H19" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
                                </svg>
                            </button>

                            <input
                                class="w-14 h-10 text-center border border-[#FF7500] rounded text-[16px] font-medium outline-none bg-white"
                                type="text" inputmode="numeric" pattern="\d*"
                                value="{{ $q }}"
                                data-cart-qty-input
                                @input.debounce.350ms="onQtyInput({{ $pid }}, $event.target)"
                                @blur="onQtyBlur({{ $pid }}, $event.target)"
                                @keydown.enter.prevent="$event.target.blur()"
                            />

                            <button
                                class="w-6 h-6 flex items-center justify-center rounded-full bg-[#FF7500] text-white text-[16px] leading-none"
                                @click="inc({{ $pid }})"
                                aria-label="Збільшити кількість"
                            >
                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M5 12H19" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
                                    <path d="M12 5V19" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
                                </svg>
                            </button>
                        </div>

                        {{-- сумма --}}
                        <div class="order-2 ml-auto sm:ml-0 min-w-[100px] md:min-w-[120px] shrink-0 flex flex-col items-end text-right">
                            <div class="flex items-baseline justify-end gap-1 text-[#DC2626] font-bold whitespace-nowrap">
                                <span class="text-[18px]" data-cart-line-total>
                                    {{ number_format($sum, 0, ',', ' ') }}
                                </span>
                                <span class="text-[14px]">
                                    {{ st('cart.summary.currency_short', 'грн') }}
                                </span>
                            </div>
                            <div
                                data-cart-line-old-total
                                @class(['mt-1 w-full text-[14px] text-[#9E9E9E] line-through tabular-nums text-right', 'hidden' => $oldSum <= $sum])
                            >
                                @if($oldSum > $sum)
                                    {{ number_format($oldSum, 0, ',', ' ') }} {{ st('cart.summary.currency_short', 'грн') }}
                                @endif
                            </div>
                        </div>

                        {{-- удалить --}}
                        <button
                            class="order-3 w-6 h-6 flex items-center justify-center rounded hover:bg-gray-100"
                            title="{{ st('cart.item.delete', 'Видалити') }}"
                            @click.prevent="del({{ $pid }}, $event)"
                        >
                            {{-- svg как есть --}}
                            <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect x="0.5" y="0.5" width="31" height="31" rx="3.5" fill="white"/>
                                <rect x="0.5" y="0.5" width="31" height="31" rx="3.5" stroke="#E5E7EB"/>
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M9.29289 9.29289C9.68342 8.90237 10.3166 8.90237 10.7071 9.29289L16 14.5858L21.2929 9.29289C21.6834 8.90237 22.3166 8.90237 22.7071 9.29289C23.0976 9.68342 23.0976 10.3166 22.7071 10.7071L17.4142 16L22.7071 21.2929C23.0976 21.6834 23.0976 22.3166 22.7071 22.7071C22.3166 23.0976 21.6834 23.0976 21.2929 22.7071L16 17.4142L10.7071 22.7071C10.3166 23.0976 9.68342 23.0976 9.29289 22.7071C8.90237 22.3166 8.90237 21.6834 9.29289 21.2929L14.5858 16L9.29289 10.7071C8.90237 10.3166 8.90237 9.68342 9.29289 9.29289Z" fill="#929292"/>
                            </svg>
                        </button>
                    </div>
                </div>

            @endforeach
        </div>
    </div>

    <div
        x-data="{
            total: @js((float) ($total ?? 0)),
            payment() { return this.total / 3 },
            format(value) { return Math.round(Number(value || 0)).toLocaleString('uk-UA') },
        }"
        x-init="window.addEventListener('cart-updated', (event) => { const data = event.detail || {}; if ('total_price' in data || 'total' in data) total = Number(data.total_price ?? data.total ?? 0); })"
        class="mx-4 mb-4 rounded-xl bg-[#EFFAF1] p-3 text-[#175C2A]"
    >
        <div class="flex items-start gap-2">
            <svg class="mt-0.5 h-7 w-7 shrink-0 text-[#3D9B58]" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <rect x="3" y="5" width="18" height="14" rx="2" stroke="currentColor" stroke-width="2"/>
                <path d="M3 10H21M7 15H10" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
            <div>
                <div class="font-bold leading-5">{{ st('cart.installment.title', 'Можна оплатити частинами!') }}</div>
                <div class="text-xs leading-4 text-[#4E6D56]">{{ st('cart.installment.plan', '3 місяці — по') }} <strong><span x-text="format(payment())"></span> {{ st('cart.summary.currency_short', 'грн') }}</strong></div>
            </div>
        </div>

        <div class="mt-3 grid grid-cols-2 gap-2">
            <div class="flex min-w-0 items-center gap-2 rounded-lg bg-white p-2">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-[#242938] text-xs font-semibold text-white">M</span>
                <div class="min-w-0 text-[10px] leading-3 text-[#4B5563]">
                    <strong class="block truncate text-[12px] text-[#242938]">monobank</strong>
                    <span class="block">{{ st('product.installment.monobank', 'Покупка частинами') }}</span>
                    <span class="block">{{ st('product.installment.prefix', 'Від') }} <strong><span x-text="format(payment())"></span> {{ st('cart.summary.currency_short', 'грн') }} × 3</strong></span>
                </div>
            </div>
            <div class="flex min-w-0 items-center gap-2 rounded-lg bg-white p-2">
                <svg class="h-9 w-9 shrink-0" viewBox="0 0 40 40" fill="none" aria-hidden="true"><path d="M32.7059 32H20.6827C20.6827 31.029 20.6922 30.0756 20.6803 29.1211C20.6637 27.7979 20.4866 26.4969 20.0541 25.2395C19.075 22.3911 17.0156 20.7497 14.1114 20.0511C12.5999 19.6871 11.0611 19.6636 9.51867 19.6707C9.0279 19.673 8.53713 19.6707 8.03448 19.6707V8H32.7059V32ZM27.5986 27.0406V12.9771H13.1525V14.8521C20.3809 15.8595 24.6136 19.8421 25.6557 27.0417H27.5998L27.5986 27.0406Z" fill="#76AE42"/><path d="M8 31.9905V22.6246H17.649V31.9905H8Z" fill="black"/></svg>
                <div class="min-w-0 text-[10px] leading-3 text-[#4B5563]">
                    <strong class="block truncate text-[12px] text-[#242938]">ПриватБанк</strong>
                    <span class="block">{{ st('product.installment.privatbank', 'Оплата частинами') }}</span>
                    <span class="block">{{ st('product.installment.prefix', 'Від') }} <strong><span x-text="format(payment())"></span> {{ st('cart.summary.currency_short', 'грн') }} × 3</strong></span>
                </div>
            </div>
        </div>
    </div>

    <div class="p-4 border-t flex items-center justify-between text-[#19191A] text-2xl font-bold">
        <div>{{ st('cart.summary.total_to_pay', 'До сплати') }}</div>
        <div data-cart-total>
            {{ number_format($total ?? 0, 0, ',', ' ') }}
            {{ st('cart.summary.currency_short', 'грн') }}
        </div>
    </div>

    <div class="p-4 pt-0">
        @auth
            <a href="{{ $checkoutUrl }}"
               class="block w-full text-center bg-orange-500 text-white py-2 rounded-lg hover:bg-orange-600 transition">
                {{ st('cart.actions.checkout', 'Оформити замовлення') }}
            </a>
        @else
            <button type="button"
                    x-data
                    @click.prevent="
                        const checkoutUrl = '{{ $checkoutUrl }}';
                        fetch('{{ $saveCheckoutUrl }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name=&quot;csrf-token&quot;]')?.getAttribute('content') || '',
                                'Accept': 'application/json',
                            },
                            credentials: 'same-origin',
                            body: JSON.stringify({ url: checkoutUrl }),
                        })
                        .then(() => {
                            window.location.href = '{{ $authShowUrl }}?redirect_to_checkout=1';
                        })
                        .catch(() => {
                            // Если запрос не удался, все равно редиректим, но с параметром
                            window.location.href = '{{ $authShowUrl }}?redirect_to_checkout=1';
                        });
                    "
                    class="block w-full text-center bg-orange-500 text-white py-2 rounded-lg hover:bg-orange-600 transition">
                {{ st('cart.actions.checkout', 'Оформити замовлення') }}
            </button>
        @endauth
        <a href="{{ $cartUrl }}" class="block w-full text-center text-gray-600 mt-2 underline hover:no-underline">
            {{ st('cart.actions.goto_cart', 'Перейти в кошик') }}
        </a>
    </div>
@endif
