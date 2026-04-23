@extends('admin.layouts.app')
@section('page_title',__('Create Listing'))
@section('content')
    <div class="content container-fluid">
        <div class="page-header">
            <div class="row align-items-end">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title">@lang('Create Listing')</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb breadcrumb-no-gutter">
                            <li class="breadcrumb-item">
                                <a class="breadcrumb-link" href="{{ route('admin.dashboard') }}">
                                    @lang('Dashboard')
                                </a>
                            </li>
                            <li class="breadcrumb-item">
                                <a class="breadcrumb-link" href="{{ route('admin.listings') }}">
                                    @lang('Listings')
                                </a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">@lang('Create Listing')</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <form action="{{ route('admin.listing.create') }}" method="get" class="row g-3 align-items-end">
                    <div class="col-lg-5">
                        <label class="form-label">@lang('Listing Owner') <span class="text-danger">*</span></label>
                        <select class="js-select form-select @error('user_id') is-invalid @enderror" name="user_id" id="listingOwnerSelect"
                                data-hs-tom-select-options='{
                                  "placeholder": "Select owner",
                                  "hideSearch": false
                                }'>
                            <option value="">@lang('Select Owner')</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ (string) old('user_id', $selectedUserId) === (string) $user->id ? 'selected' : '' }}>
                                    {{ $user->firstname }} {{ $user->lastname }} ({{ $user->email }})
                                </option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback">
                            @error('user_id') {{ $message }} @enderror
                        </div>
                    </div>

                    <div class="col-lg-5">
                        <label class="form-label">@lang('Active Package') <span class="text-danger">*</span></label>
                        <select class="js-select form-select @error('purchase_package_id') is-invalid @enderror" name="purchase_package_id"
                                data-hs-tom-select-options='{
                                  "placeholder": "Select package",
                                  "hideSearch": false
                                }'>
                            <option value="">@lang('Select Package')</option>
                            @foreach($packages as $package)
                                <option value="{{ $package->id }}" {{ (string) old('purchase_package_id', $selectedPackageId) === (string) $package->id ? 'selected' : '' }}>
                                    {{ optional(optional($package->get_package)->details)->title ?? __('Package') }}
                                    @if($package->no_of_listing !== null)
                                        - {{ __('Remaining') }}: {{ $package->no_of_listing }}
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback">
                            @error('purchase_package_id') {{ $message }} @enderror
                        </div>
                    </div>

                    <div class="col-lg-2 d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi-eye me-1"></i>@lang('Load Form')
                        </button>
                    </div>
                </form>
            </div>
        </div>

        @if($single_package_infos)
            <div class="card">
                <ul class="nav nav-tabs page-header-tabs mb-0 flex-wrap">
                    <li class="nav-item">
                        <button type="button" class="nav-link tab active" tab-id="tab1">@lang('Basic Info')</button>
                    </li>

                    @if($single_package_infos->is_video == 1)
                        <li class="nav-item">
                            <button type="button" class="nav-link tab" tab-id="tab2">@lang('Video')</button>
                        </li>
                    @endif

                    <li class="nav-item">
                        <button type="button" class="nav-link tab" tab-id="tab3">@lang('Photos')</button>
                    </li>

                    @if($single_package_infos->is_amenities == 1)
                        <li class="nav-item">
                            <button type="button" class="nav-link tab" tab-id="tab4">@lang('Amenities')</button>
                        </li>
                    @endif

                    @if($single_package_infos->is_product == 1)
                        <li class="nav-item">
                            <button class="nav-link tab" tab-id="tab5">@lang('Products')</button>
                        </li>
                    @endif

                    @if($single_package_infos->seo == 1)
                        <li class="nav-item">
                            <button class="nav-link tab" tab-id="tab6">@lang('SEO')</button>
                        </li>
                    @endif

                    @if($single_package_infos->is_whatsapp == 1 || $single_package_infos->is_messenger == 1)
                        <li class="nav-item">
                            <button class="nav-link tab" tab-id="tab7">@lang('Communication')</button>
                        </li>
                    @endif
                </ul>

                <div class="card-body">
                    <form class="mt-5" action="{{ route('admin.listing.store') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="user_id" value="{{ old('user_id', $selectedUserId) }}">
                        <input type="hidden" name="purchase_package_id" value="{{ old('purchase_package_id', $selectedPackageId) }}">

                        <div class="card add-listing-form" id="tab0" style="display:block;">
                            <div class="card-body">
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <label class="form-label">@lang('Permalink')</label>
                                        <div class="input-group">
                                            <span class="input-group-text">{{ url('/listing') }}/</span>
                                            <input type="text" class="form-control set-slug @error('slug') is-invalid @enderror" name="slug"
                                                   value="{{ old('slug') }}" placeholder="@lang('Custom slug (optional)')">
                                            <div class="invalid-feedback">
                                                @error('slug') {{ $message }} @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">@lang('Selected Package')</label>
                                        <input type="text" class="form-control" value="{{ optional(optional($single_package_infos->get_package)->details)->title ?? __('Package') }}" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @include('admin.listings.partials.editListing.basic_info')

                        @if($single_package_infos->is_video == 1)
                            @include('admin.listings.partials.editListing.video')
                        @endif

                        @include('admin.listings.partials.editListing.images')

                        @if($single_package_infos->is_amenities == 1)
                            @include('admin.listings.partials.editListing.amenities')
                        @endif

                        @if($single_package_infos->is_product == 1)
                            @include('admin.listings.partials.editListing.product')
                        @endif

                        @if($single_package_infos->seo == 1)
                            @include('admin.listings.partials.editListing.seo')
                        @endif

                        @if($single_package_infos->is_whatsapp == 1 || $single_package_infos->is_messenger == 1)
                            @include('admin.listings.partials.editListing.communication_tools')
                        @endif

                        <div class="col-12 mb-3 justify-content-strat d-flex mt-4 mb-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fal fa-check-circle" aria-hidden="true"></i> @lang('Create Listing')
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    </div>
@endsection

