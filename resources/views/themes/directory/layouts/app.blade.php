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

    <link rel="stylesheet" href="{{ asset(template(true).'css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset(template(true).'css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/css/bootstrap-icons.css') }}">

    <link rel="stylesheet" href="{{ asset(template(true).'css/owl.carousel.min.css') }}">
    <link rel="stylesheet" href="{{ asset(template(true).'css/owl.theme.default.min.css') }}">

    <link rel="stylesheet" href="{{ asset(template(true).'css/slick.css') }}">
    <link rel="stylesheet" href="{{ asset(template(true).'css/slick-theme.css') }}">
    <link rel="stylesheet" href="{{ asset(template(true).'css/swiper-bundle.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/global/css/select2.min.css') }}"/>
    <link rel="stylesheet" href="{{ asset(template(true).'css/fancybox.css') }}">
    <link rel="stylesheet" href="{{ asset(template(true).'css/style.css') }}">
    @stack('css-lib')
    @stack('style')
</head>
<body onload="preloaderFunction()">
    <!-- Preloader section start -->
    <div id="preloader">
        <div class="loader"></div>
    </div>
    <!-- Preloader section end -->
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


    <script src="{{asset('assets/global/js/jquery.min.js') }}"></script>
    <script src="{{ asset(template(true).'js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset(template(true).'js/owl.carousel.min.js') }}"></script>

    <script src="{{ asset(template(true).'js/swiper-bundle.min.js') }}"></script>
    <script src="{{ asset(template(true).'js/slick.min.js') }}"></script>
    <script src="{{ asset('assets/global/js/select2.min.js') }}"></script>
    <script src="{{ asset(template(true).'js/fancybox.umd.js') }}"></script>
    <script src="{{ asset(template(true).'js/jquery.counterup.min.js') }}"></script>
    <script src="{{ asset(template(true).'js/jquery.waypoints.min.js') }}"></script>

    @stack('extra-js')

    <script src="{{asset('assets/global/js/notiflix-aio-3.2.6.min.js')}}"></script>
    <script src="{{asset('assets/global/js/pusher.min.js')}}"></script>
    <script src="{{asset('assets/global/js/vue.min.js')}}"></script>
    <script src="{{asset('assets/global/js/axios.min.js')}}"></script>
    <script src="{{ asset(template(true).'js/main.js') }}"></script>
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


    <script>
        var fixed_top = $(".navbar.fixed-top");
        $(window).on("scroll", function () {
            if ($(window).scrollTop() > 90) {
                fixed_top.addClass("show");
                document.getElementById('sitelogo').src = "{{ getFile(basicControl()->logo_driver,basicControl()->logo) }}"
            } else {
                document.getElementById('sitelogo').src = "{{ getFile($basicControl->admin_dark_mode_logo_driver, $basicControl->admin_dark_mode_logo, true) }}"
                fixed_top.removeClass("show");
            }
        });
    </script>

</body>
</html>
