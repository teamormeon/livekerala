@extends('admin.layouts.app')
@section('page_title',__('Add Listing'))
@section('content')
    <div class="content container-fluid">
        <div class="page-header">
            <div class="row align-items-end">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title">@lang('Add Listing')</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb breadcrumb-no-gutter">
                            <li class="breadcrumb-item">
                                <a class="breadcrumb-link" href="{{ route('admin.dashboard') }}">
                                    @lang('Dashboard')
                                </a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">@lang('Listings')</li>
                            <li class="breadcrumb-item active" aria-current="page">@lang('Add New Listing')</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="card">
                <div class="">
                        <div class="pb-2">
                            <ul class="nav nav-tabs page-header-tabs">
                                <li class="nav-item" id="nav-profile">
                                    <button type="button" class="nav-link tab active" tab-id="tab1">@lang('Basic Info')</button>
                                </li>

                                <li class="nav-item" id="nav-profile">
                                    <button type="button" class="nav-link tab" tab-id="tab11">@lang('Features')</button>
                                </li>

                                @if($purchase_package_infos->is_video == 1)
                                    <li class="nav-item" id="nav-profile">
                                        <button type="button" class="nav-link tab" tab-id="tab2">@lang('Video')</button>
                                    </li>
                                @endif

                                @if($purchase_package_infos->number_of_images > 0 || $purchase_package_infos->number_of_images == null)
                                    <li class="nav-item" id="nav-profile">
                                        <button type="button" class="nav-link tab" tab-id="tab3">@lang('Photos')</button>
                                    </li>
                                @endif

                                @if($purchase_package_infos->number_of_amenities > 0 || $purchase_package_infos->number_of_amenities == null)
                                    <li class="nav-item" id="nav-profile">
                                        <button type="button" class="nav-link tab" tab-id="tab4">@lang('Amenities')</button>
                                    </li>
                                @endif

                                @if($purchase_package_infos->number_of_product > 0 || $purchase_package_infos->number_of_product == null)
                                    <li class="nav-item" id="nav-profile">
                                        <button class="nav-link tab" tab-id="tab5">@lang('Products')</button>
                                    </li>
                                @endif

                                @if($purchase_package_infos->is_seo == 1)
                                    <li class="nav-item" id="nav-profile">
                                        <button class="nav-link tab" tab-id="tab6">@lang('SEO')</button>
                                    </li>
                                @endif

                                @if($purchase_package_infos->communication_tools == 1)
                                    <li class="nav-item" id="nav-profile">
                                        <button class="nav-link tab" tab-id="tab7">@lang('Communication')</button>
                                    </li>
                                @endif
                            </ul>
                        </div>


                        <form class="mt-5" action="{{ route('admin.listing.store', $purchasePackageId) }}" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="userId" value="{{ $purchase_package_infos->user_id }}">
                            @csrf
                            @include('admin.listings.partials.addListing.basic_info')
                            @include('admin.listings.partials.addListing.features')

                            @if($purchase_package_infos->is_video == 1)
                                @include('admin.listings.partials.addListing.video')
                            @endif

                            @if($purchase_package_infos->number_of_images > 0 || $purchase_package_infos->number_of_images == null)
                                @include('admin.listings.partials.addListing.images')
                            @endif

                            @if($purchase_package_infos->number_of_amenities > 0 || $purchase_package_infos->number_of_amenities == null)
                                @include('admin.listings.partials.addListing.amenities')
                            @endif

                            @if($purchase_package_infos->number_of_product > 0 || $purchase_package_infos->number_of_product == null)
                                @include('admin.listings.partials.addListing.product')
                            @endif

                            @if($purchase_package_infos->is_seo == 1)
                                @include('admin.listings.partials.addListing.seo')
                            @endif

                            @if($purchase_package_infos->communication_tools == 1)
                                @include('admin.listings.partials.addListing.communication_tools')
                            @endif

                            <div class="col-12 mb-3 justify-content-strat d-flex mt-4 mb-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fal fa-check-circle" aria-hidden="true"></i>@lang('Submit changes')
                                </button>
                            </div>
                        </form>
                    </div>
            </div>
        </div>
        @endsection


        @push('css-lib')
            <link rel="stylesheet" href="{{ asset('assets/admin/css/summernote-bs5.min.css') }}">
            <link rel="stylesheet" href="{{ asset('assets/global/css/bootstrapicons-iconpicker.css') }}">
            <link rel="stylesheet" href="{{ asset('assets/global/css/tagsinput.css') }}">
            <link rel="stylesheet" href="{{ asset('assets/admin/css/bootstrap-icons.css') }}">
            <link rel="stylesheet" href="{{ asset('assets/admin/css/select2.min.css') }}">
        @endpush
        @push('js-lib')
            <script src="{{ asset('assets/admin/js/summernote-bs5.min.js')}}"></script>
            <script src="{{ asset('assets/global/js/tagsinput.js') }}"></script>
            <script src="{{ asset('assets/global/js/bootstrapicon-iconpicker.js') }}"></script>
            <script src="{{ asset('assets/admin/js/hs-file-attach.min.js') }}"></script>
            <script src="{{ asset('assets/admin/js/select2.min.js') }}"></script>
        @endpush

        @push('script')
            <script>

                // Showing clicked tab content div
                $(document).on('click', '.nav-link.tab', function() {
                    var tabId = $(this).attr('tab-id');
                    $('.add-listing-form').hide();
                    // Showing clicked tab content div
                    $('#' + tabId).show();
                    $('.nav-link.tab').removeClass('active');
                    $(this).addClass('active');
                });
                //Initially clicked the first tab
                $('.nav-link.tab[tab-id="tab1"]').click();


                $(document).ready(function (e) {
                    //for summernote
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
                });
            </script>
    @endpush




