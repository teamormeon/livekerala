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
            background-color: #f7f7f7;
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
    <div class="banner-slider-section">
        <div class="owl-carousel owl-theme banner-slider magnific-popup">
            @forelse($single_listing_details->get_listing_images as $listing_image)
                <div class="item">
                    <a href="{{ getFile($listing_image->driver, $listing_image->listing_image)}}"
                       rel="prettyPhoto[gallery1]" class="cursorimage">
                        <img src="{{ getFile($listing_image->driver, $listing_image->listing_image)}}"/>
                    </a>
                </div>
            @empty
                <div class="item">
                    <a href="{{ getFile($single_listing_details->thumbnail_driver, $single_listing_details->thumbnail)}}"
                       rel="prettyPhoto[gallery1]" class="cursorimage">
                        <img
                            src="{{ getFile($single_listing_details->thumbnail_driver, $single_listing_details->thumbnail)}}"/>
                    </a>
                </div>
            @endforelse
        </div>
    </div>

    <div class="listing-header-section">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-8">
                    <h3>@lang($single_listing_details->title)</h3>
                    <p>@lang('Category') : @lang($single_listing_details->getCategoriesName()) </p>
                    @if($single_listing_details->address)
                        <p class=" mb-1 contact-item"><i class="fa-regular fa-phone"></i>
                            @lang($single_listing_details->phone)
                        </p>
                        <p class=" mb-1 contact-item"><i class="fa-regular fa-envelope"></i>
                            @lang($single_listing_details->email)
                        </p>
                        <p class=" mb-1 contact-item"><i class="fa-regular fa-location-dot"></i>
                            @lang($single_listing_details->get_cities && $single_listing_details->get_cities?->getAddress() ? $single_listing_details->get_cities?->getAddress() : $single_listing_details->address)

                        </p>
                    @endif
                    <div class="d-flex gap-2 mt-20">
                        <button class="cmn-btn4 share">
                            <i class="fa-regular fa-share-nodes"></i>
                            <div id="shareBlock"></div>
                        </button>
                        <button type="button" class="cmn-btn4">
                            <i class="far fa-eye"></i>{{ $total_listing_view }}
                        </button>
                    </div>
                </div>
                <div class="col-md-4 align-items-md-end d-flex flex-column gap-3">
                    <div class="review">
                        <ul class="reviews d-flex align-items-center gap-2">
                            <li class="star-rating">
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
                                    <i class="fa fa-star-half-alt"></i>
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
                            <span
                                class="review-count">({{ $single_listing_details->reviews()[0]->total }} reviews)</span>
                        </ul>
                    </div>

                    @if($single_listing_details->get_user->website)
                        <a href="{{ $single_listing_details->get_user->website }}" target="_blank"
                           class=" mb-1 contact-item"><i class="fa-regular fa-globe"></i>
                            @lang(optional($single_listing_details->get_user)->website)
                        </a>
                    @endif

                    @if(count($single_listing_details->get_social_info) > 0)
                        <ul class="social-box">
                            @foreach($single_listing_details->get_social_info as $social)
                                <li><a href="{{ $social->social_url }}" target="_blank"><i
                                            class="{{ $social->social_icon }}"></i></a></li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <section class="listing-details-section">
        <div class="container">
            <div class="row g-4 g-sm-5">
                <div class="col-lg-8">
                    <div class="card mb-30">
                        <div class="card-header">
                            <h4> @lang('Description')</h4>
                        </div>
                        <div class="card-body">
                            <p class="mb-0">
                                {!! $single_listing_details->description !!}
                            </p>
                        </div>
                    </div>
                    <div class="card mb-30">
                        <div class="card-header">
                            <h4> @lang('Amenities')</h4>
                        </div>
                        <div class="card-body">
                            <div class="row g-4">
                                @forelse($single_listing_details->get_listing_amenities as $amenity)
                                    <div class=" col-md-4 col-sm-6">
                                        <div class="cmn-box2">
                                            <div class="icon-box">
                                                <i class="{{ optional($amenity->get_amenity)->icon }}"></i>
                                            </div>
                                            <div class="content-box">
                                                <h6>@lang(optional(optional($amenity->get_amenity)->details)->title)</h6>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                @endforelse
                            </div>
                        </div>
                    </div>

                    @if(optional($single_listing_details->get_package)->is_video != 0 && $single_listing_details->youtube_video_id != null)
                        <div class="card mb-30">
                            <div class="card-header">
                                <h4>@lang('Video')</h4>
                            </div>
                            <div class="card-body">
                                <div class="video-box">
                                    <img class="w-100"
                                         src="{{ getFile($single_listing_details->thumbnail_driver, $single_listing_details->thumbnail) }}"
                                         alt="image">
                                    <a data-fancybox=""
                                       href="https://www.youtube.com/embed/{{ $single_listing_details->youtube_video_id }}?controls=0"
                                       class="video-play-btn">
                                        <i class="fa-regular fa-play"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif


                    <div class="card mb-30">
                        <div class="card-body">
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
                                                    </div>

                                                    <div class="text-box">
                                                        <span>{{ Str::limit($listing_product->product_title, 20) }}</span>
                                                        <span
                                                            class="price"> {{ currencyPosition($listing_product->product_price) }} </span>
                                                    </div>
                                                    <hr class="p-0 m-0">
                                                    <div class="d-flex justify-content-center p-2">
                                                        <a href="javascript:void(0)"
                                                           class="listing_product_id text-uppercase"
                                                           data-bs-toggle="modal"
                                                           data-bs-target="#productDetailsModal{{ $listing_product->id }}"
                                                           data-listingproductid="{{ $listing_product->id }}">
                                                            @lang('view details')
                                                        </a>
                                                    </div>
                                                </div>

                                                @push('frontend_modal')
                                                    <div class="modal fade"
                                                         id="productDetailsModal{{ $listing_product->id }}"
                                                         data-bs-keyboard="false" tabindex="-1"
                                                         aria-labelledby="productDetailsModalLabel" aria-hidden="true">
                                                        <div
                                                            class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h4 class="modal-title"
                                                                        id="productDetailsModalLabel">@lang($listing_product->product_title)</h4>
                                                                    <button type="button" class="cmn-btn-close"
                                                                            data-bs-dismiss="modal" aria-label="Close">
                                                                        <i class="fa-light fa-xmark"></i>
                                                                    </button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <div class="row g-4">
                                                                        <div class="col-lg-6">
                                                                            <div
                                                                                id="mainCarousel{{ $listing_product->id }}"
                                                                                class="carousel mx-auto fancybox-carousel is-draggable mb-0">
                                                                                <div class="carousel__viewport">
                                                                                    <div class="carousel__track"
                                                                                         style="transform: translate3d(0px, 0px, 0px) scale(1);">
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
                                                                                </div>
                                                                                <div class="carousel__nav">
                                                                                    <button title="Next slide"
                                                                                            class="carousel__button is-next"
                                                                                            tabindex="0">
                                                                                        <svg
                                                                                            xmlns="http://www.w3.org/2000/svg"
                                                                                            viewBox="0 0 24 24"
                                                                                            tabindex="-1">
                                                                                            <path
                                                                                                d="M9 3l9 9-9 9"></path>
                                                                                        </svg>
                                                                                    </button>
                                                                                    <button title="Previous slide"
                                                                                            class="carousel__button is-prev"
                                                                                            tabindex="0">
                                                                                        <svg
                                                                                            xmlns="http://www.w3.org/2000/svg"
                                                                                            viewBox="0 0 24 24"
                                                                                            tabindex="-1">
                                                                                            <path
                                                                                                d="M15 3l-9 9 9 9"></path>
                                                                                        </svg>
                                                                                    </button>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-lg-6">
                                                                            <h4 class="mb-10">@lang($listing_product->product_title)</h4>
                                                                            <p class="mb-0 productDescription">@lang($listing_product->product_description)</p>
                                                                            <h4 class="mt-10">@lang('Price')
                                                                                : {{ currencyPosition($listing_product->product_price) }}</h4>
                                                                        </div>
                                                                    </div>
                                                                    <div class="card mt-30">
                                                                        <div class="card-body">
                                                                            <h4>@lang('Make Query')</h4>
                                                                            <form
                                                                                action="{{ route('user.send.product.query') }}"
                                                                                method="post"
                                                                                enctype="multipart/form-data">
                                                                                @csrf
                                                                                <input type="hidden" name="product_id"
                                                                                       value="{{ $listing_product->id }}"
                                                                                       class="form-control">
                                                                                <input type="hidden" name="listing_id"
                                                                                       value="{{ @$single_listing_details->id }}"
                                                                                       class="form-control">
                                                                                <textarea
                                                                                    class="form-control @error('message') is-invalid @enderror text-dark"
                                                                                    cols="30" rows="3"
                                                                                    autocomplete="off" name="message"
                                                                                    placeholder="@lang('Your message')"></textarea>
                                                                                <div class="invalid-feedback">
                                                                                    @error('message') @lang($message) @enderror
                                                                                </div>
                                                                                <button class="cmn-btn mt-20">
                                                                                    @lang('submit')
                                                                                </button>
                                                                            </form>
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
                    </div>

                    @if(isset($single_listing_details->form))
                        <div class="sidebar-widget-area">
                            <form action="{{ route('collect.listing.form.data') }}" method="post"
                                  enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="dynamic_forms_id"
                                       value="{{ $single_listing_details->form->id }}">
                                <input type="hidden" name="listing_id" value="{{ $single_listing_details->id }}">
                                <h5 class="title">@lang(optional($single_listing_details->form)->name ?? 'Data Collection Form')</h5>
                                <div class="row g-3">
                                    @foreach(optional($single_listing_details->form)->input_form as $k => $v)
                                        @if($v->type == "text")
                                            <div class="col-12">
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
                                            <div class="col-12">
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
                                            <div class="col-12">
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
                                            <div class="col-12">
                                                <label class="form-label">@lang($v->field_name)</label>
                                                <select class="cmn-select2 form-select" name="{{ $k }}" id="">
                                                    <option selected disabled>@lang('Choose '.$v->field_name)</option>
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
                                                <textarea class="form-control" cols="30" rows="3" autocomplete="off"
                                                          name="{{ $k }}" placeholder="{{ $v->field_name }}"
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
                                                class="cmn-btn w-100">@lang(optional($single_listing_details->form)->button_text ?? 'Submit')</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    @endif

                    <div class="card mb-30">
                        <div class="card-header">
                            <h4>@lang('Customer Feedback')</h4>
                        </div>
                        <div class="card-body">
                            <div class="average-review">
                                <div class="row align-items-center">
                                    <div class="col-4">
                                        <div class="card-box">
                                            <h2 class="mb-2 averageRatings">{{ number_format($average_review, 1) ?? 0 }}</h2>
                                            <div class="rating mb-2 star-rating">
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
                                                    <i class="fa fa-star-half-alt"></i>
                                                    @php
                                                        $j = $j + 1;
                                                    @endphp
                                                @endif
                                                @if($average_review == 0 || $average_review != null)
                                                    @for($j; $j < 5; $j++)
                                                        <i class="far fa-star"></i>
                                                    @endfor
                                                @endif
                                            </div>
                                            <span class="review-count">{{ $single_listing_details->reviews()[0]->total }} reviews</span>
                                        </div>
                                    </div>


                                    <div class="col-8">
                                        <div class="progress-wrapper">
                                            @foreach ([5, 4, 3, 2, 1] as $star)
                                                @php
                                                    // Total reviews count
                                                    $totalReviews = $single_listing_details->reviews()[0]->total;

                                                    // Count for the current star rating
                                                    $currentStarCount = $ratingProgressCounts[$star] ?? 0;

                                                    // Calculate percentage for the current star rating
                                                    $percentage = $totalReviews > 0 ? ($currentStarCount / $totalReviews) * 100 : 0;
                                                @endphp

                                                <div class="mb-2">
                                                    <div class="index">@lang($star .' Stars')</div>
                                                    <div class="progress" role="progressbar" aria-label="5 Stars"
                                                         aria-valuenow="0" aria-valuemin="0" aria-valuemax="10">
                                                        <div class="progress-bar" style="width: {{$percentage}}%"></div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="review-app">
                        <div class="card">
                            <div class="card-header">
                                <h4>@lang('Reviews')</h4>
                            </div>
                            <div class="card-body">
                                <div class="review-item mb-20" v-for="(obj, index) in item.feedArr">
                                    <div class="d-flex justify-content-between flex-wrap gap-2 mb-15">
                                        <div class="author-profile">
                                            <a href="" class="img-box"><img :src="obj.review_user_info.imgPath" alt=""></a>
                                            <div class="text-box">
                                                <h5 class="mb-0">@{{obj.review_user_info.firstname + ' ' +
                                                    obj.review_user_info.lastname}}</h5>
                                                <small>@{{obj.date_formatted}}</small>
                                            </div>
                                        </div>
                                        <ul class="reviews d-flex align-items-center gap-3">
                                            <li>
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
                                            </li>
                                        </ul>
                                    </div>
                                    <p class="mb-0">@{{ obj.review }}</p>
                                </div>

                                <div class="custom-not-found p-0" v-if="item.feedArr.length<1">
                                    <img src="{{ asset(template(true).'img/error/error.png') }}"
                                         alt="image" class="img-fluid">
                                </div>

                                <div class="row mt-5">
                                    <div class="col d-flex justify-content-center">
                                        @include(template().'partials.vuePaginate')
                                    </div>
                                </div>

                                @auth
                                    @if($reviewDone <= 0 && $single_listing_details->user_id != Auth::id())
                                        <div class="review-box mt-30">
                                            <h4>@lang('Add Review')</h4>
                                            <p>
                                                @lang('Writing great reviews may help others discover the places that are just apt for them')
                                            </p>
                                            <div class="ratings">
                                                <input type="radio" id="star1" name="rating" value="5" @click="rate(5)">
                                                <label for="star1" title="text"></label>
                                                <input type="radio" id="star2" name="rating" value="4" @click="rate(4)">
                                                <label for="star2" title="text"></label>
                                                <input checked type="radio" id="star3" name="rating" value="3"
                                                       @click="rate(3)">
                                                <label for="star3" title="text"></label>
                                                <input type="radio" id="star4" name="rating" value="2" @click="rate(2)">
                                                <label for="star4" title="text"></label>
                                                <input type="radio" id="star5" name="rating" value="1" @click="rate(1)">
                                                <label for="star5" title="text"></label>
                                            </div>
                                            <textarea class="form-control mt-20" id="exampleFormControlTextarea1"
                                                      name="review" v-model="item.feedback"
                                                      placeholder="@lang('Type here')" rows="5"></textarea>
                                            <span class="text-danger reviewError"></span>
                                            <div>
                                                <button class="cmn-btn mt-20"
                                                        @click.prevent="addFeedback">@lang('submit')</button>
                                            </div>
                                        </div>
                                    @endif
                                @else
                                    <div class="add-review" v-if="item.reviewDone < 1">
                                        <div class="d-flex justify-content-between">
                                            <h4>@lang('Add Review')</h4>
                                            <a href="{{ route('login') }}"
                                               class="cmn-btn btn-sm">@lang('Login to review')</a>
                                        </div>
                                    </div>
                                @endauth
                            </div>
                        </div>
                    </div>

                </div>
                <div class="col-lg-4">
                    <div class="sidebar-widget-area">
                        <div class="profile-box2">
                            <div class="image-area">
                                <img class="cover"
                                     src="{{ getFile(optional($single_listing_details->get_user)->cover_image_driver, optional($single_listing_details->get_user)->cover_image) }}"
                                     alt="image">
                                <img class="profile-img"
                                     src="{{ getFile(optional($single_listing_details->get_user)->image_driver, optional($single_listing_details->get_user)->image) }}"
                                     alt="image">
                            </div>
                            <div class="content-area">
                                <h5>@lang(optional($single_listing_details->get_user)->firstname) @lang(optional($single_listing_details->get_user)->lastname)</h5>
                                <p class="mb-0">@lang('Member since') @lang(optional($single_listing_details->get_user)->created_at->format('M Y')) </p>
                                <p class="mb-0">
                                    @if($total_listings_an_user['totalListing'] > 1)
                                        {{ $total_listings_an_user['totalListing'] }} @lang('Listings')
                                    @else
                                        {{ $total_listings_an_user['totalListing'] }} @lang('Listing')
                                    @endif
                                </p>
                                <a href="{{ route('profile', optional($single_listing_details->get_user)->username) }}"
                                   class="cmn-btn mt-20">@lang('view profile')</a>
                            </div>
                        </div>
                    </div>
                    <div class="sidebar-widget-area">
                        <h5 class="title">@lang('Contact Seller')</h5>
                        <div class="contact-box">
                            @if(optional($single_listing_details->get_user)->phone)
                                <p class="contact-item mb-0"><i
                                        class="fa-regular fa-phone"></i> {{ optional($single_listing_details->get_user)->phone }}
                                </p>
                            @endif
                            @if(optional($single_listing_details->get_user)->email)
                                <p class="contact-item mb-0"><i
                                        class="fa-regular fa-envelope"></i>{{ optional($single_listing_details->get_user)->email }}
                                </p>
                            @endif
                            @if(optional($single_listing_details->get_user)->fullAddress)
                                <p class="contact-item mb-0"><i
                                        class="fa-regular fa-location-dot"></i> @lang(optional($single_listing_details->get_user)->fullAddress)
                                </p>
                            @endif
                            @if(optional($single_listing_details->get_user)->website)
                                <p class="contact-item mb-0"><i class="fal fa-globe"></i><a class=""
                                                                                            href="javascript:void(0)">@lang(optional($single_listing_details->get_user)->website)</a>
                                </p>
                            @endif
                            @if(count(optional($single_listing_details->get_user)->get_social_links_user) > 0)
                                <ul class="social-box">
                                    @foreach(optional($single_listing_details->get_user)->get_social_links_user as $social)
                                        <li><a href="{{ $social->social_url }}" target="_blank"><i
                                                    class="{{ $social->social_icon }}"></i></a></li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>

                    <div class="sidebar-widget-area">
                        <div class="listing-map-box" data-lat="{{ $single_listing_details->lat }}"
                             data-long="{{ $single_listing_details->long }}"
                             data-title="@lang( Str::limit($single_listing_details->title, 30))"
                             data-image="{{ getFile($single_listing_details->thumbnail_driver, $single_listing_details->thumbnail) }}"
                             data-location="@lang($single_listing_details->address . ($single_listing_details->city_id != null ? ', '.optional($single_listing_details->get_cities)->getAddress() : ''))"
                             data-route="{{ route('listing.details', $single_listing_details->slug) }}"></div>
                        <h5 class="title">@lang('Map')</h5>
                        <div class="contact-box">
                            <div class="sidebar-map" id="map"></div>
                        </div>
                    </div>

                    <div class="sidebar-widget-area">
                        <h5 class="title">@lang('Opening Hours')</h5>
                        <div class="cmn-list">
                            @forelse($single_listing_details->get_business_hour as $business_hour)
                                @if($business_hour->start_time)
                                    <div class="item">
                                        <span>@lang($business_hour->working_day)</span>
                                        <span>{{ \Carbon\Carbon::parse($business_hour->start_time)->format('h a') }} - {{ \Carbon\Carbon::parse($business_hour->end_time)->format('h a') }}</span>
                                    </div>
                                @else
                                    <div class="item">
                                        <span>@lang($business_hour->working_day)</span>
                                        <span>@lang('Closed')</span>
                                    </div>
                                @endif
                            @empty
                            @endforelse
                        </div>
                    </div>

                    <div class="sidebar-widget-area">
                        <div class="d-flex justify-content-between">
                            <img src="{{ getFile(basicControl()->logo_driver,basicControl()->logo) }}" class="img-fluid"
                                 alt="image">
                            <div>
                                <h6 class="mb-2">@lang('Claim This Business')</h6>
                                <button class="cmn-btn btn-sm p-1 w-100" data-bs-toggle="modal"
                                        data-bs-target="#claimBusiness">@lang('Claim Now')</button>
                            </div>
                        </div>
                    </div>

                    <div class="sidebar-widget-area">
                        <h5 class="title">@lang('Send a Message')</h5>
                        <form action="{{ route('user.send.listing.message', $single_listing_details->id) }}"
                              method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="row g-4">
                                <div class="col-12">
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
                                <div class="col-12">
                                    <textarea class="form-control @error('message') is-invalid @enderror" cols="30"
                                              rows="3" autocomplete="off" name="message"
                                              placeholder="@lang('Your message')"></textarea>
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
        </div>
    </section>

    @if($category_wise_listing->isNotEmpty())
        <section class="related-listing-section">
            <div class="container">
                <div class="row">
                    <div class="section-header mb-20 d-flex flex-column flex-sm-row justify-content-between gap-3">
                        <div>
                            <h3>@lang('Related Listings')</h3>
                        </div>
                    </div>
                </div>
                <div class="row">
                    @foreach($category_wise_listing as $key => $listing)
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
                                                src="{{ getFile($listing->thumbnail_driver, $listing->thumbnail) }}"
                                                alt="image"></a>
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
                                                @lang($listing->address)
                                                , @lang(optional(optional($listing->get_place)->details)->place)
                                            </p>

                                            <p class="contact-item"><i
                                                    class="fa-regular fa-phone"></i> {{ $listing->phone }}
                                            </p>
                                            <a href="{{ route('profile', optional($listing->get_user)->username) }}"
                                               class="contact-item"><i class="fa-regular fa-user"></i>
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
                    @endforeach
                </div>
            </div>
        </section>
    @endif

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
                                    <button class="cmn-btn w-100">
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
    <link rel="stylesheet" href="{{ asset(template(true).'css/magnific-popup.css') }}"/>
