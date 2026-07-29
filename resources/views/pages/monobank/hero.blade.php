@php
    $badge = strip_tags(
        page_field(
            $pageSlug,
            'mono_badge',
            'monobank · Покупка частинами'
        ) ?? 'monobank · Покупка частинами'
    );

    $title = strip_tags(
        page_field(
            $pageSlug,
            'mono_title',
            'Смачні осетинські пироги вже сьогодні'
        ) ?? 'Смачні осетинські пироги вже сьогодні'
    );

    $titleAccent = strip_tags(
        page_field(
            $pageSlug,
            'mono_title_accent',
            'сплачуйте частинами'
        ) ?? 'сплачуйте частинами'
    );

    $intro = page_field(
        $pageSlug,
        'mono_intro',
        '<p>Замовляйте улюблені осетинські пироги без зайвого навантаження на бюджет.</p>'
    );

    $heroImage = page_field(
        $pageSlug,
        'mono_hero_image'
    );
@endphp

<section class="relative overflow-hidden bg-[#f4f4f5]">
    <div class="absolute inset-0">
        <div class="absolute -left-24 top-20 h-72 w-72 rounded-full bg-fuchsia-200/40 blur-3xl"></div>
        <div class="absolute -right-24 bottom-0 h-96 w-96 rounded-full bg-violet-200/40 blur-3xl"></div>
    </div>

    <div class="relative mx-auto grid max-w-7xl items-center gap-12 px-6 py-16 lg:grid-cols-2 lg:px-8 lg:py-24">

        <div>
            <div class="inline-flex items-center gap-2 rounded-full border border-black/10 bg-white px-4 py-2 text-sm font-semibold shadow-sm">

                {{ $badge }}
            </div>

            <h1 class="mt-8 max-w-3xl text-4xl font-black leading-tight tracking-tight text-black sm:text-5xl lg:text-6xl">
                {{ $title }}

                <span class="mt-2 block bg-gradient-to-r from-fuchsia-600 to-violet-600 bg-clip-text text-transparent">
                    {{ $titleAccent }}
                </span>
            </h1>

            <div class="prose prose-lg mt-8 max-w-2xl text-gray-700 prose-p:leading-relaxed prose-strong:text-black">
                {!! $intro !!}
            </div>

            <div class="mt-10">
                <a
                    href="{{ $catalogUrl }}"
                    class="inline-flex items-center justify-center rounded-2xl bg-black px-7 py-4 text-base font-bold text-white transition duration-300 hover:-translate-y-0.5 hover:bg-gray-800 hover:shadow-xl"
                >
                    {{ $catalogButton }}

                    <svg
                        class="ml-2 h-5 w-5"
                        viewBox="0 0 20 20"
                        fill="none"
                        xmlns="http://www.w3.org/2000/svg"
                        aria-hidden="true"
                    >
                        <path
                            d="M4.16675 10H15.8334M15.8334 10L10.8334 5M15.8334 10L10.8334 15"
                            stroke="currentColor"
                            stroke-width="1.8"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        />
                    </svg>
                </a>
            </div>
        </div>

        <div class="relative">
            <div class="absolute -inset-4 rounded-[2rem] bg-gradient-to-r from-fuchsia-300/30 to-violet-300/30 blur-2xl"></div>

            <div
                class="group relative overflow-hidden rounded-[2rem] border border-black/10 bg-black p-6 shadow-2xl transition duration-500 ease-out hover:-translate-y-2 hover:rotate-[0.4deg] hover:shadow-[0_30px_80px_rgba(0,0,0,0.35)] sm:p-8"
            >
                <div class="flex items-center justify-between gap-4">
                    <div class="text-lg font-black text-white">
                        monobank
                    </div>

                    <div class="rounded-full bg-white/10 px-3 py-1 text-xs font-semibold text-white">
                        Покупка частинами
                    </div>
                </div>

                @if($heroImage)
                    <div class="mt-6 overflow-hidden rounded-3xl bg-white/5">
                        <img
                            src="{{ $heroImage }}"
                            alt="{{ $title }}"
                            class="h-72 w-full object-cover transition duration-500 group-hover:scale-[1.02] sm:h-80"
                        >
                    </div>
                @else
                    <div
                        class="mt-8 rounded-3xl bg-gradient-to-br from-zinc-900 to-zinc-800 p-6 transition duration-500 group-hover:from-zinc-800 group-hover:to-zinc-900 sm:p-8"
                    >
                        <div class="flex items-start justify-between gap-6">
                            <div>
                                <div class="text-sm text-white/60">
                                    Сума замовлення
                                </div>

                                <div class="mt-2 text-4xl font-black text-white">
                                    від 2000 грн
                                </div>
                            </div>

                            <div
                                class="flex h-16 w-16 shrink-0 items-center justify-center rounded-2xl bg-white shadow-sm transition duration-300 group-hover:-translate-y-1 group-hover:shadow-xl"
                            >
                                <x-icons.monobank-paw class="h-11 w-11" />
                            </div>
                        </div>

                        <div class="mt-10 grid grid-cols-3 gap-3">
                            @foreach([1, 2, 3] as $payment)
                                <div
                                    class="rounded-2xl bg-white p-4 text-center transition duration-300 group-hover:-translate-y-1 group-hover:shadow-lg"
                                >
                                    <div class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                                        Платіж {{ $payment }}
                                    </div>

                                    <div class="mt-2 text-lg font-black text-black">
                                        1/3
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div
                            class="mt-6 flex items-center gap-3 rounded-2xl bg-white/10 p-4 text-white transition duration-300 group-hover:bg-white/15"
                        >
                            <svg
                                class="h-6 w-6 shrink-0"
                                viewBox="0 0 24 24"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                                aria-hidden="true"
                            >
                                <path
                                    d="M12 3L20 7V12C20 17 16.6 21.4 12 22C7.4 21.4 4 17 4 12V7L12 3Z"
                                    stroke="currentColor"
                                    stroke-width="1.7"
                                    stroke-linejoin="round"
                                />

                                <path
                                    d="M9 12L11 14L15 10"
                                    stroke="currentColor"
                                    stroke-width="1.7"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                />
                            </svg>

                            <div class="text-sm text-white/80">
                                Підтвердження покупки у застосунку monobank
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

    </div>
</section>