@php
    $conditionsTitle = strip_tags(
        page_field(
            $pageSlug,
            'mono_conditions_title',
            'Умови оформлення'
        ) ?? 'Умови оформлення'
    );

    $conditionsContent = page_field(
        $pageSlug,
        'mono_conditions_content',
        '
        <ul>
            <li>Мінімальна сума замовлення — <strong>2000 грн</strong>.</li>
            <li>Кількість платежів — <strong>3</strong>.</li>
            <li>Необхідна картка monobank та доступний ліміт сервісу.</li>
            <li>Підтвердження здійснюється у застосунку monobank.</li>
            <li>Остаточне рішення щодо доступності приймає банк.</li>
        </ul>
        '
    );
@endphp

<section class="bg-[#f4f4f5] py-16 sm:py-20 lg:py-24">
    <div class="mx-auto max-w-7xl px-6 lg:px-8">

        <div class="grid items-center gap-12 lg:grid-cols-2 lg:gap-16">

            <div>
                <div class="inline-flex items-center rounded-full border border-black/10 bg-white px-4 py-2 text-sm font-semibold text-black shadow-sm">
                    Основні умови
                </div>

                <h2 class="mt-6 text-3xl font-black tracking-tight text-black sm:text-4xl lg:text-5xl">
                    {{ $conditionsTitle }}
                </h2>

                <p class="mt-6 max-w-2xl text-lg leading-relaxed text-gray-600">
                    Перед оформленням переконайтеся, що сума замовлення відповідає мінімальній вимозі, а у застосунку monobank доступний необхідний ліміт.
                </p>

                @if($conditionsContent)
                    <div class="prose prose-lg mt-8 max-w-none prose-headings:text-black prose-p:text-gray-600 prose-strong:text-black prose-li:text-gray-700 prose-marker:text-black">
                        {!! $conditionsContent !!}
                    </div>
                @endif
            </div>

            <div class="relative">
                <div class="absolute -inset-4 rounded-[2rem] bg-gradient-to-br from-fuchsia-200/40 to-violet-200/40 blur-2xl"></div>

                <div class="relative rounded-[2rem] border border-black/10 bg-white p-6 shadow-xl sm:p-8">

                    <div class="flex items-center justify-between border-b border-black/10 pb-6">
                        <div>
                            <div class="text-sm font-semibold uppercase tracking-[0.18em] text-gray-400">
                                Покупка частинами
                            </div>

                            <div class="mt-2 text-2xl font-black text-black">
                                Ключові параметри
                            </div>
                        </div>

                        <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-black text-sm font-black text-white">
                            mono
                        </div>
                    </div>

                    <div class="mt-6 space-y-4">

                        <div class="flex items-center justify-between gap-6 rounded-2xl bg-[#f6f6f7] p-5">
                            <div class="flex items-center gap-4">
                                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-white font-black text-black shadow-sm">
                                    ₴
                                </div>

                                <div>
                                    <div class="text-sm text-gray-500">
                                        Мінімальна сума
                                    </div>

                                    <div class="mt-1 font-bold text-black">
                                        Замовлення на сайті
                                    </div>
                                </div>
                            </div>

                            <div class="whitespace-nowrap text-xl font-black text-black">
                                2000 грн
                            </div>
                        </div>

                        <div class="flex items-center justify-between gap-6 rounded-2xl bg-[#f6f6f7] p-5">
                            <div class="flex items-center gap-4">
                                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-white font-black text-black shadow-sm">
                                    3
                                </div>

                                <div>
                                    <div class="text-sm text-gray-500">
                                        Кількість частин
                                    </div>

                                    <div class="mt-1 font-bold text-black">
                                        Рівні платежі
                                    </div>
                                </div>
                            </div>

                            <div class="whitespace-nowrap text-xl font-black text-black">
                                3 платежі
                            </div>
                        </div>

                        <div class="flex items-center justify-between gap-6 rounded-2xl bg-[#f6f6f7] p-5">
                            <div class="flex items-center gap-4">
                                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-white text-black shadow-sm">
                                    <svg
                                        class="h-6 w-6"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        xmlns="http://www.w3.org/2000/svg"
                                    >
                                        <rect
                                            x="6"
                                            y="2.5"
                                            width="12"
                                            height="19"
                                            rx="2.5"
                                            stroke="currentColor"
                                            stroke-width="1.7"
                                        />
                                        <path
                                            d="M9 7H15"
                                            stroke="currentColor"
                                            stroke-width="1.7"
                                            stroke-linecap="round"
                                        />
                                        <path
                                            d="M10 18H14"
                                            stroke="currentColor"
                                            stroke-width="1.7"
                                            stroke-linecap="round"
                                        />
                                    </svg>
                                </div>

                                <div>
                                    <div class="text-sm text-gray-500">
                                        Підтвердження
                                    </div>

                                    <div class="mt-1 font-bold text-black">
                                        У застосунку monobank
                                    </div>
                                </div>
                            </div>

                            <div class="rounded-full bg-black px-3 py-1 text-sm font-bold text-white">
                                Онлайн
                            </div>
                        </div>

                    </div>

                    <div class="mt-6 rounded-2xl border border-black/10 bg-black p-5 text-white">
                        <div class="flex items-start gap-3">
                            <svg
                                class="mt-0.5 h-6 w-6 shrink-0"
                                viewBox="0 0 24 24"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <circle
                                    cx="12"
                                    cy="12"
                                    r="9"
                                    stroke="currentColor"
                                    stroke-width="1.7"
                                />
                                <path
                                    d="M12 10V16"
                                    stroke="currentColor"
                                    stroke-width="1.7"
                                    stroke-linecap="round"
                                />
                                <path
                                    d="M12 7H12.01"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                />
                            </svg>

                            <p class="text-sm leading-relaxed text-white/75">
                                Доступність послуги залежить від індивідуального ліміту клієнта. Остаточне рішення приймає monobank.
                            </p>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</section>