{{--
  Template Name: Политика
--}}

@extends('layouts.app')

@section('content')
    <section class="container_pol page-politics">
        @while (have_posts())
            @php(the_post())
            <h1 class="page-title">{{ get_the_title() }}</h1>

            <div class="page-content">
                {!! apply_filters('the_content', get_the_content()) !!}
            </div>
        @endwhile
    </section>
@endsection
