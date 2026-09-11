@php
    $holidayNotice = $holidayNotice ?? null;
    $message = trim((string) data_get($holidayNotice, 'comment', ''));
    $message = trim(strip_tags($message)) !== ''
        ? $message
        : st('checkout.holiday.default_message', 'Сьогодні ми не працюємо. Ви можете оформити передзамовлення на доступну дату.');
    $locale = app()->getLocale();
    $preorderUrl = in_array($locale, ['ru', 'en'], true)
        ? url('/' . $locale . '/pies')
        : url('/pies');
    $routeName = (string) request()->route()?->getName();
    $path = trim((string) request()->path(), '/');
    $isCheckout = str_ends_with($routeName, 'checkout') || str_ends_with($routeName, 'checkout.submit');
    $isHome = str_ends_with($routeName, 'home') || in_array($path, ['', 'ru', 'en'], true);
    $showMode = ($isCheckout || $isHome) ? 'always' : 'google_once';
@endphp

@if ($holidayNotice)
    <div
        x-data="{
            open: false,
            mode: @js($showMode),
            storageKey: 'tpHolidayNoticeSeenFromGoogle:{{ data_get($holidayNotice, 'id', 'current') }}',
            init() {
                if (this.mode === 'always') {
                    this.open = true;
                    return;
                }

                const fromGoogle = (() => {
                    try {
                        const referrer = document.referrer || '';
                        if (!referrer) return false;
                        const host = new URL(referrer).hostname.toLowerCase();
                        return host === 'google.com' || host.endsWith('.google.com') || host.includes('.google.');
                    } catch (_) {
                        return false;
                    }
                })();

                if (fromGoogle && !sessionStorage.getItem(this.storageKey)) {
                    sessionStorage.setItem(this.storageKey, '1');
                    this.open = true;
                }
            },
            close() {
                this.open = false;
            },
            preorder() {
                this.close();
                window.location.href = @js($preorderUrl);
            },
        }"
        x-show="open"
        x-cloak
        x-transition.opacity
        class="fixed inset-0 z-[10020] flex items-center justify-center bg-black/45 px-4 py-8 backdrop-blur-[2px]"
        @keydown.escape.window="close()"
        @click.self="close()"
    >
        <div
            x-show="open"
            x-transition.scale.90
            class="relative w-full max-w-[420px] overflow-hidden rounded-[22px] bg-white shadow-[0_22px_70px_rgba(39,40,40,.22)]"
        >
            <div class="absolute inset-x-0 top-0 h-2 bg-[#FF7500]"></div>

            <button
                type="button"
                class="absolute right-3 top-3 grid h-10 w-10 place-items-center rounded-full text-[#7C4A2A] transition hover:bg-[#FFF3E8]"
                @click="close()"
                aria-label="{{ st('checkout.holiday.cancel', 'Скасувати') }}"
            >
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M6 6L18 18M18 6L6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </button>

            <div class="px-6 pb-6 pt-8 text-center">
                <div class="mx-auto grid h-16 w-16 place-items-center rounded-full bg-[#FFF3E8] text-[#FF7500] shadow-inner">
                    <svg class="h-9 w-9" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M12 8V13" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"/>
                        <path d="M12 17H12.01" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
                        <path d="M10.3 4.2L2.9 17.1C2.1 18.5 3.1 20.2 4.7 20.2H19.3C20.9 20.2 21.9 18.5 21.1 17.1L13.7 4.2C12.9 2.8 11.1 2.8 10.3 4.2Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                    </svg>
                </div>

                <div class="mt-5 text-[24px] font-bold leading-[30px] text-[#272828]">
                    {{ st('checkout.holiday.title', 'Увага') }}
                </div>

                <div class="prose prose-sm mx-auto mt-3 max-w-none text-center leading-[22px] text-[#4B5563] prose-p:my-2 prose-strong:text-[#272828] prose-a:text-[#FF7500] prose-a:no-underline hover:prose-a:underline prose-ul:my-2 prose-ul:list-position-inside prose-ul:pl-0 prose-ol:my-2 prose-ol:list-position-inside prose-ol:pl-0">
                    {!! $message !!}
                </div>

                @if ($isCheckout)
                    <div class="mt-6">
                        <button
                            type="button"
                            class="h-12 w-full rounded-[12px] bg-[#FF7500] px-4 text-[15px] font-semibold leading-[18px] text-white shadow-[0_5px_16px_rgba(255,117,0,.32)] transition hover:bg-[#e56700]"
                            @click="close()"
                        >
                            OK
                        </button>
                    </div>
                @else
                    <div class="mt-6 grid gap-3 sm:grid-cols-2">
                        <button
                            type="button"
                            class="h-12 rounded-[12px] bg-[#FF7500] px-4 text-[15px] font-semibold leading-[18px] text-white shadow-[0_5px_16px_rgba(255,117,0,.32)] transition hover:bg-[#e56700]"
                            @click="preorder()"
                        >
                            {{ st('checkout.holiday.preorder', 'Зробити передзамовлення') }}
                        </button>

                        <button
                            type="button"
                            class="h-12 rounded-[12px] border border-[#F2C49E] bg-white px-4 text-[15px] font-semibold text-[#7C4A2A] transition hover:bg-[#FFF8F1]"
                            @click="close()"
                        >
                            {{ st('checkout.holiday.cancel', 'Скасувати') }}
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endif
