@extends(template().'layouts.app')
@section('title',trans('Listing Details'))

@section('banner_heading')
    @lang($single_listing_details->title)
@endsection
@push('style')
    <link rel="stylesheet" href="{{ asset('assets/global/css/frontend_leaflet.css') }}"/>
    <link rel="stylesheet" href="{{ asset('assets/global/css/frontendControl.FullScreen.css') }}"/>

    @if(3 > count($single_listing_details->get_products))
        <style>
            .listing-details .owl-carousel .owl-nav, .listing-details .owl-carousel .owl-nav.disabled {
                display: none !important;
            }
        </style>
    @endif

    <style>
        .whatsapp_icon {
            position: fixed;
            bottom: 30px !important;
            right: 20px !important;
        }

        .whatsapp_icon a {
            position: relative;
        }

        .whatsapp_icon .notification-dot {
            position: absolute;
            top: 2px;
            right: 2px;
            height: 12px;
            width: 12px;
            border-radius: 50%;
            background-color: red;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .whatsapp_icon .notification-dot i {
            font-size: 7px;
            margin-top: 1px;
        }

        .whatsapp_icon img {
            width: 50px !important;
            border-radius: 50%;
        }

        .whatsapp-bubble {
            box-shadow: rgba(0, 0, 0, 0.1) 0px 12px 24px 0px;
            max-width: 360px !important;
            position: fixed;
            right: 20px;
            bottom: 90px !important;
        }

        .whatsapp-bubble .card-header {
            position: relative;
            padding: 24px 20px;
        }

        .whatsapp-bubble .card {
            border: none;
        }

        .whatsapp-bubble .card-header .close-btn {
            position: absolute;
            top: 10px;
            right: 15px;
            cursor: pointer;
        }

        .whatsapp-bubble .card-header .close-btn i {
            font-size: 16px;
        }

        .whatsapp-bubble .card-body {
            padding: 20px 20px 20px 10px;
            background-color: rgb(230, 221, 212);
            position: relative;
            overflow: auto;
            max-height: 382px;
        }

        .whatsapp-bubble .card-body::before {
            position: absolute;
            content: "";
            left: 0px;
            top: 0px;
            height: 100%;
            width: 100%;
            z-index: 0;
            opacity: 0.08;
            background-image: url(http://127.0.0.1/listplace_codecanyon/project/assets/themes/classic/img/whatsapp-bg.webp);
        }

        .whatsapp-bubble .card-body .whatsapp-chat-text {
            background-color: white;
            padding: 15px;
            border-radius: 10px;
            box-shadow: rgba(0, 0, 0, 0.13) 0px 1px 0.5px;
        }

        .whatsapp-bubble .profile {
            display: flex;
        }

        .whatsapp-bubble .profile .profile-thum {
            min-width: 52px;
            width: 52px;
            height: 52px;
            border: 1px solid rgb(0, 0, 0, 0.1);
            border-radius: 50%;
            margin-right: 10px;
            position: relative;
        }

        .whatsapp-bubble .profile .profile-thum .active-dot {
            position: absolute;
            height: 10px;
            width: 10px;
            border-radius: 50%;
            background-color: rgb(74, 213, 4);
            bottom: 4px;
            left: 40px;
            border: 1px solid white;
        }

        .whatsapp-bubble .profile .profile-thum img {
            max-width: 100%;
            height: auto;
            border-radius: 50%;
        }

        .whatsapp-bubble .profile .profile-content .profile-title {
            font-weight: 600;
        }

        .whatsapp-bubble .profile .profile-content p {
            font-size: 14px;
        }

        .whatsapp-bubble .card-footer {
            padding: 20px;
        }

        .whatsapp-bubble .card-footer .btn-custom {
            width: 100%;
            height: 35px;
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 25px;
            text-transform: capitalize;
        }

        .whatsapp-bubble .card-footer .btn-custom i {
            font-size: 14px;
        }
    </style>
@endpush
@section('content')
    <input type="hidden" id="googleMapAppKey" value="{{ basicControl()->google_map_app_key }}">
    <input type="hidden" id="google_map_id" value="{{ basicControl()->google_map_id }}">
    <section class="listing-details">
        <div class="overlay">
            <div class="container">
                <div class="row g-lg-5">
                    <div class="col-lg-8">
                        @include(template().'partials.xzoom_container')
                        <div class="navigation">
                            <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <span id="pills-home-tab" data-bs-toggle="pill" data-bs-target="#pills-home"
                                          type="button" role="tab" aria-controls="pills-home" aria-selected="true">
                                        <a class="short-nav-item active" href="#description">@lang('Description')</a>
                                        @if(optional($single_listing_details->get_package)->is_video != 0 && $single_listing_details->youtube_video_id != null)
                                            <a class="short-nav-item" href="#videobox">@lang('Video')</a>
                                        @endif
                                        @if(optional($single_listing_details->get_package)->is_amenities != 0 && count($single_listing_details->get_listing_amenities) > 0)
                                            <a class="short-nav-item" href="#amenities">@lang('Amenities')</a>
                                        @endif
                                        @if(count($single_listing_details->get_products) == 1 && $single_listing_details->get_products[0]->product_title == null && $single_listing_details->get_products[0]->product_price == null && $single_listing_details->get_products[0]->product_description == null && $single_listing_details->get_products[0]->product_thumbnail == null)
                                        @else
                                            @if(optional($single_listing_details->get_package)->is_product != 0 && count($single_listing_details->get_products) > 0)
                                                <a class="short-nav-item" href="#products">@lang('Products')</a>
                                            @endif
                                        @endif

                                    </span>
                                </li>

                                <li class="nav-item ms-1" role="presentation">
                                    <span id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-profile"
                                          type="button" role="tab" aria-controls="pills-profile" aria-selected="false">
                                        <a class="short-nav-item" href="#reviews">
                                            @lang('Reviews')
                                            @php
                                                $fullStars = floor($average_review);
                                                $halfStar = ($average_review - $fullStars) >= 0.5 ? true : false;
                                            @endphp

                                            <span class="listing__reviews">
                                                 @for ($i = 0; $i < $fullStars; $i++)
                                                    <i class="fas fa-star" aria-hidden="true"></i>
                                                @endfor

                                                @if ($halfStar)
                                                    <i class="fas fa-star-half-alt"></i>
                                                @endif

                                                @for ($i = $fullStars + ($halfStar ? 1 : 0); $i < 5; $i++)
                                                    <i class="far fa-star"></i>
                                                @endfor
                                            </span>
                                            <span class="badge bg-primary font-10">
                                                {{ $single_listing_details->reviews()[0]->total }}
                                            </span></a>
                                    </span>
                                </li>
                            </ul>
                        </div>


                        <div class="tab-content" id="pills-tabContent">
                            <div class="tab-pane fade show active" id="pills-home" role="tabpanel"
                                 aria-labelledby="pills-home-tab" tabindex="0">
                                <div id="description" class="description-box">
                                    <h4>@lang('Description')</h4>
                                    <p>
                                        {!! $single_listing_details->description !!}
                                    </p>
                                </div>

                                @if(optional($single_listing_details->get_package)->is_video != 0 && $single_listing_details->youtube_video_id != null)
                                    <div id="videobox" class="video-box">
                                        <h4>@lang('Video')</h4>
                                        <iframe width="100%" height="100%"
                                                src="https://www.youtube.com/embed/{{ $single_listing_details->youtube_video_id }}?controls=0"
                                                frameborder="0"
                                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                                allowfullscreen></iframe>
                                    </div>
                                @endif

                                @if(optional($single_listing_details->get_package)->is_amenities != 0 && count($single_listing_details->get_listing_amenities) > 0)
                                    <div id="amenities" class="amenities-box">
                                        <h4 class="mb-4">@lang('Amenities')</h4>
                                        <div class="row gy-4">
                                            @forelse($single_listing_details->get_listing_amenities as $amenity)
                                                <div class="col-3 col-md-2">
                                                    <div class="amenity-box">
                                                        <i class="{{ optional($amenity->get_amenity)->icon }}"></i>
                                                        <h6>{{ optional(optional($amenity->get_amenity)->details)->title }}</h6>
                                                    </div>
                                                </div>
                                            @empty
                                            @endforelse
                                        </div>
                                    </div>
                                @endif

                                @if(count($single_listing_details->get_products) == 1 && $single_listing_details->get_products[0]->product_title == null && $single_listing_details->get_products[0]->product_price == null && $single_listing_details->get_products[0]->product_description == null && $single_listing_details->get_products[0]->product_thumbnail == null)
                                @else
                                    @if(optional($single_listing_details->get_package)->is_product != 0 && count($single_listing_details->get_products) > 0)
                                        <div id="products" class="products mb-5">
                                            <h4>@lang('Products')</h4>
                                            <div class="owl-carousel products-slider">
                                                @foreach($single_listing_details->get_products as $listing_product)
                                                    <div class="product-box">
                                                        <div class="img-box">
                                                            <img class="img-fluid"
                                                                 src="{{ getFile($listing_product->driver, $listing_product->product_thumbnail) }}"
                                                                 alt="image"/>
                                                            <span
                                                                class="price"> $@lang($listing_product->product_price) </span>
                                                        </div>

                                                        <div class="text-box">
                                                            <p>{{ Str::limit($listing_product->product_title, 20) }}</p>
                                                        </div>

                                                        <div class="d-flex justify-content-center p-2">
                                                            <button
                                                                class="btn-custom-product listing_product_id text-uppercase"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#productDetailsModal{{ $listing_product->id }}"
                                                                data-listingproductid="{{ $listing_product->id }}">
                                                                @lang('view details')
                                                            </button>
                                                        </div>
                                                    </div>

                                                    @push('frontend_modal')
                                                        <div class="modal fade product-query-modal"
                                                             id="productDetailsModal{{ $listing_product->id }}"
                                                             tabindex="-1"
                                                             aria-labelledby="productDetailsModalLabel"
                                                             aria-hidden="true" data-bs-backdrop="static">
                                                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title"
                                                                            id="productDetailsModalLabel">
                                                                            @lang($listing_product->product_title)
                                                                        </h5>
                                                                        <button type="button" class="btn-close"
                                                                                data-bs-dismiss="modal"
                                                                                aria-label="Close"></button>
                                                                    </div>

                                                                    <div class="modal-body">
                                                                        <div class="row">
                                                                            <div class="col-lg-5">
                                                                                <div>
                                                                                    <div
                                                                                        id="mainCarousel{{ $listing_product->id }}"
                                                                                        class="carousel mx-auto main_carousel">
                                                                                        @forelse($listing_product->get_product_image as $listing_product_image)

                                                                                            @php
                                                                                                $all_product_images = App\Models\ProductImage::where('product_id',$listing_product_image->product_id)->count();
                                                                                            @endphp

                                                                                            @if($all_product_images >= 1)
                                                                                                <div
                                                                                                    class="carousel__slide"
                                                                                                    data-src="{{ getFile($listing_product_image->driver, $listing_product_image->product_image) }}"
                                                                                                    data-fancybox="gallery"
                                                                                                    data-caption="">
                                                                                                    <img
                                                                                                        class="img-fluid"
                                                                                                        src="{{ getFile($listing_product_image->driver, $listing_product_image->product_image) }}"/>
                                                                                                </div>
                                                                                            @endif

                                                                                        @empty
                                                                                            <div class="carousel__slide"
                                                                                                 data-src="{{ getFile($listing_product->driver, $listing_product->product_thumbnail) }}"
                                                                                                 data-fancybox="gallery"
                                                                                                 data-caption="">
                                                                                                <img class="img-fluid"
                                                                                                     src="{{ getFile($listing_product->driver, $listing_product->product_thumbnail) }}"/>
                                                                                            </div>
                                                                                        @endforelse
                                                                                    </div>

                                                                                    <div
                                                                                        id="thumbCarousel{{ $listing_product->id }}"
                                                                                        class="carousel max-w-xl mx-auto mb-5 thumb_carousel">
                                                                                        @forelse($listing_product->get_product_image as $listing_product_image)
                                                                                            @php
                                                                                                $all_product_images = App\Models\ProductImage::where('product_id',$listing_product_image->product_id)->count();
                                                                                            @endphp
                                                                                            @if($all_product_images >= 1)
                                                                                                <div
                                                                                                    class="carousel__slide">
                                                                                                    <img
                                                                                                        class="panzoom__content img-fluid"
                                                                                                        src="{{ getFile($listing_product_image->driver, $listing_product_image->product_image) }}"/>
                                                                                                </div>
                                                                                            @endif
                                                                                        @empty
                                                                                        @endforelse
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <div class="col-lg-7">
                                                                                <h5>@lang($listing_product->product_title)</h5>
                                                                                <p>
                                                                                    @lang($listing_product->product_description)
                                                                                </p>
                                                                                <h4>
                                                                                <span
                                                                                    class="text-primary">@lang('Price'):</span>
                                                                                    <span>${{ $listing_product->product_price }}</span>
                                                                                </h4>
                                                                                <div class="make-query">
                                                                                    <h5>@lang('Make Query')</h5>
                                                                                    <form
                                                                                        action="{{ route('user.send.product.query') }}"
                                                                                        method="post"
                                                                                        enctype="multipart/form-data">
                                                                                        @csrf
                                                                                        <input type="hidden"
                                                                                               name="product_id"
                                                                                               value="{{ $listing_product->id }}"
                                                                                               class="form-control">
                                                                                        <input type="hidden"
                                                                                               name="listing_id"
                                                                                               value="{{ @$single_listing_details->id }}"
                                                                                               class="form-control">
                                                                                        <div class="row g-3">
                                                                                            <div
                                                                                                class="input-box col-12">
                                                                                                <textarea
                                                                                                    class="form-control @error('message') is-invalid @enderror text-dark"
                                                                                                    cols="30" rows="3"
                                                                                                    autocomplete="off"
                                                                                                    name="message"
                                                                                                    placeholder="@lang('Your message')"></textarea>
                                                                                                <div
                                                                                                    class="invalid-feedback">
                                                                                                    @error('message') @lang($message) @enderror
                                                                                                </div>
                                                                                            </div>
                                                                                            <div
                                                                                                class="input-box col-12">
                                                                                                <button
                                                                                                    class="btn-custom w-100">
                                                                                                    @lang('submit')
                                                                                                </button>
                                                                                            </div>
                                                                                        </div>
                                                                                    </form>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endpush
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                @endif

                            </div>

                            <div class="tab-pane fade" id="pills-profile" role="tabpanel"
                                 aria-labelledby="pills-profile-tab" tabindex="0">
                                <div id="review-app">
                                    <div id="reviews" class="reviews">
                                        <div class="customer-review">
                                            <h4>@lang('Reviews')</h4>
                                            <div class="review-box" v-for="(obj, index) in item.feedArr">
                                                <div class="text">
                                                    <img :src="obj.review_user_info.imgPath"/>
                                                    <span class="name">@{{obj.review_user_info.firstname + ' ' + obj.review_user_info.lastname}}</span>
                                                    <p class="mt-3">
                                                        @{{ obj.review }}
                                                    </p>
                                                </div>
                                                <div class="review-date">
                                                  <span class="review">
                                                      <div id="half-stars-example" >
                                                        <!-- Full Stars -->
                                                        <i
                                                            class="fas fa-star"
                                                            v-for="n in Math.floor(obj.rating)"
                                                            :key="'full-' + n">
                                                        </i>

                                                          <!-- Half Star -->
                                                        <i
                                                            v-if="obj.rating % 1 >= 0.5"
                                                            class="fas fa-star-half-alt">
                                                        </i>

                                                          <!-- Empty Stars -->
                                                        <i
                                                            class="far fa-star"
                                                            v-for="n in (5 - Math.ceil(obj.rating))"
                                                            :key="'empty-' + n">
                                                        </i>
                                                      </div>
                                                  </span>
                                                    <br/>
                                                    <span class="date">@{{obj.date_formatted}}</span>
                                                </div>
                                            </div>

                                            <div class="custom-not-found3" v-if="item.feedArr.length<1">
                                                <img src="{{ asset(template(true).'img/no_data_found.png') }}"
                                                     alt="image" class="img-fluid">
                                            </div>

                                            <div class="row mt-5">
                                                <div class="col d-flex justify-content-center">
                                                    @include(template().'partials.vuePaginate')
                                                </div>
                                            </div>
                                        </div>

                                        @auth
                                            @if($reviewDone <= 0 && $single_listing_details->user_id != Auth::id())
                                                <div class="add-review mb-5" v-if="item.reviewDone < 1">
                                                    <div>
                                                        <h4>@lang('Add Review')</h4>
                                                    </div>
                                                    <form>
                                                        <div class="mb-3">
                                                            <p>
                                                                @lang('Writing great reviews may help others discover the places that are just apt for them')
                                                            </p>
                                                            <div id="half-stars-example">
                                                                <div class="rating-group ms-0">
                                                                    <label aria-label="1 star" class="rating__label"
                                                                           for="rating-10">
                                                                        <i class="rating__icon rating__icon--star fas fa-star"
                                                                           aria-hidden="true"></i>
                                                                    </label>
                                                                    <input class="rating__input" name="rating"
                                                                           id="rating-10" value="1" @click="rate(1)"
                                                                           type="radio"/>

                                                                    <label aria-label="2 stars" class="rating__label"
                                                                           for="rating-20">
                                                                        <i class="rating__icon rating__icon--star fas fa-star"
                                                                           aria-hidden="true"></i>
                                                                    </label>
                                                                    <input class="rating__input" name="rating"
                                                                           id="rating-20" value="2" @click="rate(2)"
                                                                           type="radio"/>

                                                                    <label aria-label="3 stars" class="rating__label"
                                                                           for="rating-30">
                                                                        <i class="rating__icon rating__icon--star fas fa-star"
                                                                           aria-hidden="true"></i>
                                                                    </label>
                                                                    <input class="rating__input" name="rating"
                                                                           id="rating-30" value="3" @click="rate(3)"
                                                                           type="radio"/>

                                                                    <label aria-label="4 stars" class="rating__label"
                                                                           for="rating-40">
                                                                        <i class="rating__icon rating__icon--star fas fa-star"
                                                                           aria-hidden="true"></i>
                                                                    </label>
                                                                    <input class="rating__input" name="rating"
                                                                           id="rating-40" value="4" @click="rate(4)"
                                                                           type="radio"/>

                                                                    <label aria-label="5 stars" class="rating__label"
                                                                           for="rating-50">
                                                                        <i class="rating__icon rating__icon--star fas fa-star"
                                                                           aria-hidden="true"></i>
                                                                    </label>
                                                                    <input class="rating__input" name="rating"
                                                                           id="rating-50" value="5" checked=""
                                                                           type="radio" @click="rate(5)"/>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <label for="exampleFormControlTextarea1"
                                                                   class="form-label">@lang('Your message')</label>
                                                            <textarea class="form-control text-dark"
                                                                      id="exampleFormControlTextarea1" name="review"
                                                                      v-model="item.feedback" rows="5"></textarea>
                                                            <span class="text-danger reviewError"></span>
                                                        </div>
                                                        <button class="btn-custom mt-2"
                                                                @click.prevent="addFeedback">@lang('Submit now')</button>
                                                    </form>
                                                </div>
                                            @endif
                                        @else
                                            <div class="add-review mb-5 add__review__login" v-if="item.reviewDone < 1">
                                                <div class="d-flex justify-content-between">
                                                    <h4>@lang('Add Review')</h4>
                                                </div>
                                                <a href="{{ route('login') }}"
                                                   class="btn btn-primary btn-sm h-25">@lang('Login to review')</a>
                                            </div>
                                        @endauth
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="side-bar">
                            <div class="side-box">
                                <h5>@lang('Created By')</h5>
                                <div class="creator-box">
                                    <div class="img-box">
                                        <img
                                            src="{{ getFile(optional($single_listing_details->get_user)->cover_image_driver, optional($single_listing_details->get_user)->cover_image) }}"
                                            alt="image" class="img-fluid cover"/>
                                        <img
                                            src="{{ getFile(optional($single_listing_details->get_user)->image_driver, optional($single_listing_details->get_user)->image) }}"
                                            class="img-fluid profile" alt="image"/>
                                    </div>

                                    <div class="text-box">
                                        <h5 class="creator-name">
                                            @lang(optional($single_listing_details->get_user)->firstname) @lang(optional($single_listing_details->get_user)->lastname)
                                        </h5>
                                        <span>@lang('Member since') @lang(optional($single_listing_details->get_user)->created_at->format('M Y')) </span>
                                        <div class="d-flex justify-content-between my-3">
                                            <span>
                                                @if($total_listings_an_user['totalListing'] > 1)
                                                    {{ $total_listings_an_user['totalListing'] }} @lang('Listings')
                                                @else
                                                    {{ $total_listings_an_user['totalListing'] }} @lang('Listing')
                                                @endif
                                            </span>
                                            <span>{{ $follower_count['totalFollower'] }} @lang('Followers')</span>
                                        </div>

                                        <a href="{{ route('profile', optional($single_listing_details->get_user)->username) }}"
                                           class="btn-custom cursor-pointer">
                                            @lang('Visit profile')
                                        </a>
                                    </div>
                                </div>
                            </div>

                            @if(optional($single_listing_details->get_package)->is_business_hour != 0 && count($single_listing_details->get_business_hour) > 0)
                                <div class="side-box">
                                    <h5>@lang('Opening Hours')</h5>
                                    <ul>
                                        @forelse($single_listing_details->get_business_hour as $business_hour)
                                            @if($business_hour->start_time)
                                                <li>
                                                    @lang($business_hour->working_day)
                                                    <span class="float-end">{{ \Carbon\Carbon::parse($business_hour->start_time)->format('h a') }} - {{ \Carbon\Carbon::parse($business_hour->end_time)->format('h a') }}</span>
                                                </li>
                                            @else
                                                <li>
                                                    @lang($business_hour->working_day)
                                                    <span class="float-end">@lang('Closed')</span>
                                                </li>
                                            @endif
                                        @empty
                                        @endforelse
                                    </ul>
                                </div>
                            @endif

                            <div class="side-box">
                                <h5>@lang('Contact Seller')</h5>
                                <ul>
                                    @if(optional($single_listing_details->get_user)->phone)
                                        <li>
                                            <i class="far fa-phone-alt" aria-hidden="true"></i>
                                            <span>{{ optional($single_listing_details->get_user)->phone }}</span>
                                        </li>
                                    @endif
                                    @if(optional($single_listing_details->get_user)->email)
                                        <li>
                                            <i class="far fa-envelope" aria-hidden="true"></i>
                                            <span>{{ optional($single_listing_details->get_user)->email }}</span>
                                        </li>
                                    @endif
                                    @if(optional($single_listing_details->get_user)->fullAddress)
                                        <li>
                                            <i class="far fa-map-marker-alt" aria-hidden="true"></i>
                                            <span>@lang(optional($single_listing_details->get_user)->fullAddress)</span>
                                        </li>
                                    @endif
                                    @if($single_listing_details->get_user->website)
                                        <li>
                                            <i class="fal fa-globe me-1"></i>
                                            <a class="text-secondary"
                                               href="javascript:void(0)"><span> @lang(optional($single_listing_details->get_user)->website)</span>
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                                @if(count(optional($single_listing_details->get_user)->get_social_links_user) > 0)
                                    <div class="social-links mt-4">
                                        @foreach(optional($single_listing_details->get_user)->get_social_links_user as $social)
                                            <a href="{{ $social->social_url }}" target="_blank">
                                                <i class="{{ $social->social_icon }}" aria-hidden="true"></i>
                                            </a>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            <div class="side-box">
                                <div class="listing-map-box" data-lat="{{ $single_listing_details->lat }}"
                                     data-long="{{ $single_listing_details->long }}"
                                     data-title="@lang( Str::limit($single_listing_details->title, 30))"
                                     data-image="{{ getFile($single_listing_details->thumbnail_driver, $single_listing_details->thumbnail) }}"
                                     data-location="@lang($single_listing_details->address . ($single_listing_details->city_id != null ? ', '.$single_listing_details->get_cities?->getAddress() : ''))"
                                     data-route="{{ route('listing.details', $single_listing_details->slug) }}"></div>
                                <h5>@lang('Map')</h5>
                                <div class="sidebar-map" id="map"></div>
                            </div>

                            <div class="side-box">
                                <h5>@lang('Send a Message')</h5>
                                <form action="{{ route('user.send.listing.message', $single_listing_details->id) }}"
                                      method="post" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row g-3">
                                        <div class="input-box col-12">
                                            <input class="form-control @error('name') is-invalid @enderror" type="text"
                                                   autocomplete="off" name="name"
                                                   @if(Auth::check() && Auth::id() != $single_listing_details->user_id)
                                                       value="@lang(Auth::user()->firstname) @lang(Auth::user()->lastname)"
                                                   @else
                                                       placeholder="@lang('Full Name')"
                                                @endif/>
                                            <div class="invalid-feedback">
                                                @error('name') @lang($message) @enderror
                                            </div>
                                        </div>

                                        <div class="input-box col-12">
                                            <textarea class="form-control @error('message') is-invalid @enderror"
                                                      cols="30" rows="3" autocomplete="off" name="message"
                                                      placeholder="@lang('Your message')"></textarea>
                                            <div class="invalid-feedback">
                                                @error('message') @lang($message) @enderror
                                            </div>
                                        </div>
                                        <div class="input-box col-12">
                                            <button class="btn-custom w-100">
                                                @lang('submit')
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>


                            <div class="side-box claim-business">
                                <div class="d-flex align-items-center">
                                    <img src="{{ getFile(basicControl()->logo_driver,basicControl()->logo) }}"
                                         class="img-fluid" alt=""/>
                                    <div>
                                        <h5>@lang('Claim This Business')</h5>
                                        <button class="btn-custom" data-bs-toggle="modal"
                                                data-bs-target="#claimBusiness">
                                            @lang('Claim')
                                        </button>
                                    </div>
                                </div>
                            </div>

                            @if(isset($single_listing_details->form))
                                <div class="side-box">
                                    <form action="{{ route('collect.listing.form.data') }}" method="post"
                                          enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="dynamic_forms_id"
                                               value="{{ $single_listing_details->form->id }}">
                                        <input type="hidden" name="listing_id"
                                               value="{{ $single_listing_details->id }}">
                                        <h5 class="title">@lang(optional($single_listing_details->form)->name ?? 'Data Collection Form')</h5>
                                        <div class="row g-4">
                                            @foreach(optional($single_listing_details->form)->input_form as $k => $v)
                                                @if($v->type == "text")
                                                    <div class="input-box col-12">
                                                        <label class="form-label">@lang($v->field_name)</label>
                                                        <input class="form-control" type="text" autocomplete="off"
                                                               name="{{ $k }}" value="{{ old($k) }}"
                                                               placeholder="{{ $v->field_name }}"
                                                            {{ $v->validation == 'required' ? 'required' : '' }}>
                                                        @if($errors->has($k))
                                                            <div class="invalid-feedback">
                                                                @lang($errors->first($k))
                                                            </div>
                                                        @endif
                                                    </div>
                                                @elseif($v->type == "number")
                                                    <div class="input-box col-12">
                                                        <label class="form-label">@lang($v->field_name)</label>
                                                        <input class="form-control" type="text" autocomplete="off"
                                                               name="{{ $k }}" value="{{ old($k) }}"
                                                               placeholder="{{ $v->field_name }}"
                                                            {{ $v->validation == 'required' ? 'required' : '' }}>
                                                        @if($errors->has($k))
                                                            <div class="invalid-feedback">
                                                                @lang($errors->first($k))
                                                            </div>
                                                        @endif
                                                    </div>
                                                @elseif($v->type == "date")
                                                    <div class="input-box col-12">
                                                        <label class="form-label">@lang($v->field_name)</label>
                                                        <input class="form-control" type="date" autocomplete="off"
                                                               name="{{ $k }}" value="{{ old($k) }}"
                                                               placeholder="{{ $v->field_name }}"
                                                            {{ $v->validation == 'required' ? 'required' : '' }}>
                                                        @if($errors->has($k))
                                                            <div class="invalid-feedback">
                                                                @lang($errors->first($k))
                                                            </div>
                                                        @endif
                                                    </div>
                                                @elseif($v->type == "select")
                                                    <div class="input-box col-12">
                                                        <label class="form-label">@lang($v->field_name)</label>
                                                        <select class="js-select form-select" name="{{ $k }}" id="">
                                                            <option selected
                                                                    disabled>@lang('Choose '.$v->field_name)</option>
                                                            @foreach($v->option as $kk => $vv)
                                                                <option value="{{ $kk }}">{{ ucwords($vv) }}</option>
                                                            @endforeach
                                                        </select>
                                                        @if($errors->has($k))
                                                            <div class="invalid-feedback">
                                                                @lang($errors->first($k))
                                                            </div>
                                                        @endif
                                                    </div>
                                                @elseif($v->type == "textarea")
                                                    <div class="col-12">
                                                        <label class="form-label">@lang($v->field_name)</label>
                                                        <textarea class="form-control" cols="30" rows="3"
                                                                  autocomplete="off" name="{{ $k }}"
                                                                  placeholder="{{ $v->field_name }}"
                                                    {{ $v->validation == 'required' ? 'required' : '' }}>
                                                        {{ old($k) }}
                                                    </textarea>
                                                        @if($errors->has($k))
                                                            <div class="invalid-feedback">
                                                                @lang($errors->first($k))
                                                            </div>
                                                        @endif
                                                    </div>
                                                @elseif($v->type == "file")
                                                    <div class="col-12">
                                                        <label class="form-label">@lang($v->field_name)</label>
                                                        <div class="attach-file">
                                                            <div class="prev">@lang('Upload File')</div>
                                                            <input class="form-control" type="file" name="{{$k}}"
                                                                   {{ $v->validation == 'required' ? 'required' : '' }} id="Upload-File">
                                                            @if($errors->has($k))
                                                                <div class="invalid-feedback">
                                                                    @lang($errors->first($k))
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach
                                            <div class="col-12">
                                                <button type="submit"
                                                        class="btn-custom w-100">@lang(optional($single_listing_details->form)->button_text ?? 'Submit')</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @push('frontend_modal')
        <div class="modal fade" id="claimBusiness" tabindex="-1" aria-labelledby="claimBusinessLabel" aria-hidden="true"
             data-bs-backdrop="static">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="claimBusinessLabel">
                            @lang('Claim Business')
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('user.claim.business', $single_listing_details->id) }}" method="post"
                              enctype="multipart/form-data">
                            @csrf
                            <div class="row g-3">
                                <p>@lang('Do you really want to make this claim?')
                                    <br>@lang('If you can\'t prove it, you should face a fine.')</p>
                                <div class="input-box col-12">
                                    <input class="form-control @error('claim_name') is-invalid @enderror" type="text"
                                           autocomplete="off" name="claim_name"
                                           @if(Auth::check() && Auth::user()->id != $single_listing_details->user_id)
                                               value="@lang(Auth::user()->firstname) @lang(Auth::user()->lastname)"
                                           @else
                                               placeholder="@lang('Full Name')"
                                        @endif/>
                                    <div class="invalid-feedback">
                                        @error('claim_name') @lang($message) @enderror
                                    </div>
                                </div>

                                <div class="input-box col-12">
                                    <textarea class="form-control @error('claim_message') is-invalid @enderror"
                                              cols="30" rows="3" autocomplete="off" name="claim_message"
                                              placeholder="@lang('Your message')"></textarea>
                                    <div class="invalid-feedback">
                                        @error('claim_message') @lang($message) @enderror
                                    </div>
                                </div>
                                <div class="input-box col-12">
                                    <button class="btn-custom w-100">
                                        @lang('Claim Now')
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endpush
@endsection

