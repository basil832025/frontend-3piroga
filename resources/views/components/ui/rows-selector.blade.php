@props([
'rows'            => [],
'characteristics' => [],          // [{id, slug, title, svg, sort}]
'rootId'          => null,
'defaultPrice'    => 0,
'defaultOldPrice' => null,
'cartText'        => 'Додати в кошик',
'personSlug'      => 'persons',
])

@php
    static $rowsSelectorLabels = null;
    static $paypartsBankTerms = null;

    if ($rowsSelectorLabels === null) {
        $rowsSelectorLabels = [
        'currency' => st('all.grn', 'грн'),
        'installment_prefix' => st('product.installment.prefix', 'Від'),
        'installment_term' => st('product.installment.term', 'грн × 3 платежі'),
        'mono_label' => st('product.installment.monobank', 'Покупка частинами'),
        'privat_label' => st('product.installment.privatbank', 'Оплата частинами'),
        'per_month_short' => st('product.installment.per_month_short', 'міс.'),
        ];
    }

    // Terms are maintained in the PaypartsBank records in the admin panel.
    // Load them once per request so every product card opens the same current text.
    if ($paypartsBankTerms === null) {
        $paypartsBankTerms = collect();
        try {
            $paypartsBankTerms = \App\Models\Shop\PaypartsBank::query()
                ->active()
                ->visibleForClient(auth()->user())
                ->orderBy('id')
                ->get()
                ->mapWithKeys(function (\App\Models\Shop\PaypartsBank $bank): array {
                    $type = (string) ($bank->bank_type ?? '');
                    return [$type => [
                        'name' => $bank->localizedText('name', app()->getLocale(), $bank->bankType()?->label() ?? $type),
                        'terms' => (string) ($bank->localizedText('terms', app()->getLocale()) ?? ''),
                    ]];
                });
        } catch (\Throwable $e) {
            // Keep the catalog renderable if payparts configuration is unavailable.
            $paypartsBankTerms = collect();
        }
    }

    $fmt = function ($val) {
        [$uah, $kop] = explode(',', number_format((float)$val, 2, ',', ' '));
        return ['uah' => $uah, 'kop' => $kop];
    };

    $rootId   = $rootId ?? ($rows[0]['product_id'] ?? null);
    $selectedId = $rows[0]['variant_key'] ?? $rows[0]['product_id'] ?? $rootId;
    $rootKey  = $selectedId !== null ? (string)$selectedId : '';
    $rootRow  = null;
    if ($rootKey !== '') foreach ($rows as $r) if ((string)($r['variant_key'] ?? $r['product_id'] ?? '') === $rootKey) { $rootRow = $r; break; }
    $rootRow ??= $rows[0] ?? ['price'=>$defaultPrice,'old_price'=>$defaultOldPrice,'product_id'=>null];

    // Для одного варианта уменьшаем отступы
    $rowsCount = count($rows ?? []);
    $isSingleVariant = $rowsCount <= 1;

    $p  = $fmt($rootRow['price'] ?? $defaultPrice);
    $op = ($rootRow['old_price'] ?? null) && ($rootRow['old_price'] > ($rootRow['price'] ?? 0)) ? $fmt($rootRow['old_price']) : null;

    $priceMap = [];
    $productIdMap = [];
    $metaMap = [];
    foreach ($rows as $row) {
        $rowKey = (string) ($row['variant_key'] ?? $row['product_id'] ?? '');
        $priceMap[$rowKey] = [
            'price' => (float)($row['price'] ?? 0),
            'old'   => isset($row['old_price']) ? (float)$row['old_price'] : null,
        ];
        $productIdMap[$rowKey] = (int) ($row['cart_product_id'] ?? $row['product_id'] ?? 0);
        $metaMap[$rowKey] = is_array($row['cart_meta'] ?? null) ? $row['cart_meta'] : [];
    }

    // ********** ВАЖНЫЙ ПАТЧ: persons ВСЕГДА СПРАВА **********
    $chars = collect($characteristics)->sortBy('sort')->values();

    $personsIdx = $chars->search(fn($c) => ($c['slug'] ?? null) === $personSlug);
    if ($personsIdx !== false) {
        $rightChar = $chars[$personsIdx];
        // слева берём любые другие 1–2 характеристики, исключая persons
        $leftChars = $chars->filter(fn($c, $i) => $i !== $personsIdx)->take(2)->values();
    } else {
        // как раньше: 1–2 слева, 3-я справа (если persons нет в наборе)
        $leftChars = $chars->slice(0, 2)->values();
        $rightChar = $chars->get(2);
    }
    // *********************************************************