@endpush

@push('extra-js')
    <!-- fancybox slider -->
    <script src="{{ asset(template(true).'js/fancybox.umd.js') }}"></script>
    <script src="{{ asset(template(true).'js/jquery.magnific-popup.min.js') }}"></script>
    <script src="{{ asset(template(true).'js/socialSharing.js') }}"></script>
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
    <script>
        'use strict'
        $(document).ready(function () {
            $('.listingProductCarousel').owlCarousel({
                loop: false,
                margin: 10,
                nav: false,
            });

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
                            if (response.data.status == 'success') {
                                item.feedArr.unshift({
                                    review: response.data.data.review,
                                    review_user_info: response.data.data.review_user_info,
                                    rating: parseInt(response.data.data.rating),
                                    date_formatted: response.data.data.date_formatted,
                                });
                                item.reviewDone = 5;
                                item.feedback = "";

                                const reviewCountSpans = document.querySelectorAll(".review-count");
                                reviewCountSpans.forEach((reviewCountSpan) => {
                                    const currentCount = parseInt(reviewCountSpan.innerText) || 0;
                                    reviewCountSpan.innerText = `${currentCount + 1} reviews`;
                                });

                                const newRating = response.data.data.rating;
                                updateStarRating(newRating);
                                updateProgressBars(newRating);
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


        function updateStarRating(averageReview) {
            const starContainers = document.querySelectorAll(".star-rating");
            starContainers.forEach((starContainer) => {
                starContainer.innerHTML = "";

                const fullStars = Math.floor(averageReview);
                const hasHalfStar = averageReview % 1 !== 0;
                const emptyStars = 5 - fullStars - (hasHalfStar ? 1 : 0);

                for (let i = 0; i < fullStars; i++) {
                    const star = document.createElement("i");
                    star.classList.add("fas", "fa-star");
                    starContainer.appendChild(star);
                }

                if (hasHalfStar) {
                    const halfStar = document.createElement("i");
                    halfStar.classList.add("fa", "fa-star-half-alt");
                    starContainer.appendChild(halfStar);
                }

                for (let i = 0; i < emptyStars; i++) {
                    const emptyStar = document.createElement("i");
                    emptyStar.classList.add("far", "fa-star");
                    starContainer.appendChild(emptyStar);
                }
            });
        }

        function updateProgressBars(averageRating) {
            const ratings = [5, 4, 3, 2, 1];
            const totalReviews = parseInt(document.querySelector(".review-count").innerText.split(" ")[0]) || 0;

            ratings.forEach((rating) => {
                const progressBar = document.querySelector(`.progress-wrapper .progress[aria-label="${averageRating} Stars"] .progress-bar`);

                const percentage = (rating <= averageRating)
                    ? 100
                    : ((rating === Math.floor(averageRating)) ? (averageRating % 1) * 100 : 0);

                const reviewsAtThisRating = totalReviews * (percentage / 100);

                if (progressBar) {
                    progressBar.style.width = `${reviewsAtThisRating}%`;
                    progressBar.setAttribute('aria-valuenow', reviewsAtThisRating.toFixed(1));
                }
            });

            const averageRatingsElement = document.querySelector(".averageRatings");
            const currentAverage = parseFloat(averageRatingsElement.innerText) || 0;
            const newReviewCount = totalReviews + 1;
            const newAverage = ((currentAverage * totalReviews) + averageRating) / newReviewCount;
            averageRatingsElement.innerText = newAverage.toFixed(1);

            const reviewCountSpans = document.querySelectorAll(".review-count");
            reviewCountSpans.forEach((reviewCountSpan) => {
                reviewCountSpan.innerText = `${newReviewCount} reviews`;
            });
        }

    </script>
@endpush

