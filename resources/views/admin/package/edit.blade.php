@extends('admin.layouts.app')
@section('page_title',__('Edit Package'))
@section('content')
    <div class="content container-fluid">
        <div class="page-header">
            <div class="row align-items-end">
                <div class="col-sm mb-2 mb-sm-0">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb breadcrumb-no-gutter">
                            <li class="breadcrumb-item"><a class="breadcrumb-link"
                                                           href="javascript:void(0);">@lang('Dashboard')</a></li>
                            <li class="breadcrumb-item" aria-current="page">@lang('Manage Package')</li>
                            <li class="breadcrumb-item active" aria-current="page">@lang('Package List')</li>
                        </ol>
                    </nav>
                    <h1 class="page-header-title">@lang('Edit Package')</h1>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    @foreach($languages as $key => $language)
                        <li class="nav-item">
                            <a class="nav-link {{ $loop->first ? 'active' : '' }}" data-bs-toggle="tab"
                               href="#lang-tab-{{ $key }}" role="tab" aria-controls="lang-tab-{{ $key }}"
                               aria-selected="{{ $loop->first ? 'true' : 'false' }}">@lang($language->name)</a>
                        </li>
                    @endforeach
                </ul>

                <div class="tab-content mt-2" id="myTabContent">
                    @foreach($languages as $key => $language)
                        <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="lang-tab-{{ $key }}"
                             role="tabpanel">
                            <form method="post" action="{{ route('admin.package.update',[$id, $language->id]) }}" class="mt-4" enctype="multipart/form-data">
                                @csrf
                                @method('put')
                                <div class="row">
                                    <div class="col-lg-4 col-md-4 col-sm-12 col-12 mb-3 package_title">
                                        <label class="form-label" for="name"> @lang('Package title') </label>
                                        <input type="text" name="title[{{ $language->id }}]"
                                               class="form-control  @error('title'.'.'.$language->id) is-invalid @enderror"
                                               value="{{ old('title'.'.'.$language->id, isset($packageDetails[$language->id]) ? $packageDetails[$language->id][0]->title : '') }}" placeholder="@lang('package title')">
                                        <div class="invalid-feedback">
                                            @error('title'.'.'.$language->id) @lang($message) @enderror
                                        </div>
                                        <div class="valid-feedback"></div>
                                    </div>

                                    @if ($loop->index == 0)
                                        <div class="col-lg-4 col-md-4 form-group col-sm-12 col-12 package_price_div">
                                            <label class="form-label"> {{trans('Price')}}</label>
                                            <div class="input-group mb-3">
                                                <input type="text" name="price" value="{{ old('price', isset($packageDetails[$language->id]) ? optional($packageDetails[$language->id][0]->package)->price : '') }}"
                                                       class="form-control  @error('price') is-invalid @enderror package_price"
                                                       aria-describedby="basic-addon2" id="package_price" placeholder="@lang('price')">
                                                <div class="input-group-append">
                                                <span class="input-group-text package_price_symbol border-right-1"
                                                      id="basic-addon2"> {{ basicControl()->currency_symbol ?? '$' }} </span>
                                                    <span class="input-group-text" id="basic-addon2"> <span
                                                            class="me-2">@lang('Free')</span>
                                                <input type="checkbox" name="is_free" value="-1"
                                                       {{ old('is_free') == -1 || isset($packageDetails[$language->id]) ?? optional($packageDetails[$language->id][0]->package)->price  == null ? 'checked' : '' }} id="free_package"
                                                       class="free_package"></span>
                                                </div>
                                                <div class="invalid-feedback">
                                                    @error('price') @lang($message) @enderror
                                                </div>
                                                <div class="valid-feedback"></div>
                                            </div>
                                        </div>

                                        <div class="form-group col-lg-3 col-md-3 col-sm-12 col-12 d-none is_multiple_time_purchase">
                                            <label class="form-label"> {{trans('Multiple Time Purchase')}}</label>
                                            <div class="list-group-item">
                                                <div class="d-flex">
                                                    <div class="flex-grow-1">
                                                        <div class="row align-items-center">
                                                            <div class="col">
                                                                <span class="d-block fs-6 text-body">
                                                                    @lang('Allow to purchase a package multiple time.')
                                                                </span>
                                                            </div>
                                                            <div class="col-auto">
                                                                <label class="row form-check form-switch mb-3" for="is_multiple_time_purchase">
                                                                    <span class="col-4 col-sm-3 text-end">
                                                                        <input type='hidden' value='0' name='is_multiple_time_purchase'>
                                                                        <input class="form-check-input @error('is_multiple_time_purchase') is-invalid @enderror"
                                                                               type="checkbox" name="is_multiple_time_purchase" id="is_multiple_time_purchase" value="1"
                                                                               {{ old('is_multiple_time_purchase', isset($packageDetails[$language->id]) ?? optional($packageDetails[$language->id][0]->package)->is_multiple_time_purchase) == "1" ? 'checked' : '' }}>
                                                                    </span>
                                                                    @error('is_multiple_time_purchase')
                                                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                                                    @enderror
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group col-lg-4 col-md-4 col-sm-12 col-12 mb-3 package_expiry">
                                            <label class="form-label"> {{trans('Package Expiry')}} </label>
                                            <div class="input-group">
                                                <input type="text" name="expiry_time" class="form-control expiry_time @error('expiry_time') is-invalid @enderror"
                                                       value="{{ old('expiry_time', isset($packageDetails[$language->id]) ?? optional($packageDetails[$language->id][0]->package)->expiry_time) }}"
                                                    {{ old('expiry_time', isset($packageDetails[$language->id]) ?? optional($packageDetails[$language->id][0]->package)->expiry_time) == null ? 'disabled' : '' }}>
                                                <div class="input-group-append">
                                                    <select class="form-control expiry_time_type" id="expiry_time_type" name="expiry_time_type"
                                                        {{ old('expiry_time_type', isset($packageDetails[$language->id]) ?? optional($packageDetails[$language->id][0]->package)->expiry_time) == null ? 'disabled' : '' }}>
                                                        <option value="Days"
                                                            {{ isset($packageDetails[$language->id]) ?? $packageDetails[$language->id][0]->package->expiry_time_type == 'Days' || isset($packageDetails[$language->id]) ?? optional($packageDetails[$language->id][0]->package)->expiry_time_type == 'Day' ? 'selected' : '' }}>@lang('Day(s)')</option>
                                                        <option value="Months" {{ isset($packageDetails[$language->id]) ?? optional($packageDetails[$language->id][0]->package)->expiry_time_type == 'Months' || isset($packageDetails[$language->id]) ?? optional($packageDetails[$language->id][0]->package)->expiry_time_type == 'Month' ? 'selected' : '' }}>@lang('Month(s)')</option>
                                                        <option value="Years" {{ isset($packageDetails[$language->id]) ?? optional($packageDetails[$language->id][0]->package)->expiry_time_type == 'Year' || isset($packageDetails[$language->id]) ?? optional($packageDetails[$language->id][0]->package)->expiry_time_type == 'Years' ? 'selected' : '' }}>@lang('Year(s)')</option>
                                                    </select>
                                                    <span class="input-group-text" id="basic-addon2"> <span class="me-2">@lang('Unlimited')</span> <input type="checkbox" name="expiry_time_unlimited" value="-1" id="expiry_time_unlimited" class="expiry_time_unlimited" {{ old('expiry_time_unlimited') == -1 || isset($packageDetails[$language->id]) ??optional($packageDetails[$language->id][0]->package)->expiry_time == null ? 'checked' : '' }}></span>
                                                </div>

                                                <div class="invalid-feedback">
                                                    @error('expiry_time') @lang($message) @enderror
                                                </div>
                                                <div class="valid-feedback"></div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                @if ($loop->index == 0)
                                    <div class="row mb-3 g-3">
                                        <div class="form-group col-lg-3 col-md-3 col-sm-12 col-12">
                                            <label class="form-label"> {{trans('No. of listing')}} </label>
                                            <div class="input-group mb-3">
                                                <input type="number" min="1" name="no_of_listing" placeholder="Enter listing number" class="no_of_listing form-control @error('no_of_listing') is-invali @enderror"
                                                       aria-describedby="basic-addon2" value="{{ old('no_of_listing', isset($packageDetails[$language->id]) ?? optional($packageDetails[$language->id][0]->package)->no_of_listing) }}"
                                                    {{ old('no_of_listing', isset($packageDetails[$language->id]) ?? optional($packageDetails[$language->id][0]->package)->no_of_listing) == null ? 'disabled' : '' }}>

                                                <div class="input-group-append">
                                                    <span class="input-group-text" id="basic-addon2"> <span class="me-2">@lang('Unlimited')</span> <input type="checkbox" name="no_of_listing_unlimited" value="-1" id="listing_unlimited" class="listing_unlimited" {{ old('listing_unlimited') == -1 || isset($packageDetails[$language->id]) ?? optional($packageDetails[$language->id][0]->package)->no_of_listing  == null ? 'checked' : '' }}></span>
                                                </div>

                                                <div class="invalid-feedback">
                                                    @error('no_of_listing') @lang($message) @enderror
                                                </div>
                                                <div class="valid-feedback"></div>
                                            </div>
                                        </div>

                                        <div class="form-group col-lg-3 col-md-3 col-12">
                                            <label class="form-label"> {{trans('No. of categories per listing')}} </label>
                                            <div class="input-group mb-3">
                                                <input type="number" name="no_of_categories_per_listing" placeholder="Enter category number per listing"
                                                       value="{{ old('no_of_categories_per_listing', isset($packageDetails[$language->id]) ?? optional($packageDetails[$language->id][0]->package)->no_of_categories_per_listing) }}" min="1"
                                                       class="form-control no_of_categories_per_listing @error('no_of_categories_per_listing') is-invalid @enderror"
                                                       aria-describedby="basic-addon2">
                                                <div class="invalid-feedback">
                                                    @error('no_of_categories_per_listing') @lang($message) @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group col-lg-3 col-md-3 col-sm-6">
                                            <label class="form-label">@lang('Image')</label>
                                            <div class="list-group-item">
                                                <div class="d-flex">
                                                    <div class="flex-grow-1">
                                                        <div class="row align-items-center">
                                                            <div class="col">
                                                                <span class="d-block fs-6 text-body">
                                                                    @lang('Allow to add image')
                                                                </span>
                                                            </div>
                                                            <div class="col-auto">
                                                                <label class="row form-check form-switch mb-3" for="is_image">
                                                                    <span class="col-4 col-sm-3 text-end">
                                                                        <input type='hidden' value='0' name='is_image'>
                                                                        <input class="form-check-input @error('is_image') is-invalid @enderror"
                                                                               type="checkbox" name="is_image" id="is_image" value="1" {{ old('is_image') || isset($packageDetails[$language->id]) ?? optional($packageDetails[$language->id][0]->package)->is_image  == "1" ? 'checked' : '' }}>
                                                                    </span>
                                                                    @error('is_image')
                                                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                                                    @enderror
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group col-lg-3 col-md-3 col-sm-12 col-12">
                                            <label class="form-label"> {{trans('No. of images per listing')}} </label>
                                            <div class="input-group mb-3">
                                                <input type="number" name="no_of_img_per_listing" min="1" placeholder="Enter image number per listing"
                                                       value="{{ old('no_of_img_per_listing', isset($packageDetails[$language->id]) ?? optional($packageDetails[$language->id][0]->package)->no_of_img_per_listing) }}"
                                                       class="form-control no_of_img_per_listing @error('no_of_img_per_listing') is-invalid @enderror"
                                                       aria-describedby="basic-addon2" {{ old('is_image', isset($packageDetails[$language->id]) ?? optional($packageDetails[$language->id][0]->package)->is_image) == 0 || old('listing_img_unlimited') == -1 || isset($packageDetails[$language->id]) ?? optional($packageDetails[$language->id][0]->package)->no_of_img_per_listing == null ? 'disabled' : '' }} >

                                                <div class="input-group-append">
                                                    <span class="input-group-text" id="basic-addon2"> <span class="me-2">@lang('Unlimited')</span> <input type="checkbox" name="no_of_img_per_listing_unlimited" value="-1" id="listing_img_unlimited" class="listing_img_unlimited" {{ old('is_image', isset($packageDetails[$language->id]) ?? optional($packageDetails[$language->id][0]->package)->is_image) == 1 && (old('listing_img_unlimited') == -1 || isset($packageDetails[$language->id]) ?? optional($packageDetails[$language->id][0]->package)->no_of_img_per_listing == null) ? 'checked' : ''  }}></span>
                                                </div>
                                                <div class="invalid-feedback">
                                                    @error('no_of_img_per_listing') @lang($message) @enderror
                                                </div>
                                                <div class="valid-feedback"></div>
                                            </div>
                                        </div>

                                        <div class="form-group col-lg-3 col-md-3 col-sm-6">
                                            <label class="form-label">@lang('Video')</label>
                                            <div class="list-group-item">
                                                <div class="d-flex">
                                                    <div class="flex-grow-1">
                                                        <div class="row align-items-center">
                                                            <div class="col">
                                                                <span class="d-block fs-6 text-body">
                                                                    @lang('Allow to add video')
                                                                </span>
                                                            </div>
                                                            <div class="col-auto">
                                                                <label class="row form-check form-switch mb-3" for="is_video">
                                                                    <span class="col-4 col-sm-3 text-end">
                                                                        <input type='hidden' value='0' name='is_video'>
                                                                        <input class="form-check-input @error('is_video') is-invalid @enderror"
                                                                               type="checkbox" name="is_video" id="is_video" value="1"
                                                                            {{ old('is_video') || isset($packageDetails[$language->id]) ?? optional($packageDetails[$language->id][0]->package)->is_video  == "1" ? 'checked' : '' }}>
                                                                    </span>
                                                                    @error('is_video')
                                                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                                                    @enderror
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group col-lg-3 col-md-3 col-sm-6">
                                            <label class="form-label">@lang('Amenities')</label>
                                            <div class="list-group-item">
                                                <div class="d-flex">
                                                    <div class="flex-grow-1">
                                                        <div class="row align-items-center">
                                                            <div class="col">
                                                                <span class="d-block fs-6 text-body">
                                                                    @lang('Allow to add amenities')
                                                                </span>
                                                            </div>
                                                            <div class="col-auto">
                                                                <label class="row form-check form-switch mb-3" for="is_amenities">
                                                                    <span class="col-4 col-sm-3 text-end">
                                                                        <input type='hidden' value='0' name='is_amenities'>
                                                                        <input class="form-check-input @error('is_amenities') is-invalid @enderror"
                                                                               type="checkbox" name="is_amenities" id="is_amenities" value="1"
                                                                            {{ old('is_amenities') || isset($packageDetails[$language->id]) ?? optional($packageDetails[$language->id][0]->package)->is_amenities  == "1" ? 'checked' : '' }}>
                                                                    </span>
                                                                    @error('is_amenities')
                                                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                                                    @enderror
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group col-lg-3 col-md-3 col-12">
                                            <label class="form-label"> {{trans('No. of amenities per listing')}} </label>
                                            <div class="input-group mb-3">
                                                <input type="number" name="no_of_amenities_per_listing" placeholder="Enter amenity number per listing"
                                                       value="{{ old('no_of_amenities_per_listing', isset($packageDetails[$language->id]) ?? optional($packageDetails[$language->id][0]->package)->no_of_amenities_per_listing) }}" min="1" class="form-control no_of_amenities_per_listing @error('no_of_amenities_per_listing') is-invalid @enderror" aria-describedby="basic-addon2" {{ old('is_amenities', isset($packageDetails[$language->id]) ?? optional($packageDetails[$language->id][0]->package)->is_amenities) == 0 || old('amenities_unlimited') == -1 || isset($packageDetails[$language->id]) ?? optional($packageDetails[$language->id][0]->package)->no_of_amenities_per_listing == null ? 'disabled' : '' }}>

                                                <div class="input-group-append">
                                                    <span class="input-group-text" id="basic-addon2"> <span class="me-2">@lang('Unlimited')</span> <input type="checkbox" name="no_of_amenities_per_listing_unlimited" value="-1" id="amenities_unlimited" class="amenities_unlimited" {{ old('is_amenities', isset($packageDetails[$language->id]) ?? optional($packageDetails[$language->id][0]->package)->is_amenities) == 1 && (old('amenities_unlimited') == -1 || isset($packageDetails[$language->id]) ?? optional($packageDetails[$language->id][0]->package)->no_of_amenities_per_listing == null) ? 'checked' : '' }}></span>
                                                </div>
                                                <div class="invalid-feedback">
                                                    @error('no_of_amenities_per_listing') @lang($message) @enderror
                                                </div>
                                                <div class="valid-feedback"></div>
                                            </div>
                                        </div>


                                        <div class="form-group col-lg-3 col-md-3 col-sm-6">
                                            <label class="form-label">@lang('Product')</label>
                                            <div class="list-group-item">
                                                <div class="d-flex">
                                                    <div class="flex-grow-1">
                                                        <div class="row align-items-center">
                                                            <div class="col">
                                                                <span class="d-block fs-6 text-body">
                                                                    @lang('Allow to add product')
                                                                </span>
                                                            </div>
                                                            <div class="col-auto">
                                                                <label class="row form-check form-switch mb-3" for="is_product">
                                                                    <span class="col-4 col-sm-3 text-end">
                                                                        <input type='hidden' value='0' name='is_product'>
                                                                        <input class="form-check-input @error('is_product') is-invalid @enderror"
                                                                               type="checkbox" name="is_product" id="is_product" value="1"
                                                                            {{ old('is_product') || isset($packageDetails[$language->id]) ?? optional($packageDetails[$language->id][0]->package)->is_product  == "1" ? 'checked' : '' }}>
                                                                    </span>
                                                                    @error('is_product')
                                                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                                                    @enderror
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group col-lg-3 col-md-3 col-sm-12 col-12">
                                            <label class="form-label"> {{trans('No. of product')}} </label>
                                            <div class="input-group mb-3">
                                                <input type="number" name="no_of_product" min="1" placeholder="Enter product number"
                                                       class="no_of_product form-control @error('no_of_product') is-invalid @enderror" aria-describedby="basic-addon2"
                                                       value="{{ old('no_of_product', isset($packageDetails[$language->id]) ?? optional($packageDetails[$language->id][0]->package)->no_of_product) }}"
                                                    {{ old('is_product', isset($packageDetails[$language->id]) ?? optional($packageDetails[$language->id][0]->package)->is_product) == 0 || old('product_unlimited') == -1 || isset($packageDetails[$language->id]) ?? optional($packageDetails[$language->id][0]->package)->no_of_product == null ? 'disabled' : '' }}>

                                                <div class="input-group-append">
                                                    <span class="input-group-text" id="basic-addon2"> <span class="me-2">@lang('Unlimited')</span>
                                                        <input type="checkbox" name="no_of_product_unlimited" value="-1" id="product_unlimited" class="product_unlimited" {{ old('is_product', isset($packageDetails[$language->id]) ?? optional($packageDetails[$language->id][0]->package)->is_product) == 1 && (old('product_unlimited') == -1 || isset($packageDetails[$language->id]) ?? optional($packageDetails[$language->id][0]->package)->no_of_product == null) ? 'checked' : '' }}></span>
                                                </div>
                                                <div class="invalid-feedback">
                                                    @error('no_of_product') @lang($message) @enderror
                                                </div>

                                                <div class="valid-feedback"></div>
                                            </div>
                                        </div>

                                        <div class="form-group col-lg-3 col-md-3 col-sm-12 col-12">
                                            <label class="form-label"> {{trans('No. of images per product')}} </label>
                                            <div class="input-group mb-3">
                                                <input type="number" name="no_of_img_per_product" placeholder="Enter image number per product"
                                                       value="{{ old('no_of_img_per_product', isset($packageDetails[$language->id]) ?? optional($packageDetails[$language->id][0]->package)->no_of_img_per_product) }}" min="1" class="form-control no_of_img_per_product @error('no_of_img_per_product') is-invalid @enderror" aria-describedby="basic-addon2" {{ old('is_product', isset($packageDetails[$language->id]) ?? optional($packageDetails[$language->id][0]->package)->is_product) == 0 || old('product_img_unlimited') == -1 || isset($packageDetails[$language->id]) ?? optional($packageDetails[$language->id][0]->package)->no_of_img_per_product == null ? 'disabled' : '' }}>

                                                <div class="input-group-append">
                                                    <span class="input-group-text" id="basic-addon2"> <span class="me-2">@lang('Unlimited')</span> <input type="checkbox" name="no_of_img_per_product_unlimited" value="-1" id="product_img_unlimited" class="product_img_unlimited" {{ old('is_product', isset($packageDetails[$language->id]) ?? optional($packageDetails[$language->id][0]->package)->is_product) == 1 && (old('product_img_unlimited') == -1 || isset($packageDetails[$language->id]) ?? optional($packageDetails[$language->id][0]->package)->no_of_img_per_product == null) ? 'checked' : '' }}></span>
                                                </div>
                                                <div class="invalid-feedback">
                                                    @error('no_of_img_per_product') @lang($message) @enderror
                                                </div>
                                                <div class="valid-feedback"></div>
                                            </div>
                                        </div>

                                        <div class="col-lg-3 col-md-3 col-sm-12 col-12">
                                            <div class="form-group ">
                                                <label class="form-label">@lang('Create Form')</label>
                                                <div class="list-group-item">
                                                    <div class="d-flex">
                                                        <div class="flex-grow-1">
                                                            <div class="row align-items-center">
                                                                <div class="col">
                                                                <span class="d-block fs-6 text-body">
                                                                    @lang('Allow to create dynamic form.')
                                                                </span>
                                                                </div>
                                                                <div class="col-auto">
                                                                    <label class="row form-check form-switch mb-3" for="is_create_from">
                                                                    <span class="col-4 col-sm-3 text-end">
                                                                        <input type='hidden' value='0' name='is_create_from'>
                                                                        <input class="form-check-input @error('is_create_from') is-invalid @enderror"
                                                                               type="checkbox" name="is_create_from" id="is_create_from" value="1"
                                                                                {{ old('is_create_from') || isset($packageDetails[$language->id]) ?? optional($packageDetails[$language->id][0]->package)->is_create_from  == "1" ? 'checked' : '' }}>
                                                                    </span>
                                                                        @error('is_create_from')
                                                                        <span class="invalid-feedback d-block">{{ $message }}</span>
                                                                        @enderror
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="form-group col-lg-3 col-md-3 col-sm-6">
                                            <label class="form-label">@lang('Business Hour')</label>
                                            <div class="list-group-item">
                                                <div class="d-flex">
                                                    <div class="flex-grow-1">
                                                        <div class="row align-items-center">
                                                            <div class="col">
                                                                <span class="d-block fs-6 text-body">
                                                                    @lang('Allow to add business hour')
                                                                </span>
                                                            </div>
                                                            <div class="col-auto">
                                                                <label class="row form-check form-switch mb-3" for="is_business_hour">
                                                                    <span class="col-4 col-sm-3 text-end">
                                                                        <input type='hidden' value='0' name='is_business_hour'>
                                                                        <input class="form-check-input @error('is_business_hour') is-invalid @enderror"
                                                                               type="checkbox" name="is_business_hour" id="is_business_hour" value="1"
                                                                            {{ old('is_business_hour') || isset($packageDetails[$language->id]) ?? optional($packageDetails[$language->id][0]->package)->is_business_hour  == "1" ? 'checked' : '' }}>
                                                                    </span>
                                                                    @error('is_business_hour')
                                                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                                                    @enderror
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group col-lg-3 col-md-3 col-sm-6">
                                            <label class="form-label">@lang('SEO')</label>
                                            <div class="list-group-item">
                                                <div class="d-flex">
                                                    <div class="flex-grow-1">
                                                        <div class="row align-items-center">
                                                            <div class="col">
                                                                <span class="d-block fs-6 text-body">
                                                                    @lang('Allow to SEO')
                                                                </span>
                                                            </div>
                                                            <div class="col-auto">
                                                                <label class="row form-check form-switch mb-3" for="seo">
                                                                    <span class="col-4 col-sm-3 text-end">
                                                                        <input type='hidden' value='0' name='seo'>
                                                                        <input class="form-check-input @error('seo') is-invalid @enderror"
                                                                               type="checkbox" name="seo" id="seo" value="1"
                                                                            {{ old('seo') || isset($packageDetails[$language->id]) ?? optional($packageDetails[$language->id][0]->package)->seo  == "1" ? 'checked' : '' }}>
                                                                    </span>
                                                                    @error('seo')
                                                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                                                    @enderror
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group col-lg-3 col-md-3 col-sm-6">
                                            <label class="form-label">@lang('Messenger Chat SDK')</label>
                                            <div class="list-group-item">
                                                <div class="d-flex">
                                                    <div class="flex-grow-1">
                                                        <div class="row align-items-center">
                                                            <div class="col">
                                                                <span class="d-block fs-6 text-body">
                                                                    @lang('Allow to messenger chat')
                                                                </span>
                                                            </div>
                                                            <div class="col-auto">
                                                                <label class="row form-check form-switch mb-3" for="is_messenger">
                                                                    <span class="col-4 col-sm-3 text-end">
                                                                        <input type='hidden' value='0' name='is_messenger'>
                                                                        <input class="form-check-input @error('is_messenger') is-invalid @enderror"
                                                                               type="checkbox" name="is_messenger" id="is_messenger" value="1"
                                                                            {{ old('is_messenger') || isset($packageDetails[$language->id]) ?? optional($packageDetails[$language->id][0]->package)->is_messenger  == "1" ? 'checked' : '' }}>
                                                                    </span>
                                                                    @error('is_messenger')
                                                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                                                    @enderror
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group col-lg-3 col-md-3 col-sm-6">
                                            <label class="form-label">@lang('Whatsapp Chat SDK')</label>
                                            <div class="list-group-item">
                                                <div class="d-flex">
                                                    <div class="flex-grow-1">
                                                        <div class="row align-items-center">
                                                            <div class="col">
                                                                <span class="d-block fs-6 text-body">
                                                                    @lang('Allow to whatsapp chat')
                                                                </span>
                                                            </div>
                                                            <div class="col-auto">
                                                                <label class="row form-check form-switch mb-3" for="is_whatsapp">
                                                                    <span class="col-4 col-sm-3 text-end">
                                                                        <input type='hidden' value='0' name='is_whatsapp'>
                                                                        <input class="form-check-input @error('is_whatsapp') is-invalid @enderror"
                                                                               type="checkbox" name="is_whatsapp" id="is_whatsapp" value="1"
                                                                            {{ old('is_whatsapp') || isset($packageDetails[$language->id]) ?? optional($packageDetails[$language->id][0]->package)->is_whatsapp  == "1" ? 'checked' : '' }}>
                                                                    </span>
                                                                    @error('is_whatsapp')
                                                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                                                    @enderror
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group col-lg-3 col-md-3 col-sm-12 col-12">
                                            <label class="form-label"> {{trans('Renew Package')}}</label>
                                            <div class="list-group-item">
                                                <div class="d-flex">
                                                    <div class="flex-grow-1">
                                                        <div class="row align-items-center">
                                                            <div class="col">
                                                                <span class="d-block fs-6 text-body">
                                                                    @lang('Allow to renew package.')
                                                                </span>
                                                            </div>
                                                            <div class="col-auto">
                                                                <label class="row form-check form-switch mb-3" for="is_renew">
                                                                    <span class="col-4 col-sm-3 text-end">
                                                                        <input type='hidden' value='0' name='is_renew'>
                                                                        <input class="form-check-input @error('is_renew') is-invalid @enderror"
                                                                               type="checkbox" name="is_renew" id="is_renew" value="1"
                                                                            {{ old('is_renew') || isset($packageDetails[$language->id]) ?? optional($packageDetails[$language->id][0]->package)->is_renew  == "1" ? 'checked' : '' }}>
                                                                    </span>
                                                                    @error('is_renew')
                                                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                                                    @enderror
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-3 col-md-3 col-sm-12 col-12">
                                            <div class="form-group ">
                                                <label class="form-label">@lang('Status')</label>
                                                <div class="list-group-item">
                                                    <div class="d-flex">
                                                        <div class="flex-grow-1">
                                                            <div class="row align-items-center">
                                                                <div class="col">
                                                                <span class="d-block fs-6 text-body">
                                                                    @lang('Allow to active package.')
                                                                </span>
                                                                </div>
                                                                <div class="col-auto">
                                                                    <label class="row form-check form-switch mb-3" for="status">
                                                                    <span class="col-4 col-sm-3 text-end">
                                                                        <input type='hidden' value='0' name='status'>
                                                                        <input class="form-check-input @error('status') is-invalid @enderror"
                                                                               type="checkbox" name="status" id="status" value="1"
                                                                            {{ old('status') || isset($packageDetails[$language->id]) ?? optional($packageDetails[$language->id][0]->package)->status  == "1" ? 'checked' : '' }}>
                                                                    </span>
                                                                        @error('status')
                                                                        <span class="invalid-feedback d-block">{{ $message }}</span>
                                                                        @enderror
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        @if($gateways)
                                            @foreach($gateways as $gateway)
                                                <div class="col-lg-3 col-md-3 col-sm-12 col-12">
                                                    <label class="form-label" for="SubscriptionLabel">{{$gateway->name}} @lang('Subscription Plan Id')</label>
                                                    <input type="text" class="form-control"
                                                           name="gateway_plan_id[{{$gateway->code}}][]"
                                                           value="{{json_decode(isset($packageDetails[$language->id]) ?? $packageDetails[$language->id][0]->package->gateway_plan_id)->{$gateway->code} ?? null}}"
                                                           id="plan_name"
                                                           aria-label="@lang('plan id')"
                                                           autocomplete="off">
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-6 col-md-4 col-lg-4 col-6">
                                            <label class="form-label" for="image">@lang(stringToTitle('Image'))</label>
                                            <label class="form-check form-check-dashed" for="logoUploader" id="content_img">
                                                <img id="contentImg"
                                                     class="avatar avatar-xl avatar-4x3 avatar-centered h-100 mb-2"
                                                     src="{{ getFile(optional(@$packageDetails[$language->id][0]->package)->driver, optional(@$packageDetails[$language->id][0]->package)->image) }}"
                                                     alt="Image Description" data-hs-theme-appearance="default">
                                                <img id="contentImg"
                                                     class="avatar avatar-xl avatar-4x3 avatar-centered h-100 mb-2"
                                                     src="{{ getFile(optional(@$packageDetails[$language->id][0]->package)->driver, optional(@$packageDetails[$language->id][0]->package)->image) }}"
                                                     alt="Image Description" data-hs-theme-appearance="dark">
                                                <span class="d-block">@lang("Browse your file here")</span>
                                                <input type="hidden" name="test" value="0">
                                                <input type="file" name="image" class="js-file-attach form-check-input @error('image') is-invalid @enderror"
                                                       id="logoUploader" data-hs-file-attach-options='{
                                                                      "textTarget": "#contentImg",
                                                                      "mode": "image",
                                                                      "targetAttr": "src",
                                                                      "allowTypes": [".png", ".jpeg", ".jpg"]
                                                                   }'
                                                />
                                                @error('image')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </label>
                                        </div>
                                    </div>
                                @endif
                                <button type="submit" class="btn waves-effect waves-light btn-rounded btn-primary btn-block mt-3 w-100">@lang('Save')</button>
                            </form>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>



