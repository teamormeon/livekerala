@extends(template().'layouts.app')
@section('title',trans('Profile'))

@section('banner_heading')
    @lang('Profile')
@endsection

@section('content')
    <section class="author-section">
        <div class="container">
            <div class="author-box2">
                <div class="user-info">
                    <div class="img-box">
                        <img src="{{ getFile(optional($user_information)->image_driver, optional($user_information)->image) }}" alt="image">
                    </div>
                    <div class="text-box">
                        <div>
                            <h4>
                                @lang($user_information->firstname) @lang($user_information->lastname)
                                @if($user_information->identity_verify ==  2 && $user_information->address_verify ==  2)
                                    <i class="fas fa-check-circle" aria-hidden="true"></i>
                                @endif
                            </h4>
                            @if($user_information->bio)
                                <p class="mb-0">@lang($user_information->bio)</p>
                            @endif
                        </div>

                        @if(count($user_information->get_social_links_user) > 0)
                            <ul class="social-box justify-content-center">
                                @foreach($user_information->get_social_links_user as $social_link)
                                    <li>
                                        <a href="{{ $social_link->social_url }}" target="_blank">
                                            <i class="{{ $social_link->social_icon }}"></i>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @endif

                        <div class="user-meta">
                            @if($user_information->fullAddress)
                                <div class="item">
                                    <i class="fa-regular fa-location-dot"></i>
                                    @lang($user_information->fullAddress)
                                </div>
                            @endif
                            @if($user_information->website)
                                <a href="{{ $user_information->website }}" class="item">
                                    <i class="fa-regular fa-globe"></i>
                                    @lang($user_information->website)
                                </a>
                            @endif
                            <div class="item">
                                <i class="fa-regular fa-calendar-days"></i>
                                @lang('Joined '){{ dateTime($user_information->created_at) }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="user-info-bottom">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="mt-auto">
                                <div class="counts">
                                    <div class="count">
                                        @lang('Listing')
                                        <span>{{ count($user_information->get_listing) }}</span>
                                    </div>
                                    <div class="count">
                                        @lang('Total Views')
                                        <span>{{ $user_information->total_views_count }}</span>
                                    </div>
                                    <div class="count">
                                        @lang('Follower')
                                        <span>{{ count($user_information->follower) }}</span>
                                    </div>
                                    <div class="count">
                                        @lang('Following')
                                        <span>{{ count($user_information->following) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div id="user_copy_id">
                                @php
                                    $profileUrl = url()->current();
                                @endphp
                                <input type="text" class="form-control copyText copy__profile__url opacity-0" value="{{ $profileUrl }}" readonly>
                                <div class="d-flex justify-content-center justify-content-md-end gap-3 flex-wrap">
                                    <button class="cmn-btn3 copy-btn" data-link="{{ $profileUrl }}">@lang('Copy Profile') <i class="fa-regular fa-copy"></i></button>
                                    <button class="cmn-btn4 share">
                                        <i class="fa-regular fa-share-nodes"></i>
                                        <div id="shareBlock"></div>
                                    </button>

                                    @if(Auth::check())
                                        @if(count($check_follower) < 1)
                                            <form action="{{ route('user.profile.follow', $user_information->id) }}" method="post" class="d-inline-block">
                                                @csrf
                                                <button type="submit" class="cmn-btn follow-btn disabled"><i class="fa-regular fa-plus"></i> @lang('follow')</button>
                                            </form>
                                        @else
                                            <form action="{{ route('user.profile.unfollow', $user_information->id) }}" method="post" class="d-inline-block">
                                                @csrf
                                                <button type="submit" class="cmn-btn follow-btn disabled"><i class="fa-regular fa-minus"></i> @lang('unFollow')</button>
                                            </form>
                                        @endif
                                    @else
                                        <form action="{{ route('user.profile.follow', $user_information->id) }}" method="post" class="d-inline-block">
                                            @csrf
                                            <button type="submit" class="cmn-btn follow-btn disabled"><i class="fa-regular fa-plus"></i> @lang('Follow')</button>
                                        </form>
                                    @endif
                                </div>
                            </div>

                        </div>

                    </div>
                </div>
            </div>

            <div class="row g-4 g-sm-5 mt-50">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body">
                            <h4>@lang('Followers')</h4>
                            @forelse($user_information->follower as $follower)
                                <div class="followers mt-10">
                                    <div class="follower">
                                        <a class="profile-img-link" href="{{ route('profile', optional($follower->get_follwer_user)->username) }}">
                                            <img src="{{ getFile(optional($follower->get_follwer_user)->image_driver, optional($follower->get_follwer_user)->image) }}" alt="image">
                                        </a>
                                        <div class="creator">
                                            <div class="img-box">
                                                <img class="cover" src="{{ getFile(optional($follower->get_follwer_user)->cover_image_driver, optional($follower->get_follwer_user)->cover_image) }}" alt="image">
                                                <img class="profile-img" src="{{ getFile(optional($follower->get_follwer_user)->image_driver, optional($follower->get_follwer_user)->image) }}" alt="">
                                            </div>
                                            <div class="text-box mt-10">
                                                <h5 class="mb-0"><a href="{{ route('profile', optional($follower->get_follwer_user)->username) }}">
                                                        @lang(optional($follower->get_follwer_user)->firstname) @lang(optional($follower->get_follwer_user)->lastname)
                                                    </a>
                                                </h5>
                                                <p class="mb-0">@lang('Member since') {{ dateTime(optional($follower->get_follwer_user)->created_at) }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="d-flex justify-content-center">@lang('No Followers')</div>
                            @endforelse
                        </div>
                    </div>
                    <div class="card mt-30">
                        <div class="card-body">
                            <h4>@lang('Following')</h4>
                            <div class="followers mt-10">
                                @forelse($user_information->following as $following)
                                    <div class="follower">
                                        <a class="profile-img-link" href="{{ route('profile', optional($following->get_following_user)->username) }}">
                                            <img src="{{ getFile(optional($following->get_following_user)->image_driver, optional($following->get_following_user)->image) }}" alt="">
                                        </a>
                                        <div class="creator">
                                            <div class="img-box">
                                                <img class="cover" src="{{ getFile(optional($following->get_following_user)->cover_image_driver, optional($following->get_following_user)->cover_image) }}" alt="">
                                                <img class="profile-img" src="{{ getFile(optional($following->get_following_user)->image_driver, optional($following->get_following_user)->image) }}" alt="">
                                            </div>
                                            <div class="text-box mt-10">
                                                <h5 class="mb-0">
                                                    <a href="{{ route('profile', optional($following->get_following_user)->username) }}">
                                                        @lang(optional($following->get_following_user)->firstname) @lang(optional($following->get_following_user)->lastname)
                                                    </a>
                                                </h5>
                                                <p class="mb-0">@lang('Member since') {{ dateTime(optional($following->get_following_user)->created_at) }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="d-flex justify-content-center">@lang('No Followings')</div>
                                @endforelse

                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="sidebar-widget-area">
                        <h5 class="title">@lang('Contact Creator')</h5>
                        <form action="{{ route('user.viewer.send.message.to.user', $user_information->id) }}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="row g-4">
                                <div class="col-12">
                                    <input class="form-control @error('name') is-invalid @enderror" type="text" autocomplete="off" name="name"
                                           @if(Auth::check())
                                               @if(Auth::id() == $user_information->id)
                                                   placeholder="@lang('Full Name')"
                                           @else
                                               value="@lang(Auth::user()->firstname) @lang(Auth::user()->lastname)"
                                           @endif
                                           @else
                                               placeholder="@lang('Full Name')"
                                        @endif />
                                    <div class="invalid-feedback">
                                        @error('name') @lang($message) @enderror
                                    </div>
                                </div>
                                <div class="col-12">
                                    <textarea class="form-control @error('message') is-invalid @enderror" cols="30" rows="3" autocomplete="off" name="message" placeholder="@lang('Your message')"></textarea>
                                    <div class="invalid-feedback">
                                        @error('message') @lang($message) @enderror
                                    </div>
                                </div>
                                <div class="col-12">
                                    <button class="cmn-btn w-100">@lang('Send Message Now')</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            @if(count($latest_listings) > 0)
                <div class="card mt-30">
                    <div class="card-header">
                        <div class="title">
                            <h4 class="mb-0">@lang('Listings')</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            @forelse($latest_listings as $key => $listing)
                                @php
                                    $total = $listing->reviews()[0]->total;
                                    $average_review = $listing->reviews()[0]->average;
                                @endphp
                                <div class="col-4">
                                    <div class="item">
                                        <div class="listing-box">
                                            <div class="rate-area">
                                                <a href="javascript:void(0)" class="item wishList" id="{{$key}}"
                                                   data-user="{{ optional($listing->get_user)->id }}"
                                                   data-purchase="{{ $listing->purchase_package_id }}"
                                                   data-listing="{{ $listing->id }}">
                                                    @if($listing->get_favourite_count > 0)
                                                        <i class="fa-solid fa-heart text-danger save{{$key}}"></i>
                                                    @else
                                                        <i class="fa-regular fa-heart save{{$key}}"></i>
                                                    @endif
                                                </a>
                                            </div>
                                            <div class="image-area">
                                                <a href="{{ route('listing.details', $listing->slug) }}"> <img
                                                        src="{{ getFile($listing->thumbnail_driver, $listing->thumbnail) }}" alt="image"></a>
                                            </div>
                                            <div class="content-area">
                                                <div class="meta-data">
                                                    <div class="review">
                                                        <ul class="reviews d-flex align-items-center gap-2">
                                                            <li>
                                                                @php
                                                                    $isCheck = 0;
                                                                    $j = 0;
                                                                @endphp
                                                                @if($average_review != intval($average_review))
                                                                    @php
                                                                        $isCheck = 1;
                                                                    @endphp
                                                                @endif

                                                                @for($i = $average_review; $i > $isCheck; $i--)
                                                                    <i class="fas fa-star" aria-hidden="true"></i>
                                                                    @php
                                                                        $j = $j + 1;
                                                                    @endphp
                                                                @endfor

                                                                @if($average_review != intval($average_review))
                                                                    <i class="fas fa-star-half-alt"></i>
                                                                    @php
                                                                        $j = $j + 1;
                                                                    @endphp
                                                                @endif

                                                                @if($average_review == 0 || $average_review != null)
                                                                    @for($j; $j < 5; $j++)
                                                                        <i class="far fa-star"></i>
                                                                    @endfor
                                                                @endif
                                                            </li>
                                                            <span>(@lang($total.' reviews'))</span>
                                                        </ul>
                                                    </div>
                                                </div>
                                                <h5 class="title">
                                                    <a href="{{ route('listing.details', $listing->slug) }}">@lang($listing->title)</a>
                                                </h5>
                                                <div class="mt-15">
                                                    <p class=" mb-1 contact-item"><i class="fa-regular fa-location-dot"></i>
                                                        @lang(optional($listing->get_cities)->getAddress() ?? $listing->address)
                                                    </p>

                                                    <p class="contact-item"><i class="fa-regular fa-phone"></i> {{ $listing->phone }}
                                                    </p>
                                                    <a href="{{ route('profile', optional($listing->get_user)->username) }}" class="contact-item"><i class="fa-regular fa-user"></i>
                                                        @lang(optional($listing->get_user)->firstname) @lang(optional($listing->get_user)->lastname)
                                                    </a>
                                                </div>
                                                <hr class="cmn-hr2">
                                                <div class="bottom-info">
                                                    <a href="{{ route('listing.details', $listing->slug) }}" class="title">
                                                        <i class="fa-regular fa-eye"></i> @lang('View details')
                                                    </a>
                                                    <p class="mb-0 contact-item"><i class="fa-regular fa-calendar-days"></i>
                                                        {{ dateTime($listing->created_at) }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="d-flex justify-content-center">
                                    <span class="text-center">@lang('No Data Found')</span>
                                </div>
                            @endforelse

                        </div>
                        <div class="d-flex justify-content-center mt-4">
                            {{ $latest_listings->links(template().'partials.pagination') }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </section>
@endsection

@push('extra-js')
    <script src="{{ asset(template(true).'js/socialSharing.js') }}"></script>
@endpush

@push('script')
    <script>
        'use strict';
        var isAuthenticate = '{{Auth::check()}}';

        $(document).on('click', '.copy-btn', function () {
            var _this = $(this);
            var copyText = _this.closest('#user_copy_id').find('input.copy__profile__url');

            navigator.clipboard.writeText(copyText.val()).then(function() {
                _this.text('Copied');

                setTimeout(function () {
                    _this.html('Copy Profile <i class="fa-regular fa-copy"></i>');
                }, 1000);
            }).catch(function(error) {
                console.error("Copy failed!", error);
            });
        });

        $(document).on('click', '.wishList', function () {
            var _this = this.id;
            let user_id = $(this).data('user');
            let listing_id = $(this).data('listing');
            let purchase_package_id = $(this).data('purchase');
            if (isAuthenticate == 1) {
                wishList(user_id, listing_id, purchase_package_id, _this);
            } else {
                window.location.href = '{{route('login')}}';
            }
        });

        function wishList(user_id = null, listing_id = null, purchase_package_id = null, id = null) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: "{{ route('user.add.to.wish.list') }}",
                type: "POST",
                data: {
                    user_id: user_id,
                    listing_id: listing_id,
                    purchase_package_id: purchase_package_id
                },
                success: function (data) {
                    if (data.stage == 'added') {
                        $(`.save${id}`).removeClass("fa-regular fa-heart");
                        $(`.save${id}`).addClass("fa-solid fa-heart text-danger");
                        Notiflix.Notify.success("Wishlist added");
                    }
                    if (data.stage == 'remove') {
                        $(`.save${id}`).removeClass("fa-solid fa-heart text-danger");
                        $(`.save${id}`).addClass("fa-regular fa-heart");
                        Notiflix.Notify.success("Wishlist removed");
                    }
                },
            });
        }

        var newApp = new Vue({
            el: "#user_copy_id",
            data: {
                item: {
                    active: 0,
                },
            },
            mounted() {
            },
            methods: {
                copyTestingCode(copyText) {
                    navigator.clipboard.writeText(copyText);
                    Notiflix.Notify.success(`Copied: ${copyText}`);
                },
            },
        })
    </script>
@endpush

