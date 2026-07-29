@php
    $faqContent = page_field(
        $pageSlug,
        'mono_faq',
        '
        <h3>Поширені запитання</h3>

        <p><strong>Від якої суми доступна покупка частинами?</strong></p>
        <p>Сервіс доступний для замовлень від 2000 грн.</p>

        <p><strong>На скільки платежів можна розділити оплату?</strong></p>
        <p>Суму замовлення можна розділити на 3 платежі.</p>

        <p><strong>Що потрібно для оформлення?</strong></p>
        <p>Потрібна картка monobank і доступний ліміт сервісу «Покупка частинами».</p>

        <p><strong>Де підтверджується покупка?</strong></p>
        <p>Операція підтверджується у застосунку monobank.</p>

        <p><strong>Хто приймає рішення про доступність сервісу?</strong></p>
        <p>Остаточне рішення приймає банк відповідно до доступного ліміту клієнта.</p>
        '
    );
@endphp

<section
    class="bg-[#f4f4f5] py-16 sm:py-20 lg:py-24"
    x-data="{ active: 1 }"
>
    <div class="mx-auto max-w-5xl px-6 lg:px-8">

        <div class="mx-auto max-w-3xl text-center">
            <div class="inline-flex items-center rounded-full border border-black/10 bg-white px-4 py-2 text-sm font-semibold text-black shadow-sm">
                FAQ
            </div>

            <h2 class="mt-6 text-3xl font-black tracking-tight text-black sm:text-4xl lg:text-5xl">
                Поширені запитання
            </h2>

            <p class="mt-5 text-lg leading-relaxed text-gray-600">
                Основна інформація про оформлення замовлення через сервіс «Покупка частинами» від monobank.
            </p>
        </div>

        <div class="mt-12 space-y-4">

            <article class="overflow-hidden rounded-3xl border border-black/10 bg-white shadow-sm">
                <button
                    type="button"
                    class="flex w-full items-center justify-between gap-6 px-6 py-5 text-left sm:px-8"
                    @click="active = active === 1 ? null : 1"
                    :aria-expanded="active === 1"
                >
                    <span class="text-lg font-black text-black">
                        Від якої суми доступна покупка частинами?
                    </span>

                    <span
                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-black text-white transition"
                        :class="{ 'rotate-45': active === 1 }"
                    >
                        <svg
                            class="h-5 w-5"
                            viewBox="0 0 20 20"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                d="M10 4V16M4 10H16"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linecap="round"
                            />
                        </svg>
                    </span>
                </button>

                <div
                    x-show="active === 1"
                    x-collapse
                    class="px-6 pb-6 sm:px-8"
                >
                    <p class="border-t border-black/10 pt-5 leading-relaxed text-gray-600">
                        Сервіс доступний для замовлень від <strong class="text-black">2000 грн</strong>.
                    </p>
                </div>
            </article>

            <article class="overflow-hidden rounded-3xl border border-black/10 bg-white shadow-sm">
                <button
                    type="button"
                    class="flex w-full items-center justify-between gap-6 px-6 py-5 text-left sm:px-8"
                    @click="active = active === 2 ? null : 2"
                    :aria-expanded="active === 2"
                >
                    <span class="text-lg font-black text-black">
                        На скільки платежів можна розділити оплату?
                    </span>

                    <span
                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-black text-white transition"
                        :class="{ 'rotate-45': active === 2 }"
                    >
                        <svg
                            class="h-5 w-5"
                            viewBox="0 0 20 20"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                d="M10 4V16M4 10H16"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linecap="round"
                            />
                        </svg>
                    </span>
                </button>

                <div
                    x-show="active === 2"
                    x-collapse
                    class="px-6 pb-6 sm:px-8"
                >
                    <p class="border-t border-black/10 pt-5 leading-relaxed text-gray-600">
                        Загальна сума замовлення розподіляється на <strong class="text-black">3 платежі</strong>.
                    </p>
                </div>
            </article>

            <article class="overflow-hidden rounded-3xl border border-black/10 bg-white shadow-sm">
                <button
                    type="button"
                    class="flex w-full items-center justify-between gap-6 px-6 py-5 text-left sm:px-8"
                    @click="active = active === 3 ? null : 3"
                    :aria-expanded="active === 3"
                >
                    <span class="text-lg font-black text-black">
                        Що потрібно для оформлення?
                    </span>

                    <span
                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-black text-white transition"
                        :class="{ 'rotate-45': active === 3 }"
                    >
                        <svg
                            class="h-5 w-5"
                            viewBox="0 0 20 20"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                d="M10 4V16M4 10H16"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linecap="round"
                            />
                        </svg>
                    </span>
                </button>

                <div
                    x-show="active === 3"
                    x-collapse
                    class="px-6 pb-6 sm:px-8"
                >
                    <p class="border-t border-black/10 pt-5 leading-relaxed text-gray-600">
                        Потрібна картка monobank та доступний ліміт сервісу «Покупка частинами».
                    </p>
                </div>
            </article>

            <article class="overflow-hidden rounded-3xl border border-black/10 bg-white shadow-sm">
                <button
                    type="button"
                    class="flex w-full items-center justify-between gap-6 px-6 py-5 text-left sm:px-8"
                    @click="active = active === 4 ? null : 4"
                    :aria-expanded="active === 4"
                >
                    <span class="text-lg font-black text-black">
                        Де підтверджується покупка?
                    </span>

                    <span
                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-black text-white transition"
                        :class="{ 'rotate-45': active === 4 }"
                    >
                        <svg
                            class="h-5 w-5"
                            viewBox="0 0 20 20"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                d="M10 4V16M4 10H16"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linecap="round"
                            />
                        </svg>
                    </span>
                </button>

                <div
                    x-show="active === 4"
                    x-collapse
                    class="px-6 pb-6 sm:px-8"
                >
                    <p class="border-t border-black/10 pt-5 leading-relaxed text-gray-600">
                        Після вибору способу оплати операцію потрібно підтвердити у застосунку monobank.
                    </p>
                </div>
            </article>

            <article class="overflow-hidden rounded-3xl border border-black/10 bg-white shadow-sm">
                <button
                    type="button"
                    class="flex w-full items-center justify-between gap-6 px-6 py-5 text-left sm:px-8"
                    @click="active = active === 5 ? null : 5"
                    :aria-expanded="active === 5"
                >
                    <span class="text-lg font-black text-black">
                        Чому monobank може не підтвердити оплату?
                    </span>

                    <span
                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-black text-white transition"
                        :class="{ 'rotate-45': active === 5 }"
                    >
                        <svg
                            class="h-5 w-5"
                            viewBox="0 0 20 20"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                d="M10 4V16M4 10H16"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linecap="round"
                            />
                        </svg>
                    </span>
                </button>

                <div
                    x-show="active === 5"
                    x-collapse
                    class="px-6 pb-6 sm:px-8"
                >
                    <p class="border-t border-black/10 pt-5 leading-relaxed text-gray-600">
                        Доступність сервісу залежить від індивідуального ліміту клієнта. Остаточне рішення приймає банк.
                    </p>
                </div>
            </article>

        </div>

    </div>
</section>