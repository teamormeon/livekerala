@extends(template().'layouts.app')
@section('title',trans('Profile'))

@section('banner_heading')
    @lang('Profile')
@endsection

@section('content')
    <section class="profile-section">
        <div class="container">
            <div class="row">
                <div class="col">
                    <div class="cover-wrapper">
                        <div class="row gy-4">
                            <div class="col-lg-6">
                                <div class="about d-md-flex">
                                    <img src="{{ getFile(optional($user_information)->image_driver, optional($user_information)->image) }}" class="img-fluid profile" alt="image"/>
                                    <div>
                                        <h4 class="name">
                                            @lang($user_information->firstname) @lang($user_information->lastname)
                                            @if($user_information->identity_verify ==  2 && $user_information->address_verify ==  2)
                                                <i class="fas fa-check-circle" aria-hidden="true"></i>
                                            @endif
                                        </h4>

                                        @if($user_information->bio)
                                            <p class="bio">
                                                @lang($user_information->bio)
                                            </p>
                                        @endif

                                        <div class="links">
                                            @if($user_information->website)
                                                <a href="javascript:void(0)"><i class="fas fa-globe"></i>@lang($user_information->website)</a>
                                            @endif

                                            @if($user_information->fullAddress)
                                                <a href="javascript:void(0)"><i class="fas fa-location-arrow"></i>@lang($user_information->fullAddress)</a>
                                            @endif
                                            @if($user_information->created_at)
                                                <a href="javascript:void(0)" ><i class="fas fa-calendar-alt"></i>@lang('Joined '){{ dateTime($user_information->created_at) }}</a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @php
                                $profileUrl = url()->current();
                            @endphp

                            <div class="col-lg-6" id="user_copy_id">
                                <div class="right-wrapper">
                                    <div class="button-group">
                                        <input type="text" class="form-control copyText copy__profile__url opacity-0" value="{{ $profileUrl }}">
                                        <button  class="copy-btn">
                                            <span id="profileId">@lang('Copy profile')</span>
                                            <i class="fal fa-copy" aria-hidden="true"></i>
                                        </button>
                                        <button class="share">
                                            <div id="shareBlock"></div>
                                            <i class="fal fa-share-alt" aria-hidden="true"></i>
                                        </button>

                                        @if(Auth::check())
                                            @if(count($check_follower) < 1)
                                                <form action="{{ route('user.profile.follow', $user_information->id) }}" method="post" class="d-inline-block">
                                                    @csrf
                                                    <button class="btn-custom follow-btn disabled cursor-follow-btn" type="submit">
                                                        <i class="fal fa-plus" aria-hidden="true"></i>@lang('follow')
                                                    </button>
                                                </form>
                                            @else
                                                <form action="{{ route('user.profile.unfollow', $user_information->id) }}" method="post" class="d-inline-block">
                                                    @csrf
                                                    <button class="btn-custom follow-btn disabled cursor-follow-btn" type="submit" >
                                                        <i class="fal fa-minus" aria-hidden="true"></i>@lang('unFollow')
                                                    </button>
                                                </form>
                                            @endif
                                        @else
                                            <form action="{{ route('user.profile.follow', $user_information->id) }}" method="post" class="d-inline-block">
                                                @csrf
                                                <button class="btn-custom follow-btn disabled cursor-follow-btn" type="submit">
                                                    <i class="fal fa-plus" aria-hidden="true"></i>@lang('Follow')
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                    @if(count($user_information->get_social_links_user) > 0)
                                        <div class="social-links">
                                            @foreach($user_information->get_social_links_user as $social_link)
                                                <a href="{{ $social_link->social_url }}" target="_blank">
                                                    <i class="{{ $social_link->social_icon }}" aria-hidden="true"></i>
                                                </a>
                                            @endforeach
                                        </div>
                                    @endif

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
                                            <span></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="profile-info-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <div class="followers">
                        <h4>@lang('Followers')</h4>
                        @forelse($user_information->follower as $follower)
                            <div class="follower">
                                <a href="{{ route('profile', optional($follower->get_follwer_user)->username) }}">
                                    <img src="{{ getFile(optional($follower->get_follwer_user)->image_driver, optional($follower->get_follwer_user)->image) }}" class="img-fluid" alt="@lang('follower')"/>
                                </a>
                                <div class="creator-box">
                                    <div class="img-box">
                                        <img src="{{ getFile(optional($follower->get_follwer_user)->cover_image_driver, optional($follower->get_follwer_user)->cover_image) }}" alt="image" class="img-fluid cover"/>
                                        <img src="{{ getFile(optional($follower->get_follwer_user)->image_driver, optional($follower->get_follwer_user)->image) }}" class="img-fluid profile" alt="image"/>
                                    </div>

                                    <div class="text-box">
                                        <a class="creator-name" href="{{ route('profile', optional($follower->get_follwer_user)->username) }}">
                                            @lang(optional($follower->get_follwer_user)->firstname) @lang(optional($follower->get_follwer_user)->lastname)
                                        </a>
                                        <span>@lang('Member since') {{ dateTime(optional($follower->get_follwer_user)->created_at) }}</span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="d-flex justify-content-center">@lang('No Followers')</div>
                        @endforelse
                    </div>

                    <div class="followers">
                        <h4>@lang('Following')</h4>
                        @forelse($user_information->following as $following)
                            <div class="follower">
                                <a href="{{ route('profile', optional($following->get_following_user)->username) }}">
                                    <img src="{{ getFile(optional($following->get_following_user)->image_driver, optional($following->get_following_user)->image) }}" class="img-fluid" alt="@lang('follower')"/>
                                </a>
                                <div class="creator-box">
                                    <div class="img-box">
                                        <img src="{{ getFile(optional($following->get_following_user)->cover_image_driver, optional($following->get_following_user)->cover_image) }}" alt="image" class="img-fluid cover"/>
                                        <img src="{{ getFile(optional($following->get_following_user)->image_driver, optional($following->get_following_user)->image) }}" class="img-fluid profile" alt="image"/>
                                    </div>
                                    <div class="text-box">
                                        <a class="creator-name" href="{{ route('profile', optional($following->get_following_user)->username) }}">
                                            @lang(optional($following->get_following_user)->firstname) @lang(optional($following->get_following_user)->lastname)
                                        </a>
                                        <span>@lang('Member since') {{ dateTime(optional($following->get_following_user)->created_at) }}</span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="d-flex justify-content-center">@lang('No Followings')</div>
                        @endforelse
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="side-box">
                        <h5>@lang('Contact Creator')</h5>
                        <form action="{{ route('user.viewer.send.message.to.user', $user_information->id) }}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="row g-3">
                                <div class="input-box col-12">
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

                                <div class="input-box col-12">
                                 <textarea class="form-control @error('message') is-invalid @enderror" cols="30" rows="3" autocomplete="off" name="message" placeholder="@lang('Your message')"></textarea>
                                    <div class="invalid-feedback">
                                        @error('message') @lang($message) @enderror
                                    </div>
                                </div>
                                <div class="input-box col-12">
                                    <button class="btn-custom w-100">@lang('send')</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    @if(count($latest_listings) > 0)
                        <div class="created-listing">
                            <h4>@lang('Listings')</h4>
                            <div class="row g-4">
                                @forelse($latest_listings as $key => $latest_listing)
                                    @php
                                        $total = $latest_listing->reviews()[0]->total;
                                        $average_review = $latest_listing->reviews()[0]->average;
                                    @endphp
                                    <div class="col-lg-4 col-md-6">
                                        <div class="listing-box">
                                            <div class="img-box">
                                                <img class="img-fluid" src="{{ getFile($latest_listing->thumbnail_driver, $latest_listing->thumbnail) }}" alt="image"/>
                                                <button class="save wishList" type="button" id="{{$key}}"
                                                        data-user="{{ optional($latest_listing->get_user)->id }}"
                                                        data-purchase="{{ $latest_listing->purchase_package_id }}" data-listing="{{ $latest_listing->id }}">
                                                    @if($latest_listing->get_favourite_count > 0)
                                                        <i class="fas fa-heart save{{$key}}"></i>
                                                    @else
                                                        <i class="fal fa-heart save{{$key}}"></i>
                                                    @endif
                                                </button>
                                            </div>

                                            <div class="text-box">
                                                <div class="review">
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
                                                            $j++;
                                                        @endphp
                                                    @endfor

                                                    @if($average_review != intval($average_review))
                                                        <i class="fas fa-star-half-alt"></i>
                                                        @php
                                                            $j++;
                                                        @endphp
                                                    @endif

                                                    @if($average_review == 0 || $average_review != null)
                                                        @for($j; $j < 5; $j++)
                                                            <i class="far fa-star"></i>
                                                        @endfor
                                                    @endif
                                                    <span>(@lang($total.' reviews'))</span>
                                                </div>
                                                <a class="title" href="{{ route('listing.details', $latest_listing->slug ) }}">
                                                    @lang(Str::limit($latest_listing->title, 20))
                                                </a>
                                                <p></p>
                                                <a class="author" href="javascript:void(0)">
                                                    @lang($user_information->firstname) @lang($user_information->lastname)
                                                </a>
                                                <p class="mb-2">
                                                    <span class="">@lang('Category') : </span> {{ $latest_listing->getCategoriesName() }}
                                                </p>
                                                </p>
                                                <p class="address mt-1">
                                                    <i class="fal fa-map-marker-alt"></i>
                                                    @lang($latest_listing->get_cities?->getAddress() ?? $latest_listing->address)
                                                </p>
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
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        'use strict';
        var isAuthenticate = '{{Auth::check()}}';
        $(document).on('click', '.copy-btn', function () {
            var _this = $(this)[0];
            var copyText = $(this).siblings('input');
            $(copyText).prop('disabled', false);
            copyText.select();
            document.execCommand("copy");
            $(copyText).prop('disabled', true);
            $(this).text('Coppied');
            setTimeout(function () {
                $(_this).text('');
                $(_this).html('Copy Profile <i class="fal fa-copy"></i>');
            }, 500)
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
                        $(`.save${id}`).removeClass("fal fa-heart");
                        $(`.save${id}`).addClass("fas fa-heart");
                        Notiflix.Notify.success("Wishlist added");
                    }
                    if (data.stage == 'remove') {
                        $(`.save${id}`).removeClass("fas fa-heart");
                        $(`.save${id}`).addClass("fal fa-heart");
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

