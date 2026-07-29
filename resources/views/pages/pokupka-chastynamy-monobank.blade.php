@extends(front_view('layouts.app'))

@include(front_view('partials.seo.page'), ['page' => $page])

@section('full_width', 'true')
@section('page', 'monobank-installments')

@php
    $pageSlug = 'pokupka-chastynamy-monobank';

    $monoField = static function (string $field, string $default = '') use ($pageSlug): string {
        $value = page_field($pageSlug, $field, '');
        if (is_string($value) && trim($value) !== '') {
            return $value;
        }

        $value = page_field($pageSlug, $field, '', 'uk');
        if (is_string($value) && trim($value) !== '') {
            return $value;
        }

        return $default;
    };

    $locale = app()->getLocale();
    $defaultCatalogUrl = $locale === 'uk' ? '/pies' : '/' . $locale . '/pies';

    $badge = strip_tags($monoField('mono_badge', 'monobank · Покупка частинами'));
    $title = strip_tags($monoField('mono_title', 'Смачні осетинські пироги вже сьогодні'));
    $titleAccent = strip_tags($monoField('mono_title_accent', 'сплачуйте частинами'));
    $intro = $monoField('mono_intro');
    $catalogButton = strip_tags($monoField('mono_catalog_button', 'Перейти до каталогу'));
    $catalogUrl = strip_tags($monoField('mono_catalog_url', $defaultCatalogUrl));
    $benefitsTitle = strip_tags($monoField('mono_benefits_title', 'Чому це зручно?'));
    $benefitsContent = $monoField('mono_benefits_content');
    $conditionsTitle = strip_tags($monoField('mono_conditions_title', 'Умови оформлення'));
    $conditionsContent = $monoField('mono_conditions_content');
    $stepsTitle = strip_tags($monoField('mono_steps_title', 'Як оформити покупку частинами?'));
    $stepsContent = $monoField('mono_steps_content');
    $faqContent = $monoField('mono_faq');
    $ctaContent = $monoField('mono_cta');
@endphp

