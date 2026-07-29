@php
    $ctaContent = page_field(
        $pageSlug,
        'mono_cta',
        '
        <h2>Замовляйте зараз — сплачуйте зручно!</h2>
        <p>Оформлюйте замовлення від 2000 грн і розділяйте оплату на 3 платежі з monobank.</p>
        '
    );
@endphp

<section class="relative overflow-hidden bg-black py-16 text-white sm:py-20 lg:py-24">
    <div class="absolute inset-0">
        <div class="absolute -left-24 top-0 h-80 w-80 rounded-full bg-fuchsia-500/20 blur-3xl"></div>
        <div class="absolute -right-24 bottom-0 h-96 w-96 rounded-full bg-violet-500/20 blur-3xl"></div>
    </div>

    <div class="relative mx-auto max-w-7xl px-6 lg:px-8">
        <div class="overflow-hidden rounded-[2rem] border border-white/10 bg-white/5 p-8 backdrop-blur sm:p-10 lg:p-14">

            <div class="grid items-center gap-10 lg:grid-cols-[1.3fr_0.7fr]">

                <div>
                    <div class="inline-flex items-center rounded-full bg-white px-4 py-2 text-sm font-bold text-black">
                        monobank · Покупка частинами
                    </div>

                    <div class="prose prose-lg mt-7 max-w-3xl prose-headings:text-white prose-h2:mb-5 prose-h2:text-3xl prose-h2:font-black prose-h2:tracking-tight sm:prose-h2:text-4xl lg:prose-h2:text-5xl prose-p:text-white/75 prose-p:leading-relaxed prose-strong:text-white prose-a:text-white">
                        {!! $ctaContent !!}
                    </div>

                    <div class="mt-8">
                        <a
                            href="{{ $catalogUrl }}"
                            class="inline-flex items-center justify-center rounded-2xl bg-white px-7 py-4 text-base font-black text-black transition hover:-translate-y-0.5 hover:bg-gray-100 hover:shadow-xl"
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

                <div>
                    <div class="rounded-[2rem] bg-white p-6 text-black shadow-2xl sm:p-8">

                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-sm font-semibold uppercase tracking-[0.18em] text-gray-400">
                                    Приклад
                                </div>

                                <div class="mt-2 text-2xl font-black">
                                    Замовлення 3000 грн
                                </div>
                            </div>

                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-black shadow-sm">
                                <x-icons.monobank-paw class="h-6 w-6 text-white" />
                            </div>
                        </div>

                        <div class="mt-7 space-y-3">
                            @foreach([1, 2, 3] as $payment)
                                <div class="flex items-center justify-between rounded-2xl bg-[#f5f5f6] px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white text-sm font-black shadow-sm">
                                            {{ $payment }}
                                        </div>

                                        <div>
                                            <div class="text-sm text-gray-500">
                                                Платіж {{ $payment }}
                                            </div>

                                            <div class="font-bold text-black">
                                                1/3 суми
                                            </div>
                                        </div>
                                    </div>

                                    <div class="text-lg font-black text-black">
                                        1000 грн
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-6 rounded-2xl bg-black p-5 text-white">
                            <div class="flex items-start gap-3">
                                <svg
                                    class="mt-0.5 h-6 w-6 shrink-0"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    xmlns="http://www.w3.org/2000/svg"
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

                                <p class="text-sm leading-relaxed text-white/75">
                                    Оплата доступна за умови достатнього ліміту у застосунку monobank.
                                </p>
                            </div>
                        </div>

                    </div>
                </div>

            </div>

        </div>
    </div>
</section>