@endphp

@if(!empty($rows))
    <div
        x-data="{
        selected: @js($rootKey),
        prices:   @js($priceMap),
        productIds: @js($productIdMap),
        metas: @js($metaMap),

        fmt(v){
            const n = Math.round(Number(v||0));
            const parts = n.toFixed(2).split('.');
            return { uah: parts[0].replace(/\B(?=(\d{3})+(?!\d))/g,' '), kop: parts[1] };
        },

        installmentPayment() {
            return Number(this.prices[this.selected]?.price ?? 0) / 3;
        },

        showInstallmentInfo: false,
        showBankTerms: false,
        bankTerms: { name: '', html: '' },
        openBankTerms(bankType) {
            const bank = @js($paypartsBankTerms->all());
            const fallback = bankType === 'monobank'
                ? { name: 'monobank', html: '' }
                : { name: 'ПриватБанк', html: '' };
            this.bankTerms = bank[bankType] && bank[bankType].terms
                ? { name: bank[bankType].name || fallback.name, html: bank[bankType].terms }
                : fallback;
            this.showBankTerms = true;
        },
        installmentPopup: { top: 16, left: 16 },
        openInstallmentInfo(event) {
            const trigger = event.currentTarget.getBoundingClientRect();
            const popupWidth = Math.min(320, window.innerWidth - 32);
            const popupHeight = 285;
            const left = Math.max(16, Math.min(trigger.right - popupWidth, window.innerWidth - popupWidth - 16));
            const top = trigger.bottom + 8 + popupHeight <= window.innerHeight
                ? trigger.bottom + 8
                : Math.max(16, trigger.top - popupHeight - 8);

            this.installmentPopup = { top, left };
            this.showInstallmentInfo = true;
        },
        adding: false,
        cartQty: 0,

        selectedProductId() {
            return Number(this.productIds[this.selected] ?? this.selected ?? 0);
        },

        selectedMeta() {
            return this.metas[this.selected] ?? {};
        },

        sameVariant(item) {
            const productId = this.selectedProductId();
            if (Number(item?.product_id ?? 0) !== productId) return false;
            const selectedVolume = String(this.selectedMeta()?.volume ?? '').trim().toLowerCase();
            if (!selectedVolume) return true;
            const itemVolume = String(item?.meta?.volume ?? item?.meta?.cart_label ?? '').trim().toLowerCase();
            return itemVolume === selectedVolume;
        },

        init() {
            this.$watch('selected', (newVal) => {
                const detail = {
                    productId: newVal,
                    price: this.prices[newVal]?.price ?? null,
                    oldPrice: this.prices[newVal]?.old ?? null,
                };

                const parentCard = this.$el.closest('article[x-data]');
                if (parentCard && window.Alpine) {
                    const cardData = window.Alpine.$data(parentCard);
                    if (cardData && cardData.handleVariantSelected) {
                        cardData.handleVariantSelected({ detail });
                    }
                }

                this.$dispatch('variant-selected', detail);

                this.checkCartQty();
            });

            this.$nextTick(() => {
                const detail = {
                    productId: this.selected,
                    price: this.prices[this.selected]?.price ?? null,
                    oldPrice: this.prices[this.selected]?.old ?? null,
                };

                const parentCard = this.$el.closest('article[x-data]');
                if (parentCard && window.Alpine) {
                    const cardData = window.Alpine.$data(parentCard);
                    if (cardData && cardData.handleVariantSelected) {
                        cardData.handleVariantSelected({ detail });
                    }
                }

                this.$dispatch('variant-selected', detail);

                setTimeout(() => {
                    this.checkCartQty();
                }, Math.random() * 50 + 10);
            });

            window.addEventListener('cart-updated', (e) => {
                if (this.sameVariant(e?.detail?.item)) {
                    this.cartQty = e.detail.item?.qty ?? 0;
                } else if (e?.detail?.items) {
                    const item = e.detail.items.find(i => this.sameVariant(i));
                    if (item) {
                        this.cartQty = item.qty ?? 0;
                    }
                }
            });
        },

        async checkCartQty() {
            try {
                const cache = window.__CART_CACHE__;
                if (!cache) {
                    const res = await fetch('{{ route('cart.info') }}', {
                        headers: { 'Accept': 'application/json' }
                    });
                    const data = await res.json();
                    const item = (data?.items ?? []).find(i => this.sameVariant(i));
                    this.cartQty = item?.qty ?? 0;
                    return;
                }

                const data = await cache.get();
                const item = (data?.items ?? []).find(i => this.sameVariant(i));
                this.cartQty = item?.qty ?? 0;
            } catch (e) {
                this.cartQty = 0;
            }
        },

        async addToCart() {
            if (this.adding) return;
            this.adding = true;

            try {
                const data = await window.CartAPI.add('{{ route('cart.add') }}', {
                    product_id: this.selectedProductId(),
                    qty: 1,
                    price: this.prices[this.selected]?.price ?? null,
                    meta: this.selectedMeta(),
                });

                this.cartQty = data?.item?.qty ?? 1;

                this.$dispatch('notify', { text: 'Додано до кошика', type: 'success' });

            } catch (e) {
                console.error(e);
                alert('Не вдалося додати до кошика');
            } finally {
                this.adding = false;
            }
        },

        async incrementQty() {
            if (this.adding) return;
            this.adding = true;

            try {
                const data = await window.CartAPI.add('{{ route('cart.add') }}', {
                    product_id: this.selectedProductId(),
                    qty: 1,
                    price: this.prices[this.selected]?.price ?? null,
                    meta: this.selectedMeta(),
                });

                this.cartQty = data?.item?.qty ?? this.cartQty + 1;

            } catch (e) {
                console.error(e);
                alert('Не вдалося оновити кількість');
            } finally {
                this.adding = false;
            }
        },

        async decrementQty() {
            if (this.adding || this.cartQty <= 0) return;
            this.adding = true;

            try {
                const data = await window.CartAPI.add('{{ route('cart.add') }}', {
                    product_id: this.selectedProductId(),
                    qty: -1,
                    price: this.prices[this.selected]?.price ?? null,
                    meta: this.selectedMeta(),
                });

                this.cartQty = data?.item?.qty ?? Math.max(0, this.cartQty - 1);

            } catch (e) {
                console.error(e);
                alert('Не вдалося оновити кількість');
            } finally {
                this.adding = false;
            }
        },
    }"
        class="{{ $isSingleVariant ? 'mt-1' : 'mt-1' }} text-[13px] flex flex-col"
    >

        <div class="flex flex-col gap-2">
        @foreach ($rows as $r)
            @php
                $rowValue = (string) ($r['variant_key'] ?? $r['product_id'] ?? '');
            @endphp
            <button
                type="button"
                x-on:click="selected = '{{ $rowValue }}'"
                class="desk:w-[354px] md:w-[336px] w-[331px] flex items-center justify-between rounded-lg border px-3 py-2 transition-colors"
                :class="String(selected || '') === '{{ $rowValue }}' ? 'bg-[#FF7500] border-transparent text-white' : 'bg-white border-[#E5E7EB] text-[#666666] hover:border-[#FF7500]/50'"
            >
                <span class="inline-flex items-center gap-2">
                    @foreach($leftChars as $i => $char)
                        @php
                            $val = $r['char_values'][$char['id']] ?? null;
                            $svg = $char['svg'] ?? null;
                        @endphp
                        <span class="inline-flex items-center gap-2 {{ $i ? 'ml-6 md:ml-8' : '' }}">
                        @if($svg)
                                <span aria-hidden="true" class="inline-block h-5 w-5"
                                      style="background-color: currentColor;
                                          mask-image:url('{{ $svg }}');-webkit-mask-image:url('{{ $svg }}');
                                          mask-repeat:no-repeat;-webkit-mask-repeat:no-repeat;
                                          mask-position:center;-webkit-mask-position:center;
                                          mask-size:contain;-webkit-mask-size:contain;"></span>
                            @endif
                            @if($val)<span>{{ $val }}</span>@endif
                        </span>
                    @endforeach
                </span>

                @if($rightChar)
                    @php
                        $valRaw    = $r['char_values'][$rightChar['id']] ?? null;
                        $val       = is_array($valRaw) ? (string)($valRaw['title'] ?? reset($valRaw)) : (string)$valRaw;

                        $isPersons = ($rightChar['slug'] ?? null) === $personSlug;
                        $people    = 1;
                        if ($isPersons) {
                            $digits = (int) preg_replace('/\D+/', '', (string)$val);
                            $people = max(1, $digits ?: (is_numeric($val) ? (int)$val : 1));
                        }

                        $svgRight = $rightChar['svg'] ?? null;
                        $personIcon = $svgRight
                            ? '<span aria-hidden="true" class="inline-block h-5 w-[10px]"
                                style="background-color: currentColor;
                                mask-image:url(\'' . e($svgRight) . '\'); -webkit-mask-image:url(\'' . e($svgRight) . '\');
                                mask-repeat:no-repeat; -webkit-mask-repeat:no-repeat;
                                mask-position:center; -webkit-mask-position:center;
                                mask-size:contain; -webkit-mask-size:contain;"></span>'
                            : '';
                    @endphp

                    <span class="ml-auto inline-flex items-center whitespace-nowrap justify-end text-right shrink-0">
                    @if($isPersons)
                            @if($people <= 4)
                                {!! str_repeat($personIcon, $people) !!}
                            @else
                                {!! $personIcon !!}
                            @endif
                            @if(!empty(trim($val)))<span class="ml-1">{{ $val }}</span>@endif
                        @else
                            {!! $personIcon !!}
                            @if($val)<span class="ml-1">{{ $val }}</span>@endif
                        @endif
                    </span>
                @endif
            </button>
        @endforeach
        </div>

        {{-- подвал цен + кнопка (для карточек-списка можно оставить; для детальной страницы — убрать) --}}
        <div class="{{ $isSingleVariant ? 'mt-2' : 'mt-3' }} flex items-center justify-between">
            <div class="flex items-baseline gap-1 whitespace-nowrap shrink-0">
                <div class="flex items-baseline gap-1 text-neutral-400 line-through whitespace-nowrap"
                     x-show="prices[selected]?.old && prices[selected]?.old > prices[selected]?.price">
                    <span class="font-semibold text-[16px] leading-[16px]" x-text="fmt(prices[selected]?.old).uah">{{ $op['uah'] ?? '' }}</span>
                    <span class="text-[12px] leading-[12px]">{{ $rowsSelectorLabels['currency'] }}</span>
                </div>

                <div
                    class="flex items-baseline gap-1 whitespace-nowrap"
                    :class="prices[selected]?.old && prices[selected]?.old > prices[selected]?.price ? 'text-[#DC2626]' : 'text-[#333333]'"
                >
                    <span class="font-bold text-[26px] leading-[32px]" x-text="fmt(prices[selected]?.price).uah">{{ $p['uah'] }}</span>
                    <span class="text-[14px] leading-[14px]">{{ $rowsSelectorLabels['currency'] }}</span>
                </div>
            </div>

            {{-- Правая зона (кнопка) фиксированной ширины как в Figma --}}
            <div class="w-[153px] md:w-[173px] shrink-0">
                <button
                    type="button"
                    x-show="cartQty === 0"
                    x-cloak
                    class="w-full inline-flex items-center justify-center text-[12px] h-[36px] gap-2 rounded bg-[#FF7500] px-4 font-semibold text-white shadow-[0_4px_12px_rgba(255,117,0,.35)] transition
               hover:bg-[#ff841f] active:bg-[#e66700] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#FF7500]/50 disabled:opacity-60"
                    x-bind:data-product-id="selectedProductId()"
                    @click="addToCart"
                    x-bind:disabled="adding"
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
                    {{ $cartText }}
                </button>

                <div
                    x-show="cartQty > 0"
                    x-cloak
                    class="w-full inline-flex items-center justify-between bg-[#FDDDA7] text-[#FF7500] h-[36px] rounded px-2"
                >
                    <button
                        type="button"
                        class="w-8 h-8 grid place-items-center text-[22px] leading-none rounded disabled:opacity-40"
                        @click="decrementQty"
                        x-bind:disabled="adding || cartQty <= 0"
                        x-bind:aria-label="cartQty === 1 ? 'Видалити з кошика' : 'Зменшити кількість'"
                    >
                        <svg x-show="cartQty > 1" class="w-4 h-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M5 12H19" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
                        </svg>

                        <svg x-show="cartQty === 1" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6h14zM14 11v6M10 11v6M3 6h18M8 6V4a2 2 0 012-2h4a2 2 0 012 2v2" stroke="currentColor" stroke-width="2" stroke-linecap="square"/>
                        </svg>
                    </button>

                    <div class="flex-1 text-center font-semibold text-[14px]" x-text="cartQty">1</div>

                    <button
                        type="button"
                        class="w-8 h-8 grid place-items-center text-[22px] leading-none rounded disabled:opacity-40"
                        @click="incrementQty"
                        x-bind:disabled="adding"
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

        <button
            type="button"
            class="hidden mt-2 flex w-full items-center gap-2 rounded-lg bg-[#FFF1EB] px-3 py-2 text-left text-[12px] leading-4 text-[#7A3418] transition hover:bg-[#FFE4D9] focus:outline-none focus:ring-2 focus:ring-[#FF7500]/40"
            @click="openInstallmentInfo($event)"
            aria-haspopup="dialog"
            :aria-expanded="showInstallmentInfo"
        >
            <svg class="h-5 w-5 shrink-0 text-[#FF7500]" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <rect x="3" y="5" width="18" height="14" rx="2" stroke="currentColor" stroke-width="2"/>
                <path d="M3 10H21M7 15H10" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
            <span>{{ $rowsSelectorLabels['installment_prefix'] }}</span>
            <strong><span x-text="fmt(installmentPayment()).uah"></span> {{ $rowsSelectorLabels['installment_term'] }}</strong>
            <svg class="ml-auto h-4 w-4 shrink-0 text-[#FF7500]" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M9 5l7 7-7 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </button>

        <div class="mt-2 grid grid-cols-2 gap-2 text-[10px] leading-3 text-[#777]">
            <button type="button" class="flex min-w-0 cursor-pointer items-center gap-1.5 rounded-xl border border-[#E5E7EB] bg-white px-2 py-1.5 text-left transition hover:border-[#FF7500] hover:bg-[#FFF8F3] focus:outline-none focus:ring-2 focus:ring-[#FF7500]/40" @click="openBankTerms('monobank')" aria-haspopup="dialog">
                <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-[#242938] text-[11px] font-semibold text-white">M</span>
                <span class="min-w-0"><strong class="block truncate text-[#242938]">monobank</strong><span class="block truncate">{{ $rowsSelectorLabels['mono_label'] }}</span><span class="block whitespace-nowrap">{{ $rowsSelectorLabels['installment_prefix'] }} <strong><span x-text="fmt(installmentPayment()).uah"></span> {{ $rowsSelectorLabels['currency'] }}/{{ $rowsSelectorLabels['per_month_short'] }}</strong></span></span>
            </button>
            <button type="button" class="flex min-w-0 cursor-pointer items-center gap-1.5 rounded-xl border border-[#E5E7EB] bg-white px-2 py-1.5 text-left transition hover:border-[#FF7500] hover:bg-[#FFF8F3] focus:outline-none focus:ring-2 focus:ring-[#FF7500]/40" @click="openBankTerms('privatbank')" aria-haspopup="dialog">
                <svg class="h-7 w-7 shrink-0" viewBox="0 0 40 40" fill="none" aria-hidden="true">
                    <path d="M32.7059 32H20.6827C20.6827 31.029 20.6922 30.0756 20.6803 29.1211C20.6637 27.7979 20.4866 26.4969 20.0541 25.2395C19.075 22.3911 17.0156 20.7497 14.1114 20.0511C12.5999 19.6871 11.0611 19.6636 9.51867 19.6707C9.0279 19.673 8.53713 19.6707 8.03448 19.6707V8H32.7059V32ZM27.5986 27.0406V12.9771H13.1525V14.8521C20.3809 15.8595 24.6136 19.8421 25.6557 27.0417H27.5998L27.5986 27.0406Z" fill="#76AE42"/>
                    <path d="M8 31.9905V22.6246H17.649V31.9905H8Z" fill="black"/>
                </svg>
                <span class="min-w-0"><strong class="block truncate text-[#242938]">ПриватБанк</strong><span class="block truncate">{{ $rowsSelectorLabels['privat_label'] }}</span><span class="block whitespace-nowrap">{{ $rowsSelectorLabels['installment_prefix'] }} <strong><span x-text="fmt(installmentPayment()).uah"></span> {{ $rowsSelectorLabels['currency'] }}/{{ $rowsSelectorLabels['per_month_short'] }}</strong></span></span>
            </button>
        </div>

        <div
            x-show="showInstallmentInfo"
            x-cloak
            x-transition.opacity
            @keydown.escape.window="showInstallmentInfo = false"
            class="fixed inset-0"
            style="position: fixed; inset: 0; z-index: 100;"
            role="dialog"
            aria-modal="true"
            aria-label="{{ st('product.installment.dialog_title', 'Купити частинами') }}"
            @click.self="showInstallmentInfo = false"
        >
            <div
                x-transition.scale.origin.center
                class="relative rounded-2xl bg-white p-5 shadow-2xl"
                :style="`position: fixed; width: min(320px, calc(100vw - 32px)); max-width: calc(100vw - 32px); top: ${installmentPopup.top}px; left: ${installmentPopup.left}px;`"
            >
                <button type="button" class="absolute right-3 top-3 flex h-7 w-7 items-center justify-center rounded-full text-lg text-[#777] hover:bg-gray-100" @click="showInstallmentInfo = false" aria-label="{{ st('all.close', 'Закрити') }}">×</button>
                <h3 class="pr-7 text-[18px] font-bold text-[#242938]">{{ st('product.installment.dialog_title', 'Купити частинами') }}</h3>

                <div class="mt-4 space-y-3">
                    <div class="flex items-center gap-3 rounded-xl bg-[#F7F7F7] p-3">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-[#242938] text-sm font-semibold text-white">M</span>
                        <div><strong class="block text-sm text-[#242938]">monobank</strong><span class="text-xs text-[#666]"><span x-text="fmt(installmentPayment()).uah"></span> {{ st('cart.summary.currency_short', 'грн') }} × 3 {{ st('product.installment.dialog_payments', 'платежі') }}</span><span class="block text-xs text-[#777]">{{ $rowsSelectorLabels['mono_label'] }}</span></div>
                    </div>
                    <div class="flex items-center gap-3 rounded-xl bg-[#F7F7F7] p-3">
                        <svg class="h-10 w-10 shrink-0" viewBox="0 0 40 40" fill="none" aria-hidden="true"><path d="M32.7059 32H20.6827C20.6827 31.029 20.6922 30.0756 20.6803 29.1211C20.6637 27.7979 20.4866 26.4969 20.0541 25.2395C19.075 22.3911 17.0156 20.7497 14.1114 20.0511C12.5999 19.6871 11.0611 19.6636 9.51867 19.6707C9.0279 19.673 8.53713 19.6707 8.03448 19.6707V8H32.7059V32ZM27.5986 27.0406V12.9771H13.1525V14.8521C20.3809 15.8595 24.6136 19.8421 25.6557 27.0417H27.5998L27.5986 27.0406Z" fill="#76AE42"/><path d="M8 31.9905V22.6246H17.649V31.9905H8Z" fill="black"/></svg>
                        <div><strong class="block text-sm text-[#242938]">ПриватБанк</strong><span class="text-xs text-[#666]"><span x-text="fmt(installmentPayment()).uah"></span> {{ st('cart.summary.currency_short', 'грн') }} × 3 {{ st('product.installment.dialog_payments', 'платежі') }}</span><span class="block text-xs text-[#777]">{{ $rowsSelectorLabels['privat_label'] }}</span></div>
                    </div>
                </div>
                <p class="mt-4 text-center text-xs leading-4 text-[#777]">{{ st('product.installment.dialog_hint', 'Оформити оплату частинами можна під час оформлення замовлення.') }}</p>
            </div>
        </div>

        <div
            x-show="showBankTerms"
            x-cloak
            x-transition.opacity
            @keydown.escape.window="showBankTerms = false"
            class="fixed inset-0"
            style="position: fixed; inset: 0; z-index: 110; background: rgba(0,0,0,.5);"
            role="dialog"
            aria-modal="true"
            :aria-label="bankTerms.name"
            @click.self="showBankTerms = false"
        >
            <div
                x-transition.scale.origin.center
                class="relative rounded-2xl bg-white p-5 shadow-2xl"
                style="position: fixed; width: min(560px, calc(100vw - 32px)); max-width: calc(100vw - 32px); max-height: calc(100vh - 32px); overflow-y: auto; top: 50%; left: 50%; transform: translate(-50%, -50%);"
            >
                <button type="button" class="absolute right-3 top-3 flex h-8 w-8 items-center justify-center rounded-full text-xl text-[#777] hover:bg-gray-100" @click="showBankTerms = false" aria-label="{{ st('all.close', 'Закрити') }}">×</button>
                <h3 class="pr-9 text-[20px] font-bold text-[#242938]" x-text="bankTerms.name"></h3>
                <div class="mt-4 text-sm leading-6 text-[#4B5563]" x-html="bankTerms.html || @js(st('product.installment.dialog_hint', 'Оформити оплату частинами можна під час оформлення замовлення.'))"></div>
            </div>
        </div>

        </div>
@endif
