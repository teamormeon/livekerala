<div id="tab5" class="card add-listing-form">
    <div class="card-body">
        <div class="row g-4 new_product_form">
            <div class="d-flex justify-content-start align-items-center">
                <h3 class="me-3">@lang('Product')</h3>
                <button class="btn btn-outline-primary add-new-product" type="button" id="add_products"
                        data-products="{{ $single_package_infos->no_of_product == null ? 'unlimited' : $single_package_infos->no_of_product - $listing_products->count() }}">
                    <i class="fal fa-plus"></i> @lang('Add More') (<span class="product_count">
                        @if($single_package_infos->no_of_product == null)
                            @lang('unlimited')
                        @else
                            {{ $single_package_infos->no_of_product - $listing_products->count() }}
                        @endif </span>)
                </button>
            </div>

            @php
                $productCounts = $listing_products->count()
            @endphp

            @if($productCounts == 0)
                <div class="col-xl-6 col-md-6 col-sm-12" data-index="1">
                    <div class="form new__product__form">
                        <div class="row g-2">
                            <div class="input-box col-md-6">
                                <input class="form-control @error('product_title.0') is-invalid @enderror"
                                       type="text" name="product_title[]" placeholder="@lang('Title')"
                                       value="{{ old('product_title.0') }}"/>
                                @error('product_title.0')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="input-box col-md-6">
                                <input class="form-control @error('product_price.0') is-invalid @enderror"
                                       type="number" step="0.1" name="product_price[]"
                                       placeholder="@lang('Price')" value="{{ old('product_price.0') }}"/>
                                @error('product_price.0')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="input-box col-12 bg-white">
                            <textarea class="form-control @error('product_description.0') is-invalid @enderror" cols="30" rows="8"
                                      name="product_description[]"
                                      placeholder="@lang('Description')">{{ old('product_description.0') }}</textarea>
                                @error('product_description.0')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="custom-margin">
                                <div class="thumbnailImage">
                                    <div class="form-group">
                                        <input type="file" id="productThumbnailImageUpload0" class="form-control-file" name="product_thumbnail[]">
                                        <span>@lang('thumbnail')</span>
                                        <input type="hidden" name="old_product_thumbnail[]" value="{{ old('product_thumbnail.0') }}">
                                    </div>
                                    <img id="productThumbnail0" class="thumbnail d-none" src="" alt="Image Thumbnail">
                                </div>
                                @error('product_thumbnail.0')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="custom-margin">
                                <div class="product-image no_of_product_image" id="product-image1"
                                     data-productimage="{{ $single_package_infos->number_of_img_per_product == null ? 500 : $single_package_infos->number_of_img_per_product }}">
                                </div>
                                <span class="text-danger"> @error('product_image.1.*') @lang($message) @enderror</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if($productCounts > 0)
                @for($i = 0; $i < $productCounts; $i++)
                    <div class="col-xl-6 removeProductForm">
                        <div class="form new__product__form">
                            <span class="product-form-close delete_desc"> <i class="fa fa-times"></i> </span>
                            <div class="row g-2">
                                <input type="hidden" name="product_id[]"
                                       value="{{ old("product_id.$i", $listing_products[$i]->id ?? '') }}">

                                <div class="input-box col-md-6">
                                    <input class="form-control" type="text" name="product_title[]" placeholder="@lang('Title')"
                                           value="{{ old("product_title.$i", $listing_products[$i]->product_title ?? '') }}"/>
                                </div>

                                <div class="input-box col-md-6">
                                    <input class="form-control" type="number" step="0.1" name="product_price[]"
                                           placeholder="@lang('Price')" value="{{ old("product_price.$i", $listing_products[$i]->product_price ?? '') }}"/>
                                </div>

                                <div class="input-box col-12 bg-white">
                                    <textarea class="form-control" cols="30" rows="8" name="product_description[]"
                                              placeholder="@lang('Description')">{{ old("product_description.$i", $listing_products[$i]->product_description ?? '') }}</textarea>
                                </div>

                                <div class="custom-margin">
                                    <div class="thumbnailImage">
                                        <div class="form-group">
                                            <input type="file"
                                                   id="productThumbnailImageUpload{{ $i + 1 }}"
                                                   class="form-control-file"
                                                   name="product_thumbnail[{{ $i }}]">
                                            <span>@lang('thumbnail')</span>
                                            <input type="hidden" name="old_product_thumbnail[]" value="{{ $listing_products[$i]->product_thumbnail ?? '' }}">
                                        </div>
                                        <img id="productThumbnail{{ $i + 1 }}" class="thumbnail" src="{{ getFile($listing_products[$i]->driver, $listing_products[$i]->product_thumbnail ?? '') }}" alt="">
                                    </div>
                                </div>

                                <div class="custom-margin">
                                    <div class="product-image no_of_product_image"
                                         id="product-image{{ $i + 1 }}"
                                         data-productid="{{ $listing_products[$i]->id ?? '' }}"
                                         data-productimage="{{ $single_package_infos->is_product == 1 && $single_package_infos->no_of_img_per_product == null  ? 'unlimited' : $single_package_infos->no_of_img_per_product }}"
                                         data-singleproductimage="{{ $listing_products[$i]->get_product_image->map(function ($img){
                                                $img->src = getFile($img->driver, $img->product_image);
                                                return $img;
                                            }) ?? '' }}">
                                    </div>
                                    <span class="text-danger @error('product_image.'.($i + 1).'.*') @lang($message) @enderror"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endfor
            @endif
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
            initProductImages();

            $('#add_products').on('click', function () {
                addNewProductForm();
            });

            $(document).on('click', '.product-form-close', function () {
                removeProductForm(this);
            });

            $('input[type="file"]').each(function (index, element) {
                if (element.id.startsWith('productThumbnailImageUpload')) {
                    handleThumbnailUpload(element.id);
                }
            });
        });

        function initProductImages() {
            $('.product-image').each(function (index) {
                let productPreloaded = [];
                let productId = $(this).data('productid');
                let preloadedImages = $(this).data('singleproductimage');

                if (preloadedImages && preloadedImages.length) {
                    preloadedImages.forEach(function (value) {
                        productPreloaded.push({
                            id: value.id,
                            src: value.src
                        });
                    });
                }

                let productImageOptions = {
                    preloaded: productPreloaded,
                    imagesInputName: `product_image[${productId}]`,
                    preloadedInputName: `old_product_image[${productId}]`,
                    label: 'Drag & Drop files here or click to browse images',
                    extensions: ['.jpg', '.jpeg', '.png'],
                    mimes: ['image/jpeg', 'image/png'],
                    maxSize: 5242880
                };

                let maxImages = $(this).data('productimage');
                if (maxImages !== 'unlimited') {
                    productImageOptions.maxFiles = maxImages;
                }

                $(this).imageUploader(productImageOptions);
            });
        }

        function addNewProductForm() {
            let maximum_no_of_image_per_product = $('.no_of_product_image').data('productimage');
            let productLength = $('.new__product__form').length + 1;
            let dataProducts = $('#add_products').data('products');
            let maxProductId = Math.max(...$('.product-image').map(function() { return $(this).data('productid'); }).get(), 1);
            if (dataProducts >= 1 || dataProducts == 'unlimited') {
                let productForm = `<div class="col-xl-6 removeProductForm" data-index="${productLength}">
                                        <div class="form new__product__form">
                                            <span class="product-form-close"> <i class="fa fa-times"></i> </span>
                                            <div class="row g-2">
                                                <div class="input-box col-md-6">
                                                    <input class="form-control" type="text" name="product_title[]" placeholder="@lang('Title')"/>
                                                </div>
                                                <div class="input-box col-md-6">
                                                    <input class="form-control" type="number" step="0.1" name="product_price[]" placeholder="@lang('Price')"/>
                                                </div>
                                                <div class="input-box col-12 bg-white">
                                                    <textarea class="form-control" cols="30" rows="8" name="product_description[]" placeholder="@lang('Description')"></textarea>
                                                </div>
                                                <div class="custom-margin">
                                                    <div class="thumbnailImage">
                                                        <div class="form-group">
                                                            <input type="file" id="productThumbnailImageUpload${maxProductId + 1}" class="form-control-file" name="product_thumbnail[]">
                                                            <span>@lang('thumbnail')</span>
                                                            <input type="hidden" name="old_product_thumbnail[]" value="">
                                                        </div>
                                                        <img id="productThumbnail${maxProductId + 1}" class="thumbnail d-none" src="" alt="Image Thumbnail">
                                                    </div>
                                                </div>
                                                <div class="custom-margin">
                                                    <div class="product-image no_of_product_image" id="product-image${maxProductId + 1}" data-productid="${maxProductId + 1}" data-productimage="${maximum_no_of_image_per_product}">
                                                    </div>
                                                    <span class="text-danger"> @error('product_image.*') @lang($message) @enderror</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>`;


                $('.new_product_form').append(productForm);
                if (dataProducts != 'unlimited') {
                    let newDataProducts = dataProducts - 1;
                    $('#add_products').data('products', newDataProducts);
                    $('.product_count').text(newDataProducts);
                }

                handleThumbnailUpload(`productThumbnailImageUpload${maxProductId + 1}`);

                let productImageOptions = {
                    imagesInputName: `product_image[${maxProductId + 1}]`,
                    label: 'Drag & Drop files here or click to browse images',
                    extensions: ['.jpg', '.jpeg', '.png'],
                    mimes: ['image/jpeg', 'image/png'],
                    maxSize: 5242880
                };

                let maxImages = $('.no_of_product_image').data('productimage');
                if (maxImages !== 'unlimited') {
                    productImageOptions.maxFiles = maxImages;
                }
                $(`#product-image${maxProductId + 1}`).imageUploader(productImageOptions);

            } else {
                Notiflix.Notify.warning('No more products can be added');
            }
        }

        function removeProductForm(element) {
            $(element).parents('.removeProductForm').remove();

            let dataProducts = $('#add_products').data('products');
            if (dataProducts != 'unlimited') {
                let addNewDataProducts = dataProducts + 1;
                $('#add_products').data('products', addNewDataProducts);
                $('.product_count').text(addNewDataProducts);
            }
        }

        function handleThumbnailUpload(id) {
            $(`#${id}`).on('change', function () {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function (event) {
                        $(`#${id.replace('ImageUpload', '')}`).attr('src', event.target.result).removeClass('d-none');
                    }
                    reader.readAsDataURL(file);
                }
            });
        }
    </script>
@endpush




