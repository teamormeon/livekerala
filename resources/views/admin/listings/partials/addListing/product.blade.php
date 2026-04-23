<div id="tab5" class="card add-listing-form">
    <div class="card-body">
        <div class="row g-4 new_product_form">
            <div class="d-flex justify-content-start">
                <h3 class="me-3">@lang('Product')</h3>
                <button class="btn btn-outline-primary add-new-product" type="button" id="add_products"
                        data-products="{{ $purchase_package_infos->number_of_product == null ? 'unlimited' : $purchase_package_infos->number_of_product - 1 }}">
                    <i class="fal fa-plus"></i> @lang('Add More') (<span class="product_count">
                        @if($purchase_package_infos->number_of_product == null)
                            @lang('unlimited')
                        @else
                            {{ $purchase_package_infos->number_of_product - 1 }}
                        @endif </span>)
                </button>
            </div>

            <!-- Existing Product Forms (First one) -->
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
                            <textarea class="form-control @error('product_description.0') is-invalid @enderror" cols="30" rows="3"
                                      name="product_description[]"
                                      placeholder="@lang('Description')">{{ old('product_description.0') }}</textarea>
                            @error('product_description.0')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="custom-margin">
                            <div class="thumbnailImage">
                                <div class="form-group">
                                    <input type="file" id="productThumbnailImageUpload1" class="form-control-file" name="product_thumbnail[]">
                                    <span>@lang('thumbnail')</span>
                                </div>
                                <img id="productThumbnail1" class="thumbnail d-none" src="" alt="Image Thumbnail">
                            </div>
                            @error('product_thumbnail.0')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="custom-margin">
                            <div class="product-image no_of_product_image" id="product-image1"
                                 data-productimage="{{ $purchase_package_infos->number_of_img_per_product == null ? 500 : $purchase_package_infos->number_of_img_per_product }}">
                            </div>
                            <span class="text-danger"> @error('product_image.1.*') @lang($message) @enderror</span>
                        </div>
                    </div>
                </div>
            </div>

            @php
                $productCounts = old('product_title') ? count(old('product_title')) : 0;
            @endphp
            @if($productCounts > 1)
                @for($i = 1; $i < $productCounts; $i++)
                    <div class="col-xl-6 removeProductForm" data-index="{{ $i + 1 }}">
                        <div class="form new__product__form">
                            <span class="product-form-close"> <i class="fa fa-times"></i> </span>
                            <div class="row g-2">
                                <div class="input-box col-md-6">
                                    <input class="form-control @error('product_title.'.$i) is-invalid @enderror"
                                           type="text" name="product_title[]" placeholder="@lang('Title')"
                                           value="{{ old('product_title.'.$i) }}"/>
                                    @error('product_title.'.$i)
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="input-box col-md-6">
                                    <input class="form-control @error('product_price.'.$i) is-invalid @enderror"
                                           type="number" step="0.1" name="product_price[]"
                                           placeholder="@lang('Price')" value="{{ old('product_price.'.$i) }}"/>
                                    @error('product_price.'.$i)
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="input-box col-12 bg-white">
                                    <textarea class="form-control @error('product_description.'.$i) is-invalid @enderror" cols="30" rows="3"
                                              name="product_description[]"
                                              placeholder="@lang('Description')">{{ old('product_description.'.$i) }}</textarea>
                                    @error('product_description.'.$i)
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="custom-margin">
                                    <div class="thumbnailImage">
                                        <div class="form-group">
                                            <input type="file" id="productThumbnailImageUpload{{ $i + 1 }}" class="form-control-file" name="thumbnail">
                                            <span>@lang('thumbnail')</span>
                                        </div>
                                        <img id="productThumbnail{{ $i + 1 }}" class="thumbnail d-none" src="" alt="Image Thumbnail">
                                    </div>
                                </div>

                                <div class="custom-margin">
                                    <div class="product-image no_of_product_image" id="product-image{{ $i + 1 }}"
                                         data-productimage="{{ $purchase_package_infos->number_of_img_per_product == null ? 500 : $purchase_package_infos->number_of_img_per_product }}">
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

            function handleThumbnailUpload(index) {
                $('#productThumbnailImageUpload' + index).on('change', function () {
                    const file = this.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function (event) {
                            $('#productThumbnail' + index).attr('src', event.target.result).removeClass('d-none');
                        }
                        reader.readAsDataURL(file);
                    }
                });
            }

            let maximum_no_of_image_per_product = $('.no_of_product_image').data('productimage');
            let productImageOptions = {
                imagesInputName: 'product_image[1]',
                label: 'Drag & Drop files here or click to browse images',
                extensions: ['.jpg', '.jpeg', '.png'],
                mimes: ['image/jpeg', 'image/png'],
                maxSize: 5242880
            };

            if (maximum_no_of_image_per_product != 'unlimited') {
                productImageOptions.maxFiles = maximum_no_of_image_per_product;
            }

            let totaloldProducts = $('.product-image').length;
            for (let i = 1; i <= totaloldProducts; i++) {
                handleThumbnailUpload(i);
                $(`#product-image${i}`).imageUploader(productImageOptions);
            }

            $(document).on('click', '#add_products', function () {
                let productLength = $('.new__product__form').length + 1;
                let dataProducts = $('#add_products').data('products');

                if (dataProducts >= 1 || dataProducts == 'unlimited') {
                    var productForm = `<div class="col-xl-6 removeProductForm" data-index="${productLength}">
                                            <div class="form new__product__form">
                                                <span class="product-form-close"> <i class="fa fa-times"></i> </span>
                                                <div class="row g-2">
                                                    <div class="input-box col-md-6">
                                                        <input class="form-control" type="text" name="product_title[]" placeholder="@lang('Title')" />
                                                    </div>

                                                    <div class="input-box col-md-6">
                                                        <input class="form-control" type="number" step="0.1" name="product_price[]" placeholder="@lang('Price')" />
                                                    </div>

                                                    <div class="input-box col-12 bg-white">
                                                        <textarea class="form-control" cols="30" rows="3" name="product_description[]" placeholder="@lang('Description')"></textarea>
                                                    </div>

                                                    <div class="custom-margin">
                                                        <div class="thumbnailImage">
                                                            <div class="form-group">
                                                                <input type="file" id="productThumbnailImageUpload${productLength}" class="form-control-file" name="product_thumbnail[]">
                                                                <span>@lang('thumbnail')</span>
                                                            </div>
                                                            <img id="productThumbnail${productLength}" class="thumbnail d-none" src="" alt="Image Thumbnail">
                                                        </div>
                                                    </div>

                                                    <div class="custom-margin">
                                                        <div class="product-image no_of_product_image" id="product-image${productLength}" data-productimage="${maximum_no_of_image_per_product}">
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

                    handleThumbnailUpload(productLength);

                    let productImageOptions = {
                        imagesInputName: `product_image[${productLength}]`,
                        label: 'Drag & Drop files here or click to browse images',
                        extensions: ['.jpg', '.jpeg', '.png'],
                        mimes: ['image/jpeg', 'image/png'],
                        maxSize: 5242880
                    };

                    if (maximum_no_of_image_per_product != 'unlimited') {
                        productImageOptions.maxFiles = maximum_no_of_image_per_product;
                    }
                    $(`#product-image${productLength}`).imageUploader(productImageOptions);

                } else {
                    Notiflix.Notify.warning("No more add products");
                }
            });

            $(document).on('click', '.product-form-close', function () {
                $(this).parents('.removeProductForm').remove();

                let dataProducts = $('#add_products').data('products');
                if (dataProducts != 'unlimited') {
                    let addNewDataProducts = dataProducts + 1;
                    $('#add_products').data('products', addNewDataProducts);
                    $('.product_count').text(addNewDataProducts);
                }
            });
        });

    </script>
@endpush