@if(optional($single_listing_details->get_package)->is_whatsapp == 1)
    @section('whatsapp_chat')
        @include(template().'whatsapp_chat')
    @endsection
@endif

@if(optional($single_listing_details->get_package)->is_messenger == 1)
    @section('fb_messenger_chat')
        <!--start of Facebook Messenger Script-->
        <div id="fb-root"></div>
        <script>
            "use strict";
            // $(document).ready(function () {
            var fb_app_id = "{{ $single_listing_details->fb_app_id }}";
            window.fbAsyncInit = function () {
                FB.init({
                    appId: fb_app_id,
                    autoLogAppEvents: true,
                    xfbml: true,
                    version: 'v10.0'
                });
            };
            (function (d, s, id) {
                var js, fjs = d.getElementsByTagName(s)[0];
                if (d.getElementById(id)) return;
                js = d.createElement(s);
                js.id = id;
                js.src = 'https://connect.facebook.net/en_US/sdk/xfbml.customerchat.js';
                fjs.parentNode.insertBefore(js, fjs);
            }(document, 'script', 'facebook-jssdk'));
            // });
        </script>
        <script async defer crossorigin="anonymous" src="https://connect.facebook.net/en_US/sdk.js"></script>
        <div class="fb-customerchat" page_id="{{ $single_listing_details->fb_page_id }}"></div>
        <!--End of Facebook Messenger Script-->
    @endsection