@push('css-lib')
    <link rel="stylesheet" href="{{ asset('assets/global/css/esri-leaflet-geocoder.css') }}"/>
    <link rel="stylesheet" href="{{ asset('assets/global/css/leaflet-search.css') }}"/>
    <link rel="stylesheet" href="{{ asset('assets/global/css/leaflet.css') }}"/>
    <link rel="stylesheet" href="{{ asset('assets/global/css/Control.FullScreen.css') }}"/>
    <link rel="stylesheet" href="{{ asset('assets/global/css/leaflet-search-two.css') }}"/>
    <link rel="stylesheet" href="{{ asset('assets/global/css/tagsinput.css') }}"/>
    <link rel="stylesheet" href="{{ asset('assets/global/css/image-uploader.css') }}"/>
    <link rel="stylesheet" href="{{ asset('assets/global/css/select2.min.css') }}"/>
    <link rel="stylesheet" href="{{ asset('assets/admin/css/tom-select.bootstrap5.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/global/css/bootstrap-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/global/css/bootstrapicons-iconpicker.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/css/summernote-bs5.min.css') }}">
@endpush

@push('js-lib')
    <script src="{{ asset('assets/global/js/leaflet.js') }}"></script>
    <script src="{{ asset('assets/global/js/Control.FullScreen.js') }}"></script>
    <script src="{{ asset('assets/global/js/esri-leaflet.js') }}"></script>
    <script src="{{ asset('assets/global/js/leaflet-search.js') }}"></script>
    <script src="{{ asset('assets/global/js/esri-leaflet-geocoder.js') }}"></script>
    <script src="{{ asset('assets/global/js/bootstrap-geocoder.js') }}"></script>
    @if(basicControl()->is_google_map == 1)
        <script src="{{ asset('assets/global/js/google_map.js') }}"></script>
        <script src="https://maps.googleapis.com/maps/api/js?key={{ basicControl()->google_map_app_key }}&callback=initMap&libraries=places&v=weekly" defer></script>
    @else
        <script src="{{ asset('assets/global/js/map.js') }}"></script>
    @endif
    <script src="{{ asset('assets/admin/js/summernote-bs5.min.js')}}"></script>
    <script src="{{ asset('assets/global/js/select2.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/tom-select.complete.min.js') }}"></script>
    <script src="{{ asset('assets/global/js/tagsinput.js') }}"></script>
    <script src="{{ asset('assets/global/js/image-uploader.js') }}"></script>
    <script src="{{ asset('assets/global/js/bootstrapicon-iconpicker.js') }}"></script>
@endpush

@push('script')
    <script>
        $(document).on('click', '.nav-link.tab', function () {
            var tabId = $(this).attr('tab-id');
            $('.add-listing-form').hide();
            $('#' + tabId).show();
            $('.nav-link.tab').removeClass('active');
            $(this).addClass('active');
        });

        @if($single_package_infos)
        $('.nav-link.tab[tab-id="tab1"]').click();
        @endif

        $(document).ready(function () {
            HSCore.components.HSTomSelect.init('.js-select', {
                maxOptions: 250,
            });

            $('#listingOwnerSelect').on('change', function () {
                $(this).closest('form').submit();
            });

            $('#description').summernote({
                height: 120,
                callbacks: {
                    onBlurCodeview: function () {
                        let codeviewHtml = $(this).siblings('div.note-editor').find('.note-codable').val();
                        $(this).val(codeviewHtml);
                    }
                }
            });

            $('#body_text').summernote({
                height: 120,
                callbacks: {
                    onBlurCodeview: function () {
                        let codeviewHtml = $(this).siblings('div.note-editor').find('.note-codable').val();
                        $(this).val(codeviewHtml);
                    }
                }
            });

            $('.js-select').select2();
            $(document).on('input', '.change_name_input', function () {
                if ($('.set-slug').val().trim() === '') {
                    let inputValue = $(this).val();
                    let finalValue = inputValue.toLowerCase().replace(/\s+/g, '-');
                    $('.set-slug').val(finalValue);
                }
            });
        });
    </script>
@endpush
