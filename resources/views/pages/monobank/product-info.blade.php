@php
    $providerTitle = strip_tags(
        page_field(
            $pageSlug,
            'mono_provider',
            'Надавач послуг'
        ) ?? 'Надавач послуг'
    );

    $providerInfo = page_field(
        $pageSlug,
        'mono_provider_info',
        '
        <p><strong>АТ «УНІВЕРСАЛ БАНК»</strong></p>
        <p>Ліцензія НБУ №92 від 20.01.1994.</p>
        <p>Номер у Державному реєстрі банків №226.</p>
        '
    );

    $featuresTitle = strip_tags(
        page_field(
            $pageSlug,
            'mono_features_title',
            'Характеристики продукту'
        ) ?? 'Характеристики продукту'
    );

    $features = page_field(
        $pageSlug,
        'mono_features',
        '
        <ul>
            <li>Мінімальна сума розстрочки — <strong>2 грн</strong></li>
            <li>Максимальна сума розстрочки — <strong>400 000 грн</strong></li>
            <li>Реальна річна процентна ставка — <strong>0,000001%</strong></li>
            <li>Доступний строк — <strong>3–25 платежів</strong></li>
            <li>Порядок погашення — щомісячні платежі рівними частинами</li>
            <li>Перший платіж у момент оформлення покупки</li>
        </ul>
        '
    );

    $warning = page_field(
        $pageSlug,
        'mono_warning',
        '
        <p>
            Інформація про істотні характеристики продукту та попередження
            розміщені на сайті
            <a href="https://chast.monobank.ua" target="_blank" rel="noopener noreferrer">
                chast.monobank.ua
            </a>.
        </p>
        '
    );
@endphp

<section class="relative overflow-hidden bg-white py-14 sm:py-16 lg:py-20">

    <div class="pointer-events-none absolute inset-0">
        <div class="absolute -left-24 top-10 h-64 w-64 rounded-full bg-fuchsia-100/60 blur-3xl"></div>
        <div class="absolute -right-24 bottom-0 h-72 w-72 rounded-full bg-violet-100/60 blur-3xl"></div>
    </div>

    <div class="relative mx-auto max-w-7xl px-6 lg:px-8">

        <div class="grid gap-6 lg:grid-cols-2">

            {{-- Надавач послуг --}}
            <article
                class="group relative overflow-hidden rounded-[2rem] border border-black/10 bg-[#f7f7f8] p-6 transition duration-300 hover:-translate-y-1 hover:shadow-xl sm:p-8"
            >
                <div class="absolute -right-10 -top-10 h-32 w-32 rounded-full bg-black/[0.03]"></div>

                <div class="relative flex items-start gap-4">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-black text-white shadow-sm">
                        <svg
                            class="h-6 w-6"
                            viewBox="0 0 24 24"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                            aria-hidden="true"
                        >
                            <path d="M4 10H20" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                            <path d="M6 10V19" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                            <path d="M10 10V19" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                            <path d="M14 10V19" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                            <path d="M18 10V19" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                            <path d="M3 19H21" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                            <path d="M12 3L21 8H3L12 3Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                        </svg>
                    </div>

                    <div class="min-w-0">
                        <div class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-400">
                            monobank
                        </div>

                        <h2 class="mt-2 text-2xl font-black tracking-tight text-black">
                            {{ $providerTitle }}
                        </h2>
                    </div>
                </div>

                <div
                    class="prose prose-lg relative mt-7 max-w-none
                           prose-p:my-3
                           prose-p:leading-8
                           prose-p:text-gray-700
                           prose-strong:font-bold
                           prose-strong:text-black"
                >
                    {!! $providerInfo !!}
                </div>
            </article>

            {{-- Характеристики --}}
            <article
                class="group relative overflow-hidden rounded-[2rem] border border-black/10 bg-black p-6 text-white transition duration-300 hover:-translate-y-1 hover:shadow-[0_24px_60px_rgba(0,0,0,0.22)] sm:p-8"
            >
                <div class="absolute -right-12 -top-12 h-40 w-40 rounded-full bg-fuchsia-500/10 blur-2xl"></div>
                <div class="absolute -bottom-16 -left-12 h-40 w-40 rounded-full bg-violet-500/10 blur-2xl"></div>

                <div class="relative flex items-start gap-4">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-white text-black shadow-sm">
                        <svg
                            class="h-6 w-6"
                            viewBox="0 0 24 24"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                            aria-hidden="true"
                        >
                            <path d="M8 6H20" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                            <path d="M8 12H20" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                            <path d="M8 18H20" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                            <path d="M4 6H4.01" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/>
                            <path d="M4 12H4.01" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/>
                            <path d="M4 18H4.01" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/>
                        </svg>
                    </div>

                    <div class="min-w-0">
                        <div class="text-xs font-semibold uppercase tracking-[0.18em] text-white/60">
                            Умови сервісу
                        </div>

                        <h2 class="mt-2 text-2xl font-black tracking-tight text-white">
                            {{ $featuresTitle }}
                        </h2>
                    </div>
                </div>

                <div
                    class="relative mt-7
                           [&_ul]:m-0
                           [&_ul]:space-y-4
                           [&_ul]:pl-5
                           [&_li]:leading-8
                           [&_li]:text-white/90
                           [&_li::marker]:text-white/80
                           [&_strong]:font-bold
                           [&_strong]:text-white"
                >
                    {!! $features !!}
                </div>
            </article>

        </div>

        {{-- Попередження --}}
        <div class="mt-6 rounded-[1.5rem] border border-black/10 bg-white p-5 shadow-sm sm:p-6">
            <div class="flex items-start gap-4">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#f4f4f5] text-black">
                    <svg
                        class="h-5 w-5"
                        viewBox="0 0 24 24"
                        fill="none"
                        xmlns="http://www.w3.org/2000/svg"
                        aria-hidden="true"
                    >
                        <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.7"/>
                        <path d="M12 10V16" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                        <path d="M12 7H12.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </div>

                <div
                    class="prose max-w-none text-sm
                           prose-p:my-0
                           prose-p:leading-7
                           prose-p:text-gray-700
                           prose-a:font-semibold
                           prose-a:text-black
                           prose-a:underline
                           prose-a:underline-offset-4
                           hover:prose-a:text-gray-600"
                >
                    {!! $warning !!}
                </div>
            </div>
        </div>

    </div>
</section>