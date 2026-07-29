@extends('layouts.app')

@include('partials.seo.page', ['page' => $page])

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

        @include('pages.monobank.hero')
        @include('pages.monobank.benefits')
        @include('pages.monobank.conditions')
        @include('pages.monobank.steps')
        @include('pages.monobank.faq')
        @include('pages.monobank.cta')

    </div>
@endsection