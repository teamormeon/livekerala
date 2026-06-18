<!-- Nav section start -->
<nav class="navbar fixed-top navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand logo" href="{{ url('/') }}">
            <img id="sitelogo" src="{{ getFile($basicControl->admin_dark_mode_logo_driver, $basicControl->admin_dark_mode_logo, true) }}" alt="...">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar"
                aria-controls="offcanvasNavbar">
            <i class="fa-light fa-list"></i>
        </button>
        <div class="offcanvas offcanvas-end nav-offcanvas" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbar">
            <div class="offcanvas-header">
                <a class="navbar-brand" href="{{ url('/') }}">
                    <img src="{{ getFile(basicControl()->logo_driver,basicControl()->logo) }}" alt="">
                </a>
                <button type="button" class="cmn-btn-close" data-bs-dismiss="offcanvas" aria-label="Close"><i
                        class="fa-light fa-arrow-right"></i></button>
            </div>
            <div class="offcanvas-body align-items-center justify-content-between">
                <ul class="navbar-nav mx-auto">
                    {!! renderHeaderMenu(getHeaderMenuData()) !!}
                </ul>
            </div>
        </div>
        <div class="nav-right">
            <ul class="custom-nav">
                @auth()
                    <li class="nav-item">
                        <button class="get-start-btn" data-bs-toggle="modal" data-bs-target="#addListingmodal">
                            <span><i class="fa-regular fa-circle-plus"></i> </span>
                            <span class="d-none d-md-block">@lang('add listing')</span></button>
                    </li>
                @endauth
                @guest()
                    <li class="nav-item">
                        <a href="{{ route('login') }}" class="login-btn"><i class="fa-regular fa-user"></i><span class="d-none d-md-block"> @lang('Login')</span></a>
                    </li>
                @else
                    <li class="nav-item">
                        <div class="profile-box">
                            <div class="profile">
                                <img src="{{ getFile(auth()->user()->image_driver, auth()->user()->image) }}" class="img-fluid" alt="image">
                            </div>
                            <ul class="user-dropdown">
                                <li>
                                    <a href="{{ route('user.dashboard') }}"> <i class="fa-regular fa-grid"></i> @lang('Dashboard') </a>
                                </li>
                                <li>
                                    <a href="{{ route('user.ticket.list') }}"> <i class="fal fa-user-headset"></i> @lang('Support') </a>
                                </li>
                                <li>
                                    <a href="{{ route('user.profile') }}"> <i class="fal fa-user-cog"></i> @lang('Account Settings')
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"> <i class="fal fa-sign-out-alt"></i> @lang('Sign Out') </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>

<!-- Nav section end -->


<!-- Bottom Mobile Menu -->
<ul class="nav bottom-nav fixed-bottom d-lg-none">
    <li class="nav-item">
        <button class="nav-link navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar"
                aria-controls="offcanvasNavbar">
            <i class="far fa-list"></i>
        </button>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->is('blogs')) active @endif" href="{{ route('blogs') }}"><i class="far fa-planet-ringed"></i></a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->is('/')) active @endif" href="{{ url('/') }}"><i class="far fa-house"></i></a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->is('contact')) active @endif" href="{{ url('/contact') }}"><i class="far fa-address-book"></i></a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('user.profile')) active @endif" href="{{ route('user.profile') }}"><i class="far fa-user"></i></a>
    </li>
</ul>
<!-- Bottom Mobile Menu -->

