@extends(front_view('layouts.app'))

@include(front_view('partials.seo.page'), ['page' => $page])

@section('full_width', 'true')
@section('page', 'privatbank-installments')

@php
    $pageSlug = 'oplata-chastynamy-pryvatbank';

    $locale = app()->getLocale();

    $defaultCatalogUrl = $locale === 'uk'
        ? '/pies'
        : '/' . $locale . '/pies';

    $catalogUrl = strip_tags(
        page_field($pageSlug, 'pb_catalog_url', $defaultCatalogUrl) ?? $defaultCatalogUrl
    );

    $catalogButton = strip_tags(
        page_field($pageSlug, 'pb_catalog_button', 'Перейти до каталогу')
        ?? 'Перейти до каталогу'
    );
@endphp

@section('content')
    <div class="overflow-hidden bg-[#f8faf5] text-[#171d2d]">

        @include(front_view('pages.privatbank.hero'))

        @include(front_view('pages.privatbank.benefits'))

        @include(front_view('pages.privatbank.conditions'))

        @include(front_view('pages.privatbank.steps'))

        @include(front_view('pages.privatbank.faq'))

        @include(front_view('pages.privatbank.cta'))

    </div>
@endsection
