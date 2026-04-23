
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

    <link rel="stylesheet" href="{{ asset(template(true).'css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset(template(true).'css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset(template(true).'css/style.css') }}">
</head>
<body>


@yield('content')

<script src="{{ asset(template(true).'js/bootstrap.bundle.min.js') }}"></script>

</body>
</html>
