@extends($theme . 'layouts.app')
@section('title',trans('Home'))
@section('content')
    @include(template().'sections.about')
    @include(template() . 'partials.footer')
@endsection
