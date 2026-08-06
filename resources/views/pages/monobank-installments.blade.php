@extends(front_view('layouts.app'))

@include(front_view('partials.seo.page'), ['page' => $page])

@section('full_width', 'true')
@section('page', 'monobank-installments')

@php
    $pageSlug = 'pokupka-chastynamy-monobank';
    $locale = app()->getLocale();

    $defaultCatalogUrl = $locale === 'uk'
        ? '/pies'
        : '/' . $locale . '/pies';

    $catalogUrl = strip_tags(
        page_field($pageSlug, 'mono_catalog_url', $defaultCatalogUrl)
        ?? $defaultCatalogUrl
    );

    $catalogButton = strip_tags(
        page_field(
            $pageSlug,
            'mono_catalog_button',
            'Перейти до каталогу'
        ) ?? 'Перейти до каталогу'
    );
@endphp

@section('content')
    <div class="overflow-hidden bg-[#f6f6f7] text-[#121212]">

        @include(front_view('pages.monobank.hero'))
        @include(front_view('pages.monobank.product-info'))
        @include(front_view('pages.monobank.benefits'))
        @include(front_view('pages.monobank.conditions'))
        @include(front_view('pages.monobank.steps'))
        @include(front_view('pages.monobank.faq'))
        @include(front_view('pages.monobank.cta'))

    </div>
@endsection