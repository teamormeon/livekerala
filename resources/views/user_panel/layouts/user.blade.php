<!DOCTYPE html>
<!--[if lt IE 7 ]>
<html class="ie ie6" lang="en"> <![endif]-->
<!--[if IE 7 ]>
<html class="ie ie7" lang="en"> <![endif]-->
<!--[if IE 8 ]>
<html class="ie ie8" lang="en"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!-->
<html class="no-js" lang="en" @if(session()->get('rtl') == 1) dir="rtl" @endif >
<head>
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta http-equiv='X-UA-Compatible' content='IE=edge,chrome=1'/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{ getFile(basicControl()->favicon_driver, basicControl()->favicon) }}" rel="icon">
    <title>@lang(basicControl()->site_title) | @if(isset($pageSeo['page_title']))
            @lang($pageSeo['page_title'])
        @else
            @yield('title')
        @endif
    </title>

{{--    @include('partials.seo')--}}

    <title>@yield('title')</title>

    <!-- bootstrap 5 -->
    <link rel="stylesheet" href="{{ asset('assets/global/css/bootstrap5.min.css') }}"/>

    <!-- jquery ui -->
    <link rel="stylesheet" href="{{ asset('assets/global/css/jquery-ui.css') }}"/>

    <!-- radial progress -->
    <link rel="stylesheet" href="{{ asset('assets/global/css/radialprogress.css') }}"/>

    <!-- select 2 -->
    <link rel="stylesheet" href="{{ asset('assets/global/css/select2.min.css') }}"/>

    <!-- leaflet -->
    <link rel="stylesheet" href="{{ asset('assets/global/css/esri-leaflet-geocoder.css') }}"/>

    <link rel="stylesheet" href="{{ asset('assets/global/css/leaflet-search.css') }}"/>

    <link rel="stylesheet" href="{{ asset('assets/global/css/leaflet.css') }}"/>
    <link rel="stylesheet" href="{{ asset('assets/global/css/Control.FullScreen.css') }}"/>

    <link rel="stylesheet" href="{{asset('assets/global/css/owl.carousel.min.css')}}" />
    <link rel="stylesheet" href="{{asset('assets/global/css/owl.theme.default.min.css')}}" />

    <!-- font awesome 5 -->
    <script src="{{ asset('assets/global/js/fontawesomepro.js') }}"></script>


    <!-- custom css -->
    <link rel="stylesheet" href="{{ asset('assets/global/css/leaflet-search-two.css') }}"/>
    <link rel="stylesheet" href="{{ asset('assets/global/css/user-style.css') }}"/>

    @stack('css-lib')
    <!----  Push your custom css  ----->
    @stack('style')
</head>

<body @if(session()->get('rtl') == 1) class="rtl" @endif >

<div class="dashboard-wrapper">

    @include('user_panel.partials.sidebar')

    <!-- content -->
    <div id="content">
        <div class="overlay">
            <!-- navbar -->
            <nav class="navbar navbar-expand-lg">
                <div class="container-fluid">
                    <a class="navbar-brand d-lg-none d-none" href="{{ url('/') }}">
                        <img src="{{ getFile(basicControl()->logo_driver, basicControl()->logo) }}"
                             alt="{{ basicControl()->site_title }}">
                    </a>

                    <button class="sidebar-toggler" onclick="toggleSideMenu()">
                        <i class="fal fa-bars"></i>
                    </button>

                    <span class="navbar-text">
                    <!-- notification panel -->
                    @include('user_panel.partials.pushNotify')
                        <!-- user panel -->
                    <div class="user-panel">
                       <span class="profile">
                          <img
                              src="{{ getFile(Auth::user()->image_driver, Auth::user()->image) }}"
                              class="img-fluid"
                              alt="image"
                          />
                       </span>
                       <ul class="user-dropdown">
                           <li>
                                <a href="{{route('user.dashboard')}}">
                                    <i class="fal fa-border-all"></i> {{trans('Dashboard')}}
                                </a>
                            </li>

                          <li>
                             <a href="{{ route('user.profile') }}">
                                <i class="fal fa-user"></i> @lang('Profile')
                             </a>
                          </li>

                           <li>
                                <a href="{{route('user.twostep.security')}}">
                                    <i class="fal fa-lock"></i> @lang('2FA Security')
                                </a>
                            </li>

                          <li>
                             <a href="{{ route('logout') }}"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fal fa-sign-out-alt"></i> @lang('Sign Out')
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                   @csrf
                               </form>
                             </a>
                          </li>
                       </ul>
                    </div>
                 </span>
                </div>
            </nav>
            @yield('content')
        </div>
    </div>

</div>

@stack('loadModal')

@stack('extra-content')
<!-- bootstrap 5-->
<script src="{{ asset('assets/global/js/bootstrap.bundle.min.js') }}"></script>

<!-- jquery cdn -->
{{--<script src="{{asset('assets/global/js/jquery.min.js') }}"></script>--}}
<script src="{{asset('assets/global/js/jquery-3.7.1.min.js') }}"></script>
<!-- jquery ui -->
<script src="{{ asset('assets/global/js/jquery-ui.js') }}"></script>

<!-- radial progress -->
<script src="{{ asset('assets/global/js/radialprogress.js') }}"></script>

<!-- select 2 -->
<script src="{{ asset('assets/global/js/select2.min.js') }}"></script>

<!-- leaflet -->
<script src="{{ asset('assets/global/js/leaflet.js') }}"></script>
<script src="{{ asset('assets/global/js/Control.FullScreen.js') }}"></script>
<script src="{{ asset('assets/global/js/esri-leaflet.js') }}"></script>
<script src="{{ asset('assets/global/js/leaflet-search.js') }}"></script>
<script src="{{ asset('assets/global/js/esri-leaflet-geocoder.js') }}"></script>
<script src="{{ asset('assets/global/js/bootstrap-geocoder.js') }}"></script>

<script src="{{asset('assets/global/js/notiflix-aio-3.2.6.min.js')}}"></script>
<script src="{{asset('assets/global/js/pusher.min.js')}}"></script>
<script src="{{asset('assets/global/js/vue.min.js')}}"></script>
<script src="{{asset('assets/global/js/axios.min.js')}}"></script>
<script src="{{asset('assets/global/js/owl.carousel.min.js')}}"></script>


@stack('extra-js')

<!-- custom script -->
<script src="{{ asset('assets/global/js/user-script.js') }}"></script>

@include('plugins', ['fromUser' => true])

@auth
<script>
    'use strict';
    $(".card-boxes").owlCarousel({
        loop: true,
        margin: -25,
        rtl: false,
        nav: false,
        dots: false,
        autoplay: false,
        autoplayTimeout: 3000,
        responsive: {
            0: {
                items: 1,
            },
            576: {
                items: 2,
            },
        },
    });

    window.Laravel = <?php echo json_encode([
        'csrfToken' => csrf_token(),
    ]); ?>;
    var module = {};

    let pushNotificationArea = new Vue({
        el: "#pushNotificationArea",
        data: {
            items: [],
        },
        mounted() {
            this.getNotifications();
            this.pushNewItem();
        },
        methods: {
            getNotifications() {
                let app = this;
                axios.get("{{ route('user.push.notification.show') }}")
                    .then(function (res) {
                        app.items = res.data;
                    })
            },
            readAt(id, link) {
                let app = this;
                let url = "{{ route('user.push.notification.readAt', 0) }}";
                url = url.replace(/.$/, id);
                axios.get(url)
                    .then(function (res) {
                        if (res.status) {
                            app.getNotifications();
                            if (link != '#') {
                                window.location.href = link
                            }
                        }
                    })
            },
            readAll() {
                let app = this;
                let url = "{{ route('user.push.notification.readAll') }}";
                axios.get(url)
                    .then(function (res) {
                        if (res.status) {
                            app.items = [];
                        }
                    })
            },
            pushNewItem() {
                let app = this;
                let pusher = new Pusher("{{ env('PUSHER_APP_KEY') }}", {
                    encrypted: true,
                    cluster: "{{ env('PUSHER_APP_CLUSTER') }}"
                });
                let channel = pusher.subscribe('user-notification.' + "{{ Auth::id() }}");
                channel.bind('App\\Events\\UserNotification', function (data) {
                    app.items.unshift(data.message);
                });
                channel.bind('App\\Events\\UpdateUserNotification', function (data) {
                    app.getNotifications();
                });
            }
        }
    });
</script>
@endauth
@stack('script')

@include(template().'partials.notification')

</body>
</html>
