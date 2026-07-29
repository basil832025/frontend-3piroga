@php
    $benefitsTitle = strip_tags(
        page_field(
            $pageSlug,
            'mono_benefits_title',
            'Чому це зручно?'
        ) ?? 'Чому це зручно?'
    );

    $benefitsContent = page_field(
        $pageSlug,
        'mono_benefits_content',
        '
        <h3>Замовляйте зараз — сплачуйте поступово</h3>
        <ul>
            <li>Оплата частинами для замовлень від <strong>2000 грн</strong>.</li>
            <li>Можливість розділити суму на <strong>3 рівні платежі</strong>.</li>
            <li>Швидке оформлення без складних анкет і зайвих документів.</li>
            <li>Підтвердження покупки у застосунку monobank.</li>
        </ul>
        '
    );
@endphp

<section class="bg-white py-16 sm:py-20 lg:py-24">
    <div class="mx-auto max-w-7xl px-6 lg:px-8">

        <div class="mx-auto max-w-3xl text-center">
            <div class="inline-flex items-center rounded-full bg-black px-4 py-2 text-sm font-semibold text-white">
                Переваги
            </div>

            <h2 class="mt-6 text-3xl font-black tracking-tight text-black sm:text-4xl lg:text-5xl">
                {{ $benefitsTitle }}
            </h2>

            <p class="mt-5 text-lg leading-relaxed text-gray-600">
                Велике замовлення більше не потрібно оплачувати одразу повною сумою.
            </p>
        </div>

        <div class="mt-12 grid gap-6 md:grid-cols-2 lg:grid-cols-4">

            <article class="group rounded-3xl border border-black/10 bg-[#f7f7f8] p-6 transition duration-300 hover:-translate-y-1 hover:shadow-xl">
                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-black text-white">
                    <svg
                        class="h-7 w-7"
                        viewBox="0 0 24 24"
                        fill="none"
                        xmlns="http://www.w3.org/2000/svg"
                    >
                        <path
                            d="M4 7H20V17H4V7Z"
                            stroke="currentColor"
                            stroke-width="1.7"
                            stroke-linejoin="round"
                        />
                        <path
                            d="M4 10H20"
                            stroke="currentColor"
                            stroke-width="1.7"
                        />
                        <path
                            d="M8 14H11"
                            stroke="currentColor"
                            stroke-width="1.7"
                            stroke-linecap="round"
                        />
                    </svg>
                </div>

                <h3 class="mt-6 text-xl font-black text-black">
                    Від 2000 грн
                </h3>

                <p class="mt-3 leading-relaxed text-gray-600">
                    Послуга доступна для замовлень, сума яких становить від 2000 грн.
                </p>
            </article>

            <article class="group rounded-3xl border border-black/10 bg-[#f7f7f8] p-6 transition duration-300 hover:-translate-y-1 hover:shadow-xl">
                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-black text-white">
                    <span class="text-xl font-black">3</span>
                </div>

                <h3 class="mt-6 text-xl font-black text-black">
                    Три платежі
                </h3>

                <p class="mt-3 leading-relaxed text-gray-600">
                    Загальна сума замовлення розподіляється на три зручні частини.
                </p>
            </article>

            <article class="group rounded-3xl border border-black/10 bg-[#f7f7f8] p-6 transition duration-300 hover:-translate-y-1 hover:shadow-xl">
                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-black text-white">
                    <svg
                        class="h-7 w-7"
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
                            d="M10 18H14"
                            stroke="currentColor"
                            stroke-width="1.7"
                            stroke-linecap="round"
                        />
                        <path
                            d="M9 7H15"
                            stroke="currentColor"
                            stroke-width="1.7"
                            stroke-linecap="round"
                        />
                    </svg>
                </div>

                <h3 class="mt-6 text-xl font-black text-black">
                    Підтвердження в застосунку
                </h3>

                <p class="mt-3 leading-relaxed text-gray-600">
                    Підтвердьте покупку безпосередньо у мобільному застосунку monobank.
                </p>
            </article>

            <article class="group rounded-3xl border border-black/10 bg-[#f7f7f8] p-6 transition duration-300 hover:-translate-y-1 hover:shadow-xl">
                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-black text-white">
                    <svg
                        class="h-7 w-7"
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
                </div>

                <h3 class="mt-6 text-xl font-black text-black">
                    Без зайвих документів
                </h3>

                <p class="mt-3 leading-relaxed text-gray-600">
                    Оформлення відбувається швидко, без паперових анкет і відвідування банку.
                </p>
            </article>

        </div>

        @if($benefitsContent)
            <div class="mt-12 overflow-hidden rounded-[2rem] bg-black px-6 py-8 text-white sm:px-10 lg:px-12">
                <div class="grid items-start gap-8 lg:grid-cols-[0.7fr_1.3fr]">

                    <div>
                        <div class="text-sm font-semibold uppercase tracking-[0.2em] text-white/50">
                            monobank
                        </div>

                        <div class="mt-3 text-2xl font-black sm:text-3xl">
                            Зручно для великих замовлень
                        </div>
                    </div>

                    <div class="prose prose-lg max-w-none prose-headings:text-white prose-h3:mt-0 prose-h3:text-2xl prose-h3:font-black prose-p:text-white/75 prose-strong:text-white prose-li:text-white/80 prose-marker:text-white">
                        {!! $benefitsContent !!}
                    </div>

                </div>
            </div>
        @endif

    </div>
</section>