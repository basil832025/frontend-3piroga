@php
    $stepsTitle = strip_tags(
        page_field(
            $pageSlug,
            'mono_steps_title',
            'Як оформити покупку частинами?'
        ) ?? 'Як оформити покупку частинами?'
    );

    $stepsContent = page_field(
        $pageSlug,
        'mono_steps_content',
        '
        <ol>
            <li><strong>Оберіть пироги</strong> та додайте потрібні позиції до кошика.</li>
            <li><strong>Переконайтеся</strong>, що сума замовлення становить від 2000 грн.</li>
            <li><strong>Під час оформлення</strong> виберіть спосіб оплати «Покупка частинами» від monobank.</li>
            <li><strong>Підтвердьте операцію</strong> у застосунку monobank.</li>
            <li><strong>Очікуйте доставку</strong> та насолоджуйтеся улюбленими пирогами.</li>
        </ol>
        '
    );
@endphp

<section class="bg-white py-16 sm:py-20 lg:py-24">
    <div class="mx-auto max-w-7xl px-6 lg:px-8">

        <div class="mx-auto max-w-3xl text-center">
            <div class="inline-flex items-center rounded-full bg-black px-4 py-2 text-sm font-semibold text-white">
                5 простих кроків
            </div>

            <h2 class="mt-6 text-3xl font-black tracking-tight text-black sm:text-4xl lg:text-5xl">
                {{ $stepsTitle }}
            </h2>

            <p class="mt-5 text-lg leading-relaxed text-gray-600">
                Від вибору пирогів до підтвердження оплати у застосунку — усе займає лише кілька хвилин.
            </p>
        </div>

        <div class="relative mt-14">
            <div class="absolute left-7 top-7 hidden h-[calc(100%-3.5rem)] w-px bg-black/10 lg:block"></div>

            <div class="space-y-6">

                <article class="relative grid gap-5 rounded-3xl border border-black/10 bg-[#f7f7f8] p-6 lg:grid-cols-[56px_1fr_auto] lg:items-center">
                    <div class="relative z-10 flex h-14 w-14 items-center justify-center rounded-2xl bg-black text-xl font-black text-white">
                        1
                    </div>

                    <div>
                        <h3 class="text-xl font-black text-black">
                            Оберіть улюблені пироги
                        </h3>

                        <p class="mt-2 leading-relaxed text-gray-600">
                            Перейдіть до каталогу, оберіть потрібні позиції та додайте їх до кошика.
                        </p>
                    </div>

                    <a
                        href="{{ $catalogUrl }}"
                        class="inline-flex items-center justify-center rounded-xl border border-black/10 bg-white px-5 py-3 text-sm font-bold text-black transition hover:bg-black hover:text-white"
                    >
                        До каталогу
                    </a>
                </article>

                <article class="relative grid gap-5 rounded-3xl border border-black/10 bg-[#f7f7f8] p-6 lg:grid-cols-[56px_1fr_auto] lg:items-center">
                    <div class="relative z-10 flex h-14 w-14 items-center justify-center rounded-2xl bg-black text-xl font-black text-white">
                        2
                    </div>

                    <div>
                        <h3 class="text-xl font-black text-black">
                            Перевірте суму замовлення
                        </h3>

                        <p class="mt-2 leading-relaxed text-gray-600">
                            Загальна вартість замовлення має становити не менше 2000 грн.
                        </p>
                    </div>

                    <div class="rounded-xl bg-white px-5 py-3 text-sm font-black text-black shadow-sm">
                        від 2000 грн
                    </div>
                </article>

                <article class="relative grid gap-5 rounded-3xl border border-black/10 bg-[#f7f7f8] p-6 lg:grid-cols-[56px_1fr_auto] lg:items-center">
                    <div class="relative z-10 flex h-14 w-14 items-center justify-center rounded-2xl bg-black text-xl font-black text-white">
                        3
                    </div>

                    <div>
                        <h3 class="text-xl font-black text-black">
                            Виберіть monobank
                        </h3>

                        <p class="mt-2 leading-relaxed text-gray-600">
                            На етапі оформлення замовлення виберіть спосіб оплати «Покупка частинами».
                        </p>
                    </div>

                    <div class="rounded-xl bg-black px-5 py-3 text-sm font-bold text-white">
                        Покупка частинами
                    </div>
                </article>

                <article class="relative grid gap-5 rounded-3xl border border-black/10 bg-[#f7f7f8] p-6 lg:grid-cols-[56px_1fr_auto] lg:items-center">
                    <div class="relative z-10 flex h-14 w-14 items-center justify-center rounded-2xl bg-black text-xl font-black text-white">
                        4
                    </div>

                    <div>
                        <h3 class="text-xl font-black text-black">
                            Підтвердьте у застосунку
                        </h3>

                        <p class="mt-2 leading-relaxed text-gray-600">
                            Відкрийте застосунок monobank і підтвердьте запропоновану операцію.
                        </p>
                    </div>

                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white text-black shadow-sm">
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
                </article>

                <article class="relative grid gap-5 rounded-3xl border border-black/10 bg-black p-6 text-white lg:grid-cols-[56px_1fr_auto] lg:items-center">
                    <div class="relative z-10 flex h-14 w-14 items-center justify-center rounded-2xl bg-white text-xl font-black text-black">
                        5
                    </div>

                    <div>
                        <h3 class="text-xl font-black text-white">
                            Отримайте замовлення
                        </h3>

                        <p class="mt-2 leading-relaxed text-white/70">
                            Після підтвердження замовлення ми приготуємо пироги та передамо їх на доставку.
                        </p>
                    </div>

                    <div class="rounded-xl bg-white/10 px-5 py-3 text-sm font-bold text-white">
                        Готово
                    </div>
                </article>

            </div>
        </div>

    </div>
</section>