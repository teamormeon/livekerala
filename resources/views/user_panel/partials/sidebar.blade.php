@php use App\Models\ProductQuery; @endphp
<!-- sidebar -->
<div id="sidebar" class="">
    <div class="sidebar-top">
        <a class="navbar-brand" href="{{url('/')}}">
            <img src="{{ getFile(basicControl()->logo_driver,basicControl()->logo) }}" alt="logo">
        </a>
        <button
            class="sidebar-toggler d-lg-none"
            onclick="toggleSideMenu()">
            <i class="fal fa-times"></i>
        </button>
    </div>
    <ul class="main tabScroll">
        <li>
            <a class="{{(lastUriSegment() == 'dashboard') ? 'active' : ''}}" href="{{ route('user.dashboard') }}"
            ><i class="fal fa-th-large text-success"></i>@lang('Dashboard')</a>
        </li>

        <li>
            <a href="{{ route('user.myPackages') }}" class="{{ request()->routeIs('user.myPackages', 'user.paymentHistory') ? 'active' : ''}}">
                <i class="fal fa-box-full text-primary"></i>@lang('My Packages')
            </a>
        </li>

        <li>
            @php
                $id = '';
            @endphp
            <a href="{{ route('user.listings') }}"
               class="{{ (lastUriSegment() == 'listings' || lastUriSegment() == 'pending' || lastUriSegment() == 'approved' || lastUriSegment() == 'rejected' || request()->routeIs(['user.addListing','user.editListing','user.reviews','user.dynamic.form.data','user.listing.import.csv'])) ? 'active' : ''}}">
                <i class="fal fa-list-ol text-orange"></i>@lang('My Listings')
            </a>
        </li>

        <li>
            <a href="{{ route('user.wish.list') }}" class="{{ request()->routeIs('user.wish.list') ? 'active' : ''}}">
                <i class="fal fa-heart text-cyan"></i> @lang('WishList')
            </a>
        </li>

        <li>
            <a href="{{ route('user.claim.business.list','customer-claim') }}" class="{{ request()->routeIs(['user.claim.business.list','user.claim.business.conversation']) ? 'active' : ''}}">
                <i class="fal fa-gavel text-orange"></i> @lang('Claim Business')
            </a>
        </li>

        <li>
            <a href="{{ route('user.product.queries','customer-enquiry') }}"
               class="{{ request()->routeIs('user.product.queries','user.product.query.reply') ? 'active' : '' }}">
                <i class="fal fa-question text-orange"></i> @lang('Product Enquiries')
                @php
                    $customerEnquiry = ProductQuery::where('user_id', auth()->id())->where('customer_enquiry', 0)->count();
                    $myEnquiry = ProductQuery::whereHas('unseenReplies')->where('client_id', auth()->id())->count();
                @endphp
                @if($customerEnquiry > 0 || $myEnquiry > 0)
                    <sup class="text-danger custom__queiry_count"> <span class="badge bg-primary rounded-circle">{{ $customerEnquiry + $myEnquiry }}</span> </sup>
                @endif
            </a>
        </li>

        <li>
            <a href="{{ route('user.transaction') }}" class="{{ request()->routeIs('user.transaction') ? 'active' : ''}}">
                <i class="fal fa-sack-dollar text-pink"></i>@lang('Transaction')
            </a>
        </li>

        <li>
            <a href="{{ route('user.analytics') }}" class="{{ request()->routeIs(['user.analytics','user.analytics.show']) ? 'active' : ''}}">
                <i class="fal fa-analytics text-green"></i>@lang('Analytics')
            </a>
        </li>

        <li>
            <a href="{{route('user.profile')}}" class="{{(lastUriSegment() == 'profile') ? 'active' : ''}}">
                <i class="fal fa-users-cog text-indigo"></i> @lang('Profile Settings')
            </a>
        </li>

        <li>
            <a href="{{route('user.ticket.list')}}" class="{{ request()->routeIs('user.ticket.list','user.ticket.create','user.ticket.view') ? 'active' : ''}}">
                <i class="fal fa-user-headset text-success"></i> @lang('support ticket')
            </a>
        </li>

        <li class="">
            <a href="{{route('user.twostep.security')}}" class="{{ request()->routeIs('user.twostep.security') ? 'active' : ''}}">
                <i class="fal fa-lock text-orange"></i> @lang('2FA Security')
            </a>
        </li>

        <li class="">
            <a href="{{route('user.notification.permission')}}" class="{{ request()->routeIs('user.notification.permission') ? 'active' : ''}}">
                <i class="far fa-bell text-orange"></i> @lang('Notification Settings')
            </a>
        </li>

        <li class="">
            <a href="{{ route('logout') }}"
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fal fa-sign-out-alt text-purple"></i> @lang('Sign Out')
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </a>
        </li>

    </ul>
</div>


<!-- Bottom Mobile Menu -->
<ul class="nav bottom-nav fixed-bottom d-lg-none">
    <li class="nav-item">
        <button type="button" class="nav-link sidebar-toggler d-lg-none" onclick="toggleSideMenu()">
            <i class="far fa-list"></i>
        </button>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->is('blogs')) active @endif" href="{{ route('blogs') }}"><i class="far fa-planet-ringed"></i></a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('user.dashboard')) active @endif" href="{{ route('user.dashboard') }}"><i class="far fa-house"></i></a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->is('contact')) active @endif" href="{{ url('/contact') }}"><i class="far fa-address-book"></i></a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('user.profile')) active @endif" href="{{ route('user.profile') }}"><i class="far fa-user"></i></a>
    </li>
</ul>
<!-- Bottom Mobile Menu -->
