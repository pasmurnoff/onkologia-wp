@extends('layouts.app')

@section('content')
    <div class="container sections-wrapper">

        @include('components.search.wrap')
        @include('components.category-sections.wrap')
        @include('components.latest-topics-section.wrap')
        <?php render_latest_topics_section(); ?>
        <!--  include __DIR__ . ?/partials/search.php?;-->
        <!--  include __DIR__ . ?/partials/categories-section.php?;-->
        <!--  include __DIR__ . ?/partials/latest-topics-section.php?;-->
        <!--  render_latest_topics_section();-->

    </div>
@endsection
