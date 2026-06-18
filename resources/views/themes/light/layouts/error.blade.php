
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
    <!-- font awesome 5 -->
    <script src="{{ asset('assets/global/js/fontawesomepro.js') }}"></script>
    <!-- custom css -->
    <link rel="stylesheet" href="{{ asset(template(true).'css/style.css') }}"/>
    @stack('css-lib')
    <!----  Push your custom css  ----->
    @stack('style')
</head>
<body @if(session()->get('rtl') == 1) class="rtl" @endif >

@yield('content')


<!-- bootstrap -->
<script src="{{ asset(template(true).'js/bootstrap.bundle.min.js') }}"></script>

</body>
</html>
