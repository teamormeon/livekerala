<!DOCTYPE html>
<html class="no-js" lang="en" @if(session()->get('rtl') == 1) dir="rtl" @endif >
<head>
    <meta charset="utf-8"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link href="{{ getFile(basicControl()->favicon_driver, basicControl()->favicon) }}" rel="icon">
    <title>@lang(basicControl()->site_title) | @if(isset($pageSeo['page_title']))
            @lang($pageSeo['page_title'])
        @else
            @yield('title')
        @endif
    </title>
    @include(template().'partials.seo')

    <!-- bootstrap 5 -->
    <link rel="stylesheet" href="{{ asset('assets/global/css/bootstrap5.min.css') }}"/>
    <!-- select 2 -->
    <link rel="stylesheet" href="{{ asset('assets/global/css/select2.min.css') }}"/>
    <!-- owl carousel -->
    <link rel="stylesheet" href="{{ asset(template(true).'css/animate.css') }}"/>
    <link rel="stylesheet" href="{{ asset(template(true).'css/owl.carousel.min.css') }}"/>
    <link rel="stylesheet" href="{{ asset(template(true).'css/owl.theme.default.min.css') }}"/>
    <!-- range slider -->
    <link rel="stylesheet" href="{{ asset(template(true).'css/range-slider.css') }}"/>
    <!-- magnific popup -->
    <link rel="stylesheet" href="{{ asset(template(true).'css/magnific-popup.css') }}"/>
    <!-- font awesome 5 -->
    <script src="{{ asset('assets/global/js/fontawesomepro.js') }}"></script>
    <!-- fancybox slider -->
    <link rel="stylesheet" href="{{ asset(template(true).'css/fancybox.css') }}"/>
    <link rel="stylesheet" href="{{ asset('assets/global/css/bootstrap-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/global/css/bootstrapicons-iconpicker.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/global/css/fontawesome.min.css') }}">
    <!-- custom css -->
    <link rel="stylesheet" href="{{ asset(template(true).'css/style.css') }}"/>
    @if(request()->is('/'))
        <link rel="stylesheet" href="{{ asset(template(true).'css/home-modern.css') }}"/>
    @endif
    @stack('css-lib')
    <!----  Push your custom css  ----->
    @stack('style')
</head>
<body @if(session()->get('rtl') == 1) class="rtl" @endif >
@include(template().'partials.header')

@include(template().'partials.banner')

@yield('content')

@if(basicControl()->cookie_status == 1)
    @include(template().'partials.cookie')
@endif
@include(template().'partials.footer')

@yield('whatsapp_chat')

@yield('fb_messenger_chat')


@stack('extra-content')

@stack('frontend_modal')

<!-- bootstrap -->
<script src="{{ asset(template(true).'js/bootstrap.bundle.min.js') }}"></script>

<!-- jquery cdn -->
<script src="{{asset('assets/global/js/jquery.min.js') }}"></script>


<!-- select 2 -->
<script src="{{ asset('assets/global/js/select2.min.js') }}"></script>
<!-- owl carousel -->
<script src="{{ asset(template(true).'js/owl.carousel.min.js') }}"></script>
<!-- range slider -->
<script src="{{ asset(template(true).'js/range-slider.min.js') }}"></script>
<!-- leaflet js -->
<script src="{{ asset('assets/global/js/leaflet.js') }}"></script>
<!-- social share -->
<script src="{{ asset(template(true).'js/socialSharing.js') }}"></script>
<!-- magnific popup -->
<script src="{{ asset(template(true).'js/magnific-popup.js') }}"></script>


@stack('extra-js')


<script src="{{asset('assets/global/js/notiflix-aio-3.2.6.min.js')}}"></script>
<script src="{{asset('assets/global/js/pusher.min.js')}}"></script>
<script src="{{asset('assets/global/js/vue.min.js')}}"></script>
<script src="{{asset('assets/global/js/axios.min.js')}}"></script>

<script src="{{ asset(template(true).'js/script.js') }}"></script>
@stack('script')

@include(template().'partials.notification')

@if ($errors->any())
    @php
        $collection = collect($errors->all());
        $errors = $collection->unique();
    @endphp
    <script>
        "use strict";
        @foreach ($errors as $error)
        Notiflix.Notify.failure("{{trans($error)}}");
        @endforeach
    </script>
@endif

@include('plugins')

</body>
</html>