@section('content')
    <div class="overflow-hidden bg-[#f8faf5] text-[#171d2d]">
        <section class="relative isolate overflow-hidden bg-gradient-to-br from-[#f6fff8] via-white to-[#eef8ff] px-4 py-14 sm:px-6 sm:py-20">
            <div class="mx-auto grid max-w-[1198px] items-center gap-10 lg:grid-cols-[1.05fr_0.95fr]">
                <div>
                    @if ($badge !== '')
                        <div class="inline-flex items-center gap-2 rounded-full border border-black/10 bg-white px-5 py-2.5 text-sm font-bold text-[#111827] shadow-sm">
                            <span class="grid h-5 w-5 place-items-center rounded-full bg-black text-[10px] font-black text-white">m</span>
                            {{ $badge }}
                        </div>
                    @endif

                    <h1 class="mt-7 max-w-[720px] text-[38px] font-black leading-[1.07] text-[#171d2d] sm:text-5xl lg:text-[58px]">
                        {{ $title }}
                        <span class="mt-2 block text-[#111111]">{{ $titleAccent }}</span>
                    </h1>

                    @if (trim(strip_tags($intro)) !== '')
                        <div class="prose prose-slate mt-7 max-w-2xl text-base leading-8 prose-p:my-4 prose-strong:font-extrabold prose-strong:text-[#171d2d] sm:text-lg">
                            {!! clean_html($intro, 'safe', null, '<p><br><strong><em><ul><ol><li><a>') !!}
                        </div>
                    @endif

                    <div class="mt-9 flex flex-col gap-4 sm:flex-row">
                        <a href="{{ $catalogUrl }}" class="inline-flex min-h-14 items-center justify-center rounded-full bg-black px-8 py-4 text-base font-extrabold text-white shadow-[0_16px_35px_rgba(0,0,0,0.18)] transition hover:-translate-y-1 hover:bg-[#ff7500]">
                            {{ $catalogButton }}
                        </a>
                        <a href="#how-it-works" class="inline-flex min-h-14 items-center justify-center rounded-full border border-black/10 bg-white px-8 py-4 text-base font-extrabold text-[#171d2d] transition hover:-translate-y-0.5 hover:border-[#ff7500] hover:text-[#ff7500]">
                            Як це працює?
                        </a>
                    </div>
                </div>

                <div class="relative mx-auto w-full max-w-[520px]">
                    <div class="rounded-[28px] border border-black/10 bg-white p-6 shadow-[0_28px_70px_rgba(15,23,42,0.10)] sm:p-8">
                        <div class="rounded-[22px] bg-[#111111] p-7 text-white">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="text-2xl font-black">monobank</div>
                                    <div class="mt-1 text-sm text-white/70">Покупка частинами</div>
                                </div>
                                <div class="grid h-12 w-12 place-items-center rounded-2xl bg-white text-xl font-black text-black">m</div>
                            </div>
                            <div class="mt-12 text-4xl font-black leading-tight">3 платежі</div>
                            <div class="mt-3 text-white/70">для замовлень від 2000 грн</div>
                        </div>

                        <div class="mt-5 grid gap-3 sm:grid-cols-3">
                            <div class="rounded-2xl bg-[#f6fff8] p-4 text-center">
                                <strong class="block text-xl font-black">2000+</strong>
                                <span class="mt-1 block text-xs text-slate-500">мінімальна сума</span>
                            </div>
                            <div class="rounded-2xl bg-[#f6fff8] p-4 text-center">
                                <strong class="block text-xl font-black">3</strong>
                                <span class="mt-1 block text-xs text-slate-500">платежі</span>
                            </div>
                            <div class="rounded-2xl bg-[#f6fff8] p-4 text-center">
                                <strong class="block text-xl font-black">app</strong>
                                <span class="mt-1 block text-xs text-slate-500">підтвердження</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        @if (trim(strip_tags($benefitsContent)) !== '')
            <section class="px-4 py-16 sm:px-6 sm:py-20">
                <div class="mx-auto max-w-[1198px]">
                    <h2 class="text-center text-3xl font-black text-[#171d2d] sm:text-4xl">{{ $benefitsTitle }}</h2>
                    <div class="prose prose-lg mt-10 max-w-none rounded-[28px] border border-black/10 bg-white p-6 shadow-[0_18px_50px_rgba(15,23,42,0.07)] prose-headings:font-black prose-li:my-3 prose-strong:text-[#171d2d] marker:text-[#ff7500] sm:p-10">
                        {!! clean_html($benefitsContent, 'safe', null, '<p><br><h2><h3><h4><strong><em><ul><ol><li><a><img>') !!}
                    </div>
                </div>
            </section>
        @endif

        @if (trim(strip_tags($conditionsContent)) !== '')
            <section class="bg-[#111111] px-4 py-16 text-white sm:px-6 sm:py-20">
                <div class="mx-auto max-w-[1198px]">
                    <h2 class="text-center text-3xl font-black sm:text-4xl">{{ $conditionsTitle }}</h2>
                    <div class="prose prose-lg mt-10 max-w-none rounded-[28px] bg-white p-6 text-[#171d2d] prose-li:my-3 prose-strong:text-[#111111] marker:text-[#ff7500] sm:p-10">
                        {!! clean_html($conditionsContent, 'safe', null, '<p><br><h2><h3><h4><strong><em><ul><ol><li><a>') !!}
                    </div>
                </div>
            </section>
        @endif

        @if (trim(strip_tags($stepsContent)) !== '')
            <section id="how-it-works" class="scroll-mt-24 px-4 py-16 sm:px-6 sm:py-20">
                <div class="mx-auto max-w-[1198px]">
                    <h2 class="text-center text-3xl font-black text-[#171d2d] sm:text-4xl">{{ $stepsTitle }}</h2>
                    <div class="prose prose-lg mt-10 max-w-none rounded-[28px] border border-orange-100 bg-[#fffaf3] p-6 prose-ol:space-y-4 prose-li:rounded-2xl prose-li:bg-white prose-li:px-5 prose-li:py-4 prose-li:shadow-sm marker:font-black marker:text-[#ff7500] sm:p-10">
                        {!! clean_html($stepsContent, 'safe', null, '<p><br><h2><h3><h4><strong><em><ul><ol><li><a>') !!}
                    </div>
                </div>
            </section>
        @endif

        @if (trim(strip_tags($faqContent)) !== '')
            <section class="bg-white px-4 py-16 sm:px-6 sm:py-20">
                <div class="prose prose-lg mx-auto max-w-4xl prose-headings:font-black prose-p:leading-8 prose-strong:text-[#171d2d]">
                    {!! clean_html($faqContent, 'safe', null, '<p><br><h2><h3><h4><strong><em><ul><ol><li><a>') !!}
                </div>
            </section>
        @endif

        @if (trim(strip_tags($ctaContent)) !== '')
            <section class="bg-[#ff7500] px-4 py-20 text-center text-white sm:px-6 sm:py-24">
                <div class="prose prose-lg mx-auto max-w-4xl prose-headings:font-black prose-headings:text-white prose-p:text-white/90 prose-strong:text-white">
                    {!! clean_html($ctaContent, 'safe', null, '<p><br><h2><h3><strong><em><a>') !!}
                </div>
            </section>
        @endif
    </div>
@endsection
