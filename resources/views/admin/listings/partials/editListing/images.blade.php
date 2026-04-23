<div id="tab3" class="card add-listing-form">
    <div class="card-body">
        <div class="row g-4">
            <div class="col-xl-4 custom-margin">
                <h3 class="mb-3">@lang('Thumbnail')</h3>
                <div class="thumbnailImage">
                    <div class="form-group">
                        <input type="file" id="thumbnailImageUpload" class="form-control-file" name="thumbnail">
                        <span>@lang('Select Image')</span>
                    </div>
                    <img id="thumbnail" class="thumbnail" src="{{ getFile($single_listing_infos->thumbnail_driver, $single_listing_infos->thumbnail) }}" alt="Image Thumbnail">
                </div>
            </div>
            <div class="col-xl-8 custom-margin">
                <h3 class="mb-3">@lang('Images')</h3>
                <div class="listing-image no_of_listing_image"
                     data-listingimage="{{ $single_package_infos->is_image == 1 && $single_package_infos->no_of_img_per_listing == null  ? 'unlimited' : $single_package_infos->no_of_img_per_listing }}">
                </div>
                <span class="text-danger"> @error('listing_image.*') @lang($message) @enderror</span>
            </div>
        </div>
    </div>
</div>


@push('css-lib')
    <link rel="stylesheet" href="{{ asset('assets/global/css/image-uploader.css') }}">
@endpush
@push('js-lib')
    <script src="{{ asset('assets/global/js/image-uploader.js') }}"></script>
@endpush

@push('script')
    <script>
        $(document).ready(function () {

            //for thumbnail image upload
            $('#thumbnailImageUpload').on('change', function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(event) {
                        $('#thumbnail').attr('src', event.target.result);
                        $('#thumbnail').removeClass('d-none');
                    }
                    reader.readAsDataURL(file);
                }
            });

            //for multiple image upload
            let maximum_no_of_image_per_listing = $('.no_of_listing_image').data('listingimage');

            var listing_images = {!! json_encode($listing_images->toArray()) !!};
            let preloaded = [];
            listing_images.forEach(function (value, index) {
                preloaded.push({
                    id: value.id,
                    product_id: value.product_id,
                    image_name: value.listing_image,
                    src: value.src
                });
            });
            let listingImageOptions = {
                preloaded: preloaded,
                imagesInputName: 'listing_image',
                preloadedInputName: 'old_listing_image',
                label: 'Drag & Drop files here or click to browse images',
                extensions: ['.jpg', '.jpeg', '.png'],
                mimes: ['image/jpeg', 'image/png'],
                maxSize: 5242880
            };



            if (maximum_no_of_image_per_listing != 'unlimited') {
                listingImageOptions.maxFiles = maximum_no_of_image_per_listing;
            }
            $('.listing-image').imageUploader(listingImageOptions);


        })
    </script>
@endpush
