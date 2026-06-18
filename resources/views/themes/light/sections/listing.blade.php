<!-- popular listings -->

@if(isset($listing['popularListings']) && $listing['popularListings']->isNotEmpty())
    <section class="popular-listings">
        <div class="overlay">
            <div class="container">
                @if(isset($listing['single']))
                    <div class="row">
                        <div class="col-12">
                            <div class="header-text text-center mb-5">
                                <h5>@lang($listing['single']['title'])</h5>
                                <h3>@lang($listing['single']['sub_title'])</h3>
                                <p class="mx-auto">
                                    @lang($listing['single']['description'])
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
                <div class="row g-4">
                    @forelse($listing['popularListings'] as $key => $listing)
                        @php
                            $total = $listing->reviews()[0]->total;
                            $average_review = $listing->reviews()[0]->average;
                        @endphp
                        <div class="col-lg-3 col-md-6">
                            <div class="listing-box">
                                <div class="img-box">
                                    <img class="img-fluid" src="{{ getFile($listing->thumbnail_driver, $listing->thumbnail) }}" alt="image"/>
                                    <button class="save wishList" type="button" id="{{$key}}" data-user="{{ optional($listing->get_user)->id }}"
                                            data-purchase="{{ $listing->purchase_package_id }}" data-listing="{{ $listing->id }}">
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
                                        <span>(@lang($total.' reviews'))</span>
                                    </div>
                                    <a class="title"
                                       href="{{ route('listing.details',$listing->slug) }}">
                                        @lang(Str::limit($listing->title, 20))
                                    </a>
                                    <p class="mb-2 mt-2">
                                        <span class="">@lang('Category'): </span> @lang(optional($listing)->getCategoriesName())
                                    </p>
                                    <a class="author" href="{{ route('profile', optional($listing->get_user)->username) }}"> @lang(optional($listing->get_user)->firstname) @lang(optional($listing->get_user)->lastname) </a>
                                    <p class="address">
                                        <i class="fal fa-map-marker-alt"></i>
                                        @lang($listing->city_id != null ? $listing->get_cities?->getAddress() : $listing->address)
                                    </p>
                                </div>
                            </div>
                        </div>
                    @empty
                    @endforelse
                </div>
                <div class="row text-center mt-5">
                    <div class="col">
                        <a href="{{ route('listings') }}" class="btn-custom">
                            @lang('View More')
                            <i class="fal fa-angle-double-right"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endif


<script src="{{asset('assets/global/js/jquery.min.js') }}"></script>
<script>
    'use strict'
    var isAuthenticate = '{{\Illuminate\Support\Facades\Auth::check()}}';

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



