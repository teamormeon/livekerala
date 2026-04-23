@extends(template().'layouts.app')
@section('title',trans('Listing'))

@section('banner_heading')
    @lang('All Listings')
@endsection

@push('style')
    <link rel="stylesheet" href="{{ asset('assets/global/css/frontend_leaflet.css') }}"/>
    <link rel="stylesheet" href="{{ asset('assets/global/css/frontendControl.FullScreen.css') }}"/>
@endpush

@section('content')
    <section class="listing-section">
        <div class="container-fluid">
            <div class="row">
                <div class="col-xl-2 col-lg-2 col-sm-12 my-4">
                    <form action="{{ route('listings') }}" method="get">
                        <div class="filter-area">
                            <div class="filter-box">
                                <h5>@lang('search')</h5>
                                <div class="input-group mb-3">
                                    <input type="text" name="name" class="form-control bg-white"
                                           value="{{ old('name', request()->name) }}" autocomplete="off"
                                           placeholder="@lang('Listing name')"/>
                                </div>
                                <div class="input-group mb-3">
                                    <select class="js-example-basic-single form-control" name="location">
                                        <option selected disabled>@lang('Select Country')</option>
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

                                <div class="input-group mb-3">
                                    <select class="js-example-basic-single form-control" name="city">
                                        <option selected disabled>@lang('Select City')</option>
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

                                <div class="input-group mb-3">
                                    <select class="listing__category__select2 form-control" name="category[]" multiple>
                                        <option value="all"
                                                @if(request()->category && in_array('all', request()->category))
                                                    selected
                                                @endif>@lang('All Category')</option>
                                        @foreach($all_categories as $category)
                                            @if($category != null)
                                            <option value="{{ $category->id }}"
                                                 @if(request()->category && in_array($category->id, request()->category))
                                                        selected
                                                @endif> @lang(optional($category->details)->name)
                                            </option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>



                            <div class="filter-box">
                                <h5>@lang('Filter by Ratings') </h5>
                                <div class="check-box">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="5" name="rating[]"
                                               id="check1"
                                               @if(isset(request()->rating))
                                               @foreach(request()->rating as $data)
                                               @if($data == 5) checked @endif
                                            @endforeach
                                            @endif/>

                                        <label class="form-check-label" for="check1">
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                        </label>
                                    </div>

                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="4" name="rating[]"
                                               id="check2"
                                               @if(isset(request()->rating))
                                               @foreach(request()->rating as $data)
                                               @if($data == 4) checked @else @endif
                                            @endforeach
                                            @endif/>

                                        <label class="form-check-label" for="check2">
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                        </label>
                                    </div>

                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="3" name="rating[]"
                                               id="check3"
                                               @if(isset(request()->rating))
                                               @foreach(request()->rating as $data)
                                               @if($data == 3) checked @endif
                                            @endforeach
                                            @endif/>
                                        <label class="form-check-label" for="check3">
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                        </label>
                                    </div>

                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="2" name="rating[]"
                                               id="check4"
                                               @if(isset(request()->rating))
                                               @foreach(request()->rating as $data)
                                               @if($data == 2) checked @endif
                                            @endforeach
                                            @endif/>
                                        <label class="form-check-label" for="check4">
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                        </label>
                                    </div>

                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="1" name="rating[]"
                                               id="check5"
                                               @if(isset(request()->rating))
                                               @foreach(request()->rating as $data)
                                               @if($data == 1) checked @endif
                                            @endforeach
                                            @endif/>
                                        <label class="form-check-label" for="check5">
                                            <i class="fas fa-star"></i>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <button class="btn-custom w-100" type="submit">@lang('submit')</button>
                        </div>
                    </form>
                </div>


                <div class="col-xl-6 col-lg-6 col-sm-12 my-4">
                    @if( 0 <count($all_listings))
                        <input type="hidden" id="googleMapAppKey" value="{{ basicControl()->google_map_app_key }}">
                        <input type="hidden" id="google_map_id" value="{{ basicControl()->google_map_id }}">
                        <div class="row g-4">
                            @forelse($all_listings as $key => $listing)
                                @php
                                    $total = $listing->reviews()[0]->total;
                                    $average_review = $listing->reviews()[0]->average;
                                @endphp
                                <div class="col-lg-6 col-md-6 col-sm-12">
                                    <div class="listing-box listing-map-box" data-lat="{{ $listing->lat }}"
                                         data-long="{{ $listing->long }}"
                                         data-title="@lang( Str::limit($listing->title, 30))"
                                         data-image="{{ getFile($listing->thumbnail_driver, $listing->thumbnail) }}"
                                         data-location="@lang($listing->address . ($listing->city_id != null ? ', '.$listing->get_cities?->getAddress() : ''))"
                                         data-route="{{ route('listing.details', $listing->slug) }}">
                                        <div class="img-box">
                                            <img class="img-fluid"
                                                 src="{{ getFile($listing->thumbnail_driver, $listing->thumbnail) }}"
                                                 alt="{{ basicControl()->site_title }}"/>
                                            <button class="save wishList" type="button" id="{{$key}}"
                                                    data-user="{{ optional($listing->get_user)->id }}"
                                                    data-purchase="{{ $listing->purchase_package_id }}"
                                                    data-listing="{{ $listing->id }}">
                                                @if($listing->get_favourite_count > 0)
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
                                                <span>( @lang($total.' reviews') )</span>
                                            </div>

                                            <h5 class="title">@lang(Str::limit($listing->title, 30))</h5>
                                            <a class="author"
                                               href="{{ route('profile', optional($listing->get_user)->username) }}">
                                                @lang(optional($listing->get_user)->firstname) @lang(optional($listing->get_user)->lastname)
                                            </a>
                                            <p class="mb-2">
                                                <span class="">@lang('Category'): </span> @lang(optional($listing)->getCategoriesName())
                                            </p>
                                            <p class="address mt-1">
                                                <i class="fal fa-map-marker-alt"></i>
                                                @lang($listing->city_id != null ? $listing->get_cities?->getAddress() : $listing->address)
                                            </p>
                                            <a href="{{ route('listing.details', $listing->slug) }}"
                                               class="btn-custom">@lang('View details')</a>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="custom-not-found">
                                    <img src="{{ asset(template(true).'img/no_data_found.png') }}"
                                         alt="{{ basicControl()->site_title }}" class="img-fluid">
                                </div>
                            @endforelse


                            <div class="col-lg-12 d-flex justify-content-center mt-5">
                                <nav aria-label="Page navigation example mt-3">
                                    {{ $all_listings->appends($_GET)->links(template().'partials.pagination') }}
                                </nav>
                            </div>
                        </div>
                    @else
                        <div class="custom-not-found">
                            <img src="{{ asset(template(true).'img/no_data_found.png') }}" alt="image"
                                 class="img-fluid">
                        </div>
                    @endif
                </div>

                <div class="col-xl-4 col-lg-4 col-sm-12">
                    <div class="h-100" id="map"></div>
                </div>
            </div>
        </div>
    </section>

@endsection

@push('script')
    @if(basicControl()->is_google_map == 1)
        <script src="https://maps.googleapis.com/maps/api/js?key={{ basicControl()->google_map_app_key }}&loading=async&callback=initMap&libraries=marker" defer></script>
        <script src="{{ asset('assets/global/js/frontend_google_map.js') }}"></script>
    @else
        <script src="https://maps.googleapis.com/maps/api/js?key={{ basicControl()->google_map_app_key }}&loading=async"></script>
        <script src="{{ asset('assets/global/js/frontend_leaflet.js') }}"></script>
        <script src="{{ asset('assets/global/js/frontendControl.FullScreen.js') }}"></script>
        <script src="{{ asset('assets/global/js/frontend_map.js') }}"></script>
    @endif

    <script>
        'use strict'

        $(".listing__category__select2").select2({
            width: '100%',
            placeholder: '@lang("Select Categories")',
        });

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
    </script>
@endpush
