<!-- popular listings -->
@if(isset($listing['popularListings']) && $listing['popularListings']->isNotEmpty())
    <section class="featured-section">
        <div class="container">
            <div class="row">
                <div class="section-header text-center">
                    <div class="section-subtitle">@lang($listing['single']['title'])</div>
                    <h3 class="section-title mx-auto">@lang($listing['single']['sub_title'])</h3>
                    <p class="cmn-para-text mx-auto ">@lang($listing['single']['description'])</p>
                </div>
            </div>
            <div class="row g-4 justify-content-center">
                @forelse($listing['popularListings']->take(3) as $key => $listing)
                    @php
                        $total = $listing->reviews()[0]->total;
                        $average_review = $listing->reviews()[0]->average;
                    @endphp
                    <div class="col-lg-4 col-md-6">
                        <div class="listing-box">
                            <div class="rate-area">
                                <a href="javascript:void(0)" class="item wishList {{ $listing->get_favourite_count > 0 ? 'active' : '' }}"
                                   id="{{$key}}" data-user="{{ optional($listing->get_user)->id }}"
                                   data-purchase="{{ $listing->purchase_package_id }}"
                                   data-listing="{{ $listing->id }}"><i class="fa-regular fa-heart"></i></a>
                            </div>
                            <div class="image-area">
                                <a href="{{ route('listing.details',$listing->slug) }}"> <img src="{{ getFile($listing->thumbnail_driver, $listing->thumbnail) }}" alt="image"></a>
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
                                    <a href="{{ route('listing.details',$listing->slug) }}">@lang(Str::limit($listing->title, 30))</a>
                                </h5>
                                <p>@lang('Category') : @lang(optional($listing)->getCategoriesName())</p>
                                <div class="mt-15">
                                    <p class=" mb-1 contact-item"><i class="fa-regular fa-location-dot"></i> @lang($listing->city_id != null ? $listing->get_cities?->getAddress() : $listing->address)</p>
                                    <p class="contact-item"><i class="fa-regular fa-phone"></i> @lang($listing->phone)</p>
                                </div>
                                <hr class="cmn-hr2">
                                <div class="bottom-info">
                                    <a href="{{ route('profile', optional($listing->get_user)->username) }}" class="category">
                                        <span class="icon">
                                            <i class="fa-regular fa-user"></i>
                                        </span>
                                        <div class="title">
                                            @lang(optional($listing->get_user)->firstname) @lang(optional($listing->get_user)->lastname)
                                        </div>
                                    </a>
                                    <p class="mb-0 contact-item"><i class="fa-regular fa-calendar-days"></i>
                                        {{ dateTime($listing->created_at) }}
                                    </p>
                                </div>

                            </div>
                        </div>
                    </div>
                @empty
                @endforelse
            </div>
            <div class="d-flex justify-content-center  mt-30">
                <a href="{{ route('listings') }}" class="cmn-btn">@lang('view more')</a>
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
                    $(`#${id}`).addClass("active");
                    Notiflix.Notify.success("Wishlist added");
                }
                if (data.stage == 'remove') {
                    $(`#${id}`).removeClass("active");
                    Notiflix.Notify.success("Wishlist removed");
                }
            },
        });
    }
</script>



