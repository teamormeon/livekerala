@extends('admin.layouts.app')
@section('page_title',__('Listing Category Edit'))
@section('content')
    <div class="content container-fluid">
        <div class="page-header">
            <div class="row align-items-end">
                <div class="col-sm mb-2 mb-sm-0">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb breadcrumb-no-gutter">
                            <li class="breadcrumb-item"><a class="breadcrumb-link"
                                                           href="javascript:void(0);">@lang('Dashboard')</a></li>
                            <li class="breadcrumb-item active" aria-current="page">@lang('Manage Listing')</li>
                            <li class="breadcrumb-item active" aria-current="page">@lang('Category')</li>
                        </ol>
                    </nav>
                    <h1 class="page-header-title">@lang('Edit Category')</h1>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header card-header-content-md-between">
                <h4>@lang('Create Form')</h4>
            </div>
            <div class="card-body">
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    @foreach($languages as $key => $language)
                        <li class="nav-item">
                            <a class="nav-link {{ $loop->first ? 'active' : '' }}" data-bs-toggle="tab" href="#lang-tab-{{ $key }}" role="tab" aria-controls="lang-tab-{{ $key }}"
                               aria-selected="{{ $loop->first ? 'true' : 'false' }}">@lang($language->name)</a>
                        </li>
                    @endforeach
                </ul>

                <div class="tab-content mt-2" id="myTabContent">
                    @foreach($languages as $key => $language)

                        <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="lang-tab-{{ $key }}" role="tabpanel">
                            <form method="post" action="{{ route('admin.listing.category.update',[$id,$language->id]) }}" class="mt-4" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="row">
                                    <div class="col-sm-12 col-md-4 col-lg-4 mb-3">
                                        <div class="form-group">
                                            <label class="form-label" for="name"> @lang('Category Name') </label>
                                            <input type="text" name="name[{{ $language->id }}]"
                                                   class="form-control  @error('name'.'.'.$language->id) is-invalid @enderror"
                                                   value="<?php echo old('name'.$language->id, isset($listingCategoryDetails[$language->id]) ? $listingCategoryDetails[$language->id][0]->name : '') ?>">
                                            <div class="invalid-feedback">
                                                @error('name'.'.'.$language->id) @lang($message) @enderror
                                            </div>
                                            <div class="valid-feedback"></div>
                                        </div>
                                    </div>
                                    @if ($loop->index == 0)
                                        <div class="col-lg-4 col-sm-12 col-md-4 mb-3">
                                            <div class="form-group">
                                                <label class="form-label" for="name"> @lang('Icon') </label>
                                                <div class="input-group">
                                                    <input type="text" name="icon"
                                                           value="<?php echo old('icon'.$language->id, isset($listingCategoryDetails[$language->id]) ? optional($listingCategoryDetails[$language->id][0]->category)->icon : '') ?>"
                                                           class="form-control demo__icon__picker iconpicker1 @error('icon') is-invalid @enderror"
                                                           placeholder="Pick a icon" aria-label="Pick a icon"
                                                           aria-describedby="basic-addon1" readonly>
                                                    <div class="invalid-feedback">
                                                        @error('icon') @lang($message) @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-4 col-md-4 col-sm-12 col-12">
                                            <div class="form-group ">
                                                <label class="form-label">@lang('Status')</label>
                                                <div class="list-group-item">
                                                    <div class="d-flex">
                                                        <div class="flex-grow-1">
                                                            <div class="row align-items-center">
                                                                <div class="col">
                                                                <span class="d-block fs-6 text-body">
                                                                    @lang('If you want to make active or deactive then switch on or off')
                                                                </span>
                                                                </div>
                                                                <div class="col-auto">
                                                                    <label class="row form-check form-switch mb-3" for="status">
                                                                <span class="col-4 col-sm-3 text-end">
                                                                    <input type='hidden' value='0' name='status'>
                                                                    <input
                                                                        class="form-check-input @error('status') is-invalid @enderror"
                                                                        type="checkbox"
                                                                        name="status"
                                                                        id="status"
                                                                        value="1" {{ optional(@$listingCategoryDetails[$language->id][0]->category)->status == 1 ? 'checked' : '' }}>
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

                                        <div class="col-sm-6 col-md-4 col-lg-4 col-6">
                                            <label class="form-label" for="image">@lang(stringToTitle('Mobile App Image'))</label>
                                            <label class="form-check form-check-dashed" for="logoUploader" id="content_img">
                                                <img id="contentImg"
                                                     class="avatar avatar-xl avatar-4x3 avatar-centered h-100 mb-2"
                                                     src="{{ @$listingCategoryDetails[$language->id][0]->category->mobile_app_image ? getFile(optional($listingCategoryDetails[$language->id][0]->category)->image_driver,optional($listingCategoryDetails[$language->id][0]->category)->mobile_app_image) : asset("assets/admin/img/oc-browse-file.svg") }}"
                                                     alt="Image Description" data-hs-theme-appearance="default">
                                                <img id="contentImg"
                                                     class="avatar avatar-xl avatar-4x3 avatar-centered h-100 mb-2"
                                                     src="{{ @$listingCategoryDetails[$language->id][0]->category->mobile_app_image ? getFile(optional($listingCategoryDetails[$language->id][0]->category)->image_driver,optional($listingCategoryDetails[$language->id][0]->category)->mobile_app_image) : asset("assets/admin/img/oc-browse-file-light.svg") }}"
                                                     alt="Image Description" data-hs-theme-appearance="dark">
                                                <span class="d-block">@lang("Browse your file here")</span>
                                                <input type="hidden" name="test" value="0">
                                                <input type="file" name="mobile_app_image" class="js-file-attach form-check-input @error('mobile_app_image') is-invalid @enderror"
                                                       id="logoUploader" data-hs-file-attach-options='{
                                                                      "textTarget": "#contentImg",
                                                                      "mode": "image",
                                                                      "targetAttr": "src",
                                                                      "allowTypes": [".png", ".jpeg", ".jpg"]
                                                                   }'
                                                />
                                                @error('mobile_app_image')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </label>
                                        </div>

                                    @endif
                                </div>

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
    <link rel="stylesheet" href="{{ asset('assets/global/css/bootstrapicons-iconpicker.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/global/css/select2.min.css') }}">
@endpush


@push('js-lib')
    <script src="{{ asset('assets/global/js/bootstrapicon-iconpicker.js') }}"></script>
    <script src="{{ asset('assets/global/js/select2.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/hs-file-attach.min.js') }}"></script>
@endpush


@push('script')
    <script>
        $(document).on('ready', function () {
            new HSFileAttach('.js-file-attach')
            $('.js-select').select2();
            setIconpicker('.iconpicker1');
        });


        function setIconpicker(selector = '.iconpicker1') {
            $(selector).iconpicker({
                title: 'Search Social Icons',
                selected: false,
                defaultValue: false,
                placement: "top",
                collision: "none",
                animation: true,
                hideOnSelect: true,
                showFooter: false,
                searchInFooter: false,
                mustAccept: false,
                selectedCustomClass: "bg-primary",
                fullClassFormatter: function (e) {
                    return e;
                },
                input: "input,.iconpicker-input",
                inputSearch: false,
                container: false,
                component: ".input-group-addon,.iconpicker-component",
            })
        }

    </script>
@endpush




