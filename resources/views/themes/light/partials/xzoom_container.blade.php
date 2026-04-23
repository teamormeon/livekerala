<div>
    <div id="mainCarousel" class="carousel mx-auto main_carousel">
        @forelse($single_listing_details->get_listing_images as $listing_image)
            <div class="carousel__slide" data-src="{{ getFile($listing_image->driver, $listing_image->listing_image)}}" data-fancybox="gallery" data-caption="">
                <img class="img-fluid" src="{{ getFile($listing_image->driver, $listing_image->listing_image)}}"/>
            </div>
        @empty
            <div class="carousel__slide" data-src="{{ getFile($single_listing_details->thumbnail_driver, $single_listing_details->thumbnail)}}" data-fancybox="gallery" data-caption="">
                <img class="img-fluid" src="{{ getFile($single_listing_details->thumbnail_driver, $single_listing_details->thumbnail)}}"/>
            </div>
        @endforelse
    </div>

    <div id="thumbCarousel" class="carousel max-w-xl mx-auto mb-3 thumb_carousel">
        @forelse($single_listing_details->get_listing_images as $listing_image)
            <div class="carousel__slide">
                <img class="panzoom__content img-fluid" src="{{ getFile($listing_image->driver, $listing_image->listing_image)}}"/>
            </div>
        @empty
        @endforelse
    </div>
</div>

<div class="row align-items-center">
    @if(count($single_listing_details->get_social_info) > 0)
        <div class="col-8">
            <div id="">
                @foreach($single_listing_details->get_social_info as $social)
                    <a class="btn btn-light" href="{{ $social->social_url }}" target="_blank" title=""><i class="{{ $social->social_icon }} fa-2x" aria-hidden="true"></i></a>
                @endforeach
            </div>
        </div>
    @endif

    <div class="col-4">
        <div class="d-flex justify-content-end">

            <button class="share">
                <div id="shareBlock2"></div>
                <i class="fal fa-share-alt" aria-hidden="true"></i>
            </button>
            <button type="button" class="view-btn">
                <i class="far fa-eye"></i><span class="badge text-white">{{ $total_listing_view }}</span>
            </button>
        </div>
    </div>
</div>

<div class="info-box mb-5">
    <h4 class="title">@lang($single_listing_details->title)</h4>
    <p class="p-0">@lang('Category') : @lang($single_listing_details->getCategoriesName())</p>

    @if($single_listing_details->phone)
        <p class="address mb-2"> <i class="far fa-phone-alt me-1"></i><span> @lang($single_listing_details->phone)</span></p>
    @endif
    @if($single_listing_details->email)
        <p class="address mb-2"> <i class="far fa-envelope me-1"></i><span> @lang($single_listing_details->email)</span></p>
    @endif
    <p class="address">
        <i class="fas fa-map-marker-alt"></i>
        @lang($single_listing_details->get_cities?->getAddress() ?? $single_listing_details->address)
    </p>

</div>


@push('script')
    <script>
        $("#shareBlock2").socialSharingPlugin({
            urlShare: window.location.href,
            description: $("meta[name=description]").attr("content"),
            title: $("title").text(),
        });
    </script>
@endpush


