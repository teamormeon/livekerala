@extends(template().'layouts.app')
@section('title',trans('Listing'))

@section('banner_heading')
    @lang('All Listings')
@endsection

@push('style')
    <link rel="stylesheet" href="{{ asset('assets/global/css/frontend_leaflet.css') }}"/>
    <link rel="stylesheet" href="{{ asset('assets/global/css/frontendControl.FullScreen.css') }}"/>
    <link rel="stylesheet" href="{{ asset(template(true).'css/nice-select.css') }}">
    <link rel="stylesheet" href="{{ asset(template(true).'css/ion.rangeSlider.min.css') }}">
@endpush

@section('content')
    <section class="listing-section pb-0">
        <div class="container-fluid">
            <div class="row g-4">
                <div class="col-xl-8 col-lg-8">
                    <div class="main-content pb-4">
                        <div class="listing-topbar">
                            <div class="row align-items-center">
                                <div class="col">
                                    <button class="cmn-btn3" type="button" data-bs-toggle="offcanvas"
                                            data-bs-target="#offcanvasWithBothOptions"
                                            aria-controls="offcanvasWithBothOptions">
                                        <i class="fa-regular fa-filter-list"></i> @lang('Filters')
                                    </button>
                                </div>
                                <div class="col justify-content-end d-flex">
                                    <div id="results-count" data-total="{{ $all_listings->total() }}" data-current-page="{{ $all_listings->currentPage() }}">
                                        {{--Showing results here--}}
                                    </div>
                                </div>
                            </div>
                        </div>
                        @if( count($all_listings) > 0)
                            <input type="hidden" id="googleMapAppKey" value="{{ basicControl()->google_map_app_key }}">
                            <input type="hidden" id="google_map_id" value="{{ basicControl()->google_map_id }}">
                            <div class="row g-4">
                                @foreach($all_listings as $key => $listing)
                                    @php
                                        $total = $listing->reviews()[0]->total;
                                        $average_review = $listing->reviews()[0]->average;
                                    @endphp
                                    <div class="col-xxl-6">
                                        <div class="listing-box listing-box2 listing-map-box" data-lat="{{ $listing->lat }}"
                                             data-long="{{ $listing->long }}"
                                             data-title="@lang( Str::limit($listing->title, 30))"
                                             data-image="{{ getFile($listing->thumbnail_driver, $listing->thumbnail) }}"
                                             data-location="@lang($listing->address . ($listing->city_id != null && $listing->get_cities ? ', '.$listing->get_cities->getAddress() : ''))"
                                             data-route="{{ route('listing.details', $listing->slug) }}">
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
                                                <a href="javascript:void(0)"> <img
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
                                                            <span>( @lang($total.' reviews') )</span>
                                                        </ul>
                                                    </div>
                                                </div>
                                                <h5 class="title">
                                                    <a href="{{ route('listing.details', $listing->slug) }}">@lang($listing->title)</a>
                                                </h5>
                                                <p>@lang('Category : '.optional($listing)->getCategoriesName())</p>
                                                <div class="mt-15">
                                                    <p class="mb-1 contact-item"><i class="fa-regular fa-location-dot"></i>
                                                        @lang($listing->city_id != null && $listing->get_cities ? $listing->get_cities->getAddress() : $listing->address)
                                                    </p>
                                                    <p class="mb-1 contact-item"><i class="fa-regular fa-phone"></i>
                                                        {{ $listing->phone }}
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
                                @endforeach
                            </div>
                        @else
                            <div class="custom-not-found">
                                <img src="{{ asset(template(true).'img/error/error.png') }}" alt="image" class="img-fluid">
                            </div>
                        @endif
                    </div>
                    <div class="d-flex justify-content-center mb-4">
                        <nav aria-label="Page navigation example mt-3">
                            {{ $all_listings->appends($_GET)->links(template().'partials.pagination') }}
                        </nav>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-4">
                    <!-- Map section start -->
                    <div class="h-100" id="map"></div>
                    <!-- Map section end -->
                </div>
            </div>

        </div>
    </section>


    <div class="offcanvas offcanvas-start" data-bs-scroll="true" tabindex="-1" id="offcanvasWithBothOptions"
         aria-labelledby="offcanvasWithBothOptionsLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasWithBothOptionsLabel">@lang('Filters')</h5>
            <button type="button" class="cmn-btn-close" data-bs-dismiss="offcanvas" aria-label="Close">
                <i class="fa-regular fa-arrow-left"></i></button>
        </div>
        <form action="{{ route('listings') }}" method="get">
            <div class="offcanvas-body">
                <div class="widget-title">
                    <h6>@lang('Search')</h6>
                </div>
                <div class="row g-4">
                    <div class="col-12">
                        <input type="text" name="name" class="form-control"
                               value="{{ old('name', request()->name) }}" autocomplete="off"
                               placeholder="@lang('Listing name')"/>
                    </div>
                </div>
                <hr class="cmn-hr2">
                <div class="widget-title">
                    <h6>@lang('Filter by')</h6>
                </div>
                <div class="row g-4">
                    <div class="col-12">
                        <div id="formModal">
                            <select class="modal-select" name="category[]" multiple>
                                <option value="all" @if(request()->category && in_array('all', request()->category)) selected @endif>@lang('All Category')</option>
                                @foreach($all_categories as $category)
                                    <option value="{{ $category->id }}"
                                            @if(request()->category && in_array($category->id, request()->category)) selected @endif> @lang(optional($category->details)->name)
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-12">
                        <div id="formModal">
                            <select class="modal-select" name="location">
                                <option value="all"
                                        @if(request()->location == 'all') selected @endif>@lang('All Country')
                                </option>
                                @foreach($all_places as $place)
                                    @if($place != null)
                                        <option class="m-0" value="{{ $place->id }}"
                                                @if(request()->location == $place->id) selected @endif>@lang($place->name)
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-12">
                        <div id="formModal">
                            <select class="modal-select" name="city">
                                <option value="all"
                                        @if(request()->city == 'all') selected @endif>@lang('All City')
                                </option>
                                @foreach($uniqueCities as $city)
                                    @if($city != null)
                                        <option class="m-0" value="{{ $city->id }}"
                                                @if(request()->city == $city->id) selected @endif>@lang($city->name)
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>

                </div>
                <div class="accordion" id="accordionExample">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                @lang('Filter by Ratings')
                            </button>
                        </h2>
                        <div id="collapseOne" class="accordion-collapse collapse show" data-bs-parent="#accordionExample">
                            <div class="accordion-body">
                                <div class="checkbox-categories-area">
                                    <div class="section-inner">
                                        <div class="categories-list">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="5" name="rating[]"
                                                       id="flexCheckChecked17"
                                                       @if(isset(request()->rating))
                                                           @foreach(request()->rating as $data)
                                                               @if($data == 5) checked @endif
                                                    @endforeach
                                                    @endif/>
                                                <label class="form-check-label" for="flexCheckChecked17">
                                                    <ul class="star-list">
                                                        <li>
                                                            <i class="active fa-solid fa-star"></i>
                                                            <i class="active fa-solid fa-star"></i>
                                                            <i class="active fa-solid fa-star"></i>
                                                            <i class="active fa-solid fa-star"></i>
                                                            <i class="active fa-solid fa-star"></i>
                                                        </li>
                                                    </ul>
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="4" name="rating[]"
                                                       id="flexCheckChecked18"
                                                       @if(isset(request()->rating))
                                                           @foreach(request()->rating as $data)
                                                               @if($data == 4) checked @else @endif
                                                    @endforeach
                                                    @endif/>
                                                <label class="form-check-label" for="flexCheckChecked18">
                                                    <ul class="star-list">
                                                        <li>
                                                            <i class="active fa-solid fa-star"></i>
                                                            <i class="active fa-solid fa-star"></i>
                                                            <i class="active fa-solid fa-star"></i>
                                                            <i class="active fa-solid fa-star"></i>
                                                        </li>
                                                    </ul>
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="3" name="rating[]"
                                                       id="flexCheckChecked19"
                                                       @if(isset(request()->rating))
                                                           @foreach(request()->rating as $data)
                                                               @if($data == 3) checked @endif
                                                    @endforeach
                                                    @endif/>
                                                <label class="form-check-label" for="flexCheckChecked19">
                                                    <ul class="star-list">
                                                        <li>
                                                            <i class="active fa-solid fa-star"></i>
                                                            <i class="active fa-solid fa-star"></i>
                                                            <i class="active fa-solid fa-star"></i>
                                                        </li>
                                                    </ul>
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="2" name="rating[]"
                                                       id="flexCheckChecked20"
                                                       @if(isset(request()->rating))
                                                           @foreach(request()->rating as $data)
                                                               @if($data == 2) checked @endif
                                                    @endforeach
                                                    @endif/>
                                                <label class="form-check-label" for="flexCheckChecked20">
                                                    <ul class="star-list">
                                                        <li>
                                                            <i class="active fa-solid fa-star"></i>
                                                            <i class="active fa-solid fa-star"></i>
                                                        </li>
                                                    </ul>
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="1" name="rating[]"
                                                       id="flexCheckChecked21"
                                                       @if(isset(request()->rating))
                                                           @foreach(request()->rating as $data)
                                                               @if($data == 1) checked @endif
                                                    @endforeach
                                                    @endif/>
                                                <label class="form-check-label" for="flexCheckChecked21">
                                                    <ul class="star-list">
                                                        <li>
                                                            <i class="active fa-solid fa-star"></i>
                                                        </li>
                                                    </ul>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <button class="cmn-btn w-100" type="submit">@lang('submit')</button>
            </div>
        </form>
    </div>