@endif

@push('css-lib')
    <link rel="stylesheet" href="{{ asset(template(true).'css/owl.carousel.min.css') }}"/>
    <link rel="stylesheet" href="{{ asset(template(true).'css/owl.theme.default.min.css') }}"/>
@endpush

@push('extra-js')
    <!-- fancybox slider -->
    <script src="{{ asset(template(true).'js/fancybox.umd.js') }}"></script>
@endpush

@push('script')
    @if(basicControl()->is_google_map == 1)
        <script
            src="https://maps.googleapis.com/maps/api/js?key={{ basicControl()->google_map_app_key }}&loading=async&callback=initMap&libraries=marker"
            defer></script>
        <script src="{{ asset('assets/global/js/frontend_google_map.js') }}"></script>
    @else
        <script
            src="https://maps.googleapis.com/maps/api/js?key={{ basicControl()->google_map_app_key }}&loading=async"></script>
        <script src="{{ asset('assets/global/js/frontend_leaflet.js') }}"></script>
        <script src="{{ asset('assets/global/js/frontendControl.FullScreen.js') }}"></script>
        <script src="{{ asset('assets/global/js/frontend_map.js') }}"></script>
    @endif
    <script src="{{ asset(template(true).'js/carousel.js') }}"></script>
    <script>
        'use strict'

        $(document).ready(function () {
            $('#bubble-btn').on('click', function () {
                $('.pasa').removeClass('opacity-0');
                $('.pasa').addClass('opacity-100');
                $('.whatsapp-bubble').fadeIn();
            });

            $('.close-btn').on('click', function () {
                $('.whatsapp-bubble').fadeOut();
            });

            $('.listing_product_id').on('click', function () {
                let listingProductId = $(this).data('listingproductid');
                const mainCarousel2 = new Carousel(document.querySelector(`#mainCarousel${listingProductId}`), {
                    Dots: false,
                });

                // Thumbnails
                const thumbCarousel2 = new Carousel(document.querySelector(`#thumbCarousel${listingProductId}`), {
                    Sync: {
                        target: mainCarousel2,
                        friction: 0,
                    },
                    Dots: false,
                    Navigation: false,
                    center: true,
                    slidesPerPage: 1,
                    infinite: true,
                });

                // Customize Fancybox
                Fancybox.bind('[data-fancybox="gallery"]', {
                    Carousel: {
                        on: {
                            change: (that) => {
                                mainCarousel2.slideTo(mainCarousel2.findPageForSlide(that.page), {
                                    friction: 0,
                                });
                            },
                        },
                    },
                });
            });

            $('.wishList').on('click', function () {
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


            $(".products-slider").owlCarousel({
                loop: "{{ 3 < count($single_listing_details->get_products) ?true:false}}",
                margin: 15,
                nav: false,
                dots: false,
                autoplay: true,
                autoplayTimeout: 3000,
                responsive: {
                    0: {
                        items: 1,
                    },
                    768: {
                        items: 2,
                    },
                    992: {
                        items: 3,
                    },
                },
            });
        });

        var newApp = new Vue({
            el: "#review-app",
            data: {
                item: {
                    feedback: "",
                    listingId: '',
                    feedArr: [],
                    reviewDone: "",
                    rating: "",
                },

                pagination: [],
                links: [],
                error: {
                    feedbackError: ''
                }
            },
            beforeMount() {
                let _this = this;
                _this.getReviews()
            },
            mounted() {
                let _this = this;
                _this.item.listingId = "{{$single_listing_details->id}}"
                _this.item.reviewDone = "{{$reviewDone}}"
                _this.item.rating = "5";
            },
            methods: {
                rate(rate) {
                    this.item.rating = rate;
                },
                addFeedback() {
                    let item = this.item;
                    this.makeError();
                    axios.post("{{route('user.review.push')}}", this.item)
                        .then(function (response) {
                            if (response.data.error) {
                                $('.reviewError').text(response.data.error.review[0])
                            }

                            console.log(response.data.status)

                            if (response.data.status == 'success') {
                                item.feedArr.unshift({
                                    review: response.data.data.review,
                                    review_user_info: response.data.data.review_user_info,
                                    rating: parseInt(response.data.data.rating),
                                    date_formatted: response.data.data.date_formatted,
                                });
                                item.reviewDone = 5;
                                item.feedback = "";
                                Notiflix.Notify.success("Review done")
                            }
                        })
                        .catch(function (error) {
                        });
                },
                makeError() {
                    if (!this.item.feedback) {
                        this.error.feedbackError = "Your review message field is required"
                    }
                },

                getReviews() {
                    var app = this;
                    axios.get("{{ route('listing.reviews.get',[$single_listing_details->id]) }}")
                        .then(function (res) {
                            app.item.feedArr = res.data.data.data;
                            app.pagination = res.data.data;
                            app.links = res.data.data.links;
                            app.links = app.links.slice(1, -1);
                        })

                },
                updateItems(page) {
                    var app = this;
                    if (page == 'back') {
                        var url = this.pagination.prev_page_url;
                    } else if (page == 'next') {
                        var url = this.pagination.next_page_url;
                    } else {
                        var url = page.url;
                    }
                    axios.get(url)
                        .then(function (res) {
                            app.item.feedArr = res.data.data.data;
                            app.pagination = res.data.data;
                            app.links = res.data.data.links;
                        })
                },
            }
        })


        var isAuthenticate = '{{\Illuminate\Support\Facades\Auth::check()}}';

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
    </script>
@endpush

