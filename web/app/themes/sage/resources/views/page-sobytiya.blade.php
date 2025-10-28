{{--
Template Name: Все события  
--}}
@extends('layouts.app')

@php
    // Параметры запроса: последние посты, 9 на страницу
    $paged = get_query_var('paged') ? (int) get_query_var('paged') : 1;

    $events = new WP_Query([
        'post_type' => 'post',
        'posts_per_page' => 9,
        'paged' => $paged,
        'ignore_sticky_posts' => true,
    ]);
@endphp

@section('content')
    <section class="events container_rg">
        <h1 class="page-title">{{ get_the_title() }} helllow</h1>

    </section>
@endsection