@endsection

@push('extra-js')
    @if(basicControl()->is_google_map == 1)
        <script src="https://maps.googleapis.com/maps/api/js?key={{ basicControl()->google_map_app_key }}&loading=async&callback=initMap&libraries=marker" defer></script>
        <script src="{{ asset('assets/global/js/frontend_google_map.js') }}"></script>
    @else
        <script src="https://maps.googleapis.com/maps/api/js?key={{ basicControl()->google_map_app_key }}&loading=async"></script>
        <script src="{{ asset('assets/global/js/frontend_leaflet.js') }}"></script>
        <script src="{{ asset('assets/global/js/frontendControl.FullScreen.js') }}"></script>
        <script src="{{ asset('assets/global/js/frontend_map.js') }}"></script>
    @endif
    <script src="{{ asset(template(true).'js/jquery.nice-select.min.js') }}"></script>
    <script src="{{ asset(template(true).'js/ion.rangeSlider.min.js') }}"></script>
    <script src="{{ asset(template(true).'js/intlTelInput.min.js') }}"></script>

    <script>
        'use strict'

        var isAuthenticate = '{{ Auth::check() }}';

        $(document).ready(function () {
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

            const $resultsCount = $('#results-count');
            if ($resultsCount.length) {
                const total = parseInt($resultsCount.data('total'), 10);
                const currentPage = parseInt($resultsCount.data('current-page'), 10);
                const perPage = 6;
                const start = (currentPage - 1) * perPage + 1;
                const end = Math.min(start + perPage - 1, total);
                $resultsCount.html(`Showing <strong>${start} – ${end}</strong> of <strong>${total}</strong> results`);
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
    </script>
@endpush