@endsection


@push('css-lib')
    <link rel="stylesheet" href="{{ asset('assets/admin/css/tom-select.bootstrap5.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/css/summernote-bs5.min.css')}}">
@endpush


@push('js-lib')
    <script src="{{ asset('assets/admin/js/tom-select.complete.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/hs-file-attach.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/summernote-bs5.min.js')}}"></script>
@endpush


@push('script')
    <script>
        $(document).on('ready', function () {
            $(document).ready(() => new HSFileAttach('.js-file-attach'));
            HSCore.components.HSTomSelect.init('.js-select', {
                maxOptions: 250,
            })

            $(document).on('click', '.expiry_time_unlimited', function (){
                if ($('.expiry_time_unlimited').is(':checked')){
                    $('.is_renew').attr('disabled', 'disabled');
                    $('.is_renew').prop('checked', true);
                }
                else {
                    $('.is_renew').removeAttr('disabled');
                    $('.is_renew').prop('checked', false);
                }
            })

            if ($('.free_package').is(':checked')){
                $('.is_multiple_time_purchase').removeClass('d-none');
                $('.package_title').removeClass('col-lg-4 col-md-4');
                $('.package_title').addClass('col-lg-3 col-md-3');
                $('.package_price_div').removeClass('col-lg-4 col-md-4');
                $('.package_price_div').addClass('col-lg-3 col-md-3');
                $('.package_expiry').removeClass('col-lg-4 col-md-4');
                $('.package_expiry').addClass('col-lg-3 col-md-3');
            }
            else {
                $('.is_multiple_time_purchase').addClass('d-none');
                $('.is_multiple_time_purchase').addClass('d-none');
                $('.package_title').addClass('col-lg-4 col-md-4');
                $('.package_title').removeClass('col-lg-3 col-md-3');
                $('.package_price_div').addClass('col-lg-4 col-md-4');
                $('.package_price_div').removeClass('col-lg-3 col-md-3');
                $('.package_expiry').addClass('col-lg-4 col-md-4');
                $('.package_expiry').removeClass('col-lg-3 col-md-3');
            }

            $(document).on('click', '.free_package', function (){
                if ($('.free_package').is(':checked')){
                    $('.package_price_symbol').text('');
                    $('.is_multiple_time_purchase').removeClass('d-none');
                    $('.package_title').removeClass('col-lg-4 col-md-4');
                    $('.package_title').addClass('col-lg-3 col-md-3');
                    $('.package_price_div').removeClass('col-lg-4 col-md-4');
                    $('.package_price_div').addClass('col-lg-3 col-md-3');
                    $('.package_expiry').removeClass('col-lg-4 col-md-4');
                    $('.package_expiry').addClass('col-lg-3 col-md-3');
                }
                else {
                    $('.package_price_symbol').text('$');
                    $('.is_multiple_time_purchase').addClass('d-none');
                    $('.package_title').addClass('col-lg-4 col-md-4');
                    $('.package_title').removeClass('col-lg-3 col-md-3');
                    $('.package_price_div').addClass('col-lg-4 col-md-4');
                    $('.package_price_div').removeClass('col-lg-3 col-md-3');
                    $('.package_expiry').addClass('col-lg-4 col-md-4');
                    $('.package_expiry').removeClass('col-lg-3 col-md-3');
                }
            })


            $('#image').change("on",function () {
                let reader = new FileReader();
                reader.onload = (e) => {
                    $('#image_preview_container').attr('src', e.target.result);
                }
                reader.readAsDataURL(this.files[0]);
            });

            $('.summernote').summernote({
                height: 250,
                callbacks: {
                    onBlurCodeview: function() {
                        let codeviewHtml = $(this).siblings('div.note-editor').find('.note-codable').val();
                        $(this).val(codeviewHtml);
                    }
                }
            });

        });

        let selectors = {
            'input[name="expiry_time_unlimited"]': {
                disabled: ['.expiry_time', '.expiry_time_type'],
                blank: ['.expiry_time'],
                except: ''
            },
            'input[name="is_product"]': {
                disabled: ['.no_of_product', '.product_unlimited', '.no_of_img_per_product', '.product_img_unlimited'],
                blank: ['.no_of_product', '.no_of_img_per_product'],
                except: ''
            },
            'input[name="is_image"]': {
                disabled: ['.no_of_img_per_listing', '.listing_img_unlimited'],
                blank: ['.no_of_img_per_listing'],
                except: ''
            },
            'input[name="is_amenities"]': {
                disabled: ['.no_of_amenities_per_listing', '.amenities_unlimited'],
                blank: ['.no_of_amenities_per_listing'],
                except: ''
            },
            'input[name="no_of_listing_unlimited"]': {
                disabled: ['.no_of_listing'],
                blank: ['.no_of_listing'],
                except: ''
            },
            'input[name="is_free"]': {
                disabled: ['.package_price'],
                blank: ['.package_price'],
                except: ''
            },
            'input[name="no_of_img_per_listing_unlimited"]': {
                disabled: ['.no_of_img_per_listing'],
                blank: ['.no_of_img_per_listing'],
                except: ''
            },
            'input[name="no_of_img_per_product_unlimited"]': {
                disabled: ['.no_of_img_per_product'],
                blank: ['.no_of_img_per_product'],
                except: ''
            },
            'input[name="no_of_amenities_per_listing_unlimited"]': {
                disabled: ['.no_of_amenities_per_listing'],
                blank: ['.no_of_amenities_per_listing'],
                except: ''
            },
            'input[name="no_of_product_unlimited"]': {
                disabled: ['.no_of_product'],
                blank: ['.no_of_product'],
                except: ''
            }
        }

        for (let selector in selectors) {
            let currentSelector = selectors[selector];
            setEnableDisable(currentSelector, $(selector));

            $(document).on('click', selector, function () {
                setEnableDisable(currentSelector, this);
            });
        }

        function setEnableDisable(selectors, parentSelector) {
            let disable = false;

            if ($(parentSelector).val() == 1 && $(parentSelector).is(":checked")) {
                disable = false;
            }
            else if ($(parentSelector).val() == 1 && $(parentSelector).is(":not(:checked)")) {
                disable = true;
            }
            else if ($(parentSelector).val() == -1 && $(parentSelector).is(":checked")) {
                disable = true;
            }
            else if ($(parentSelector).val() == -1 && $(parentSelector).is(":not(:checked)")) {
                disable = false;
            }

            let disabledSelector = selectors.disabled.toString();
            let blankSelector = selectors.blank.toString();

            if (disable) {
                $(disabledSelector).attr('disabled', 'disabled');
                $(blankSelector).val('');
            } else if (!selectors.except || !$(selectors.except).is(":checked")) {
                $(disabledSelector).removeAttr('disabled');
            }
        }


    </script>
@endpush




