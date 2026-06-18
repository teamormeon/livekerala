@extends('admin.layouts.app')
@section('page_title',__('GDPR Cookie Setting'))
@section('content')
    <div class="content container-fluid">
        <div class="page-header">
            <div class="row align-items-end">
                <div class="col-sm mb-2 mb-sm-0">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb breadcrumb-no-gutter">
                            <li class="breadcrumb-item">
                                <a class="breadcrumb-link" href="javascript:void(0)">@lang('Dashboard')</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">@lang('Settings')</li>
                            <li class="breadcrumb-item active" aria-current="page">@lang('Logo Settings')</li>
                        </ol>
                    </nav>
                    <h1 class="page-header-title">@lang('Logo Settings')</h1>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-3">
                @include('admin.control_panel.components.sidebar', ['settings' => config('generalsettings.settings'), 'suffix' => 'Settings'])
            </div>

            <div class="col-lg-7">
                <div class="d-grid gap-3 gap-lg-5">
                    <div class="card">
                        <div class="card-header">
                            <h2 class="card-title h4">@lang('GDPR Cookie Settings')</h2>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.gdpr.cookie.update') }}" method="post" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-sm-12">
                                        <label for="TitleLabel" class="form-label">@lang('Cookie Title')</label>
                                        <input type="text"
                                               class="form-control  @error('cookie_title') is-invalid @enderror"
                                               name="cookie_title" id="TitleLabel"
                                               placeholder="@lang("Enter Cookie Title")" aria-label="@lang("Cookie Title")"
                                               autocomplete="off"
                                               value="{{ old('cookie_title', $basicControl->cookie_title) }}">
                                        @error('cookie_title')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-sm-12 mt-3">
                                        <label for="TitleLabelTwo" class="form-label">@lang('Cookie Sub Title')</label>
                                        <input type="text"
                                               class="form-control  @error('cookie_sub_title') is-invalid @enderror"
                                               name="cookie_sub_title" id="TitleLabelTwo"
                                               placeholder="@lang("Enter Cookie Sub Title")" aria-label="@lang("Cookie Sub Title")"
                                               autocomplete="off"
                                               value="{{ old('cookie_sub_title', $basicControl->cookie_sub_title) }}">
                                        @error('cookie_sub_title')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-sm-12 mt-3">
                                        <label for="TitleLabelFour" class="form-label">@lang('Cookie Description')</label>
                                        <textarea class="summernote form-control @error('cookie_description') is-invalid @enderror"
                                                  name="cookie_description" placeholder="@lang("Enter Cookie Description")"
                                                  aria-label="@lang("'Cookie Description")">{{ old('cookie_description', $basicControl->cookie_description) }}</textarea>
                                        @error('cookie_description')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 mt-3">
                                        <label class="form-label" for="cImage">@lang(stringToTitle('Cookie Image'))</label>
                                        <label class="form-check form-check-dashed" for="logoUploader" id="content_img">
                                            <img id="contentImg"
                                                 class="avatar avatar-xl avatar-4x3 avatar-centered h-100 mb-2"
                                                 src="{{ getFile($basicControl->cookie_image_driver, $basicControl->cookie_image) }}"
                                                 alt="Image Description" data-hs-theme-appearance="default">
                                            <img id="contentImg"
                                                 class="avatar avatar-xl avatar-4x3 avatar-centered h-100 mb-2"
                                                 src="{{ getFile($basicControl->cookie_image_driver, $basicControl->cookie_image) }}"
                                                 alt="Image Description" data-hs-theme-appearance="dark">
                                            <span class="d-block">@lang("Browse your file here")</span>
                                            <input type="file" name="cookie_image" class="js-file-attach form-check-input @error('cookie_image') is-invalid @enderror"
                                                   id="logoUploader" data-hs-file-attach-options='{
                                                                      "textTarget": "#contentImg",
                                                                      "mode": "image",
                                                                      "targetAttr": "src",
                                                                      "allowTypes": [".png", ".jpeg", ".jpg", ".svg"]
                                                                   }'
                                            />
                                            @error('cookie_image')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </label>
                                    </div>
                                    <div class="col-md-8 mt-4">
                                        <div class="row align-items-center category_item_create">
                                            <div class="col-sm mb-2 mb-sm-0">
                                                <h5 class="mb-0">@lang('Status')</h5>
                                                <p class="fs-5 text-body mb-0">@lang('Check or unchecked for active or inactive cookie')</p>
                                            </div>
                                            <div class="col-sm-auto d-flex align-items-center">
                                                <div class="form-check form-switch form-switch-google">
                                                    <input type="hidden" name="cookie_status" value="0">
                                                    <input class="form-check-input" name="cookie_status"
                                                           type="checkbox" id="Status" value="1" {{ $basicControl->cookie_status == 1 ? 'checked' : '' }}>
                                                    <label class="form-check-label"
                                                           for="Status"></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @if(adminAccessRoute(config('role.control_panel.access.edit')))
                                    <div class="d-flex justify-content-start mt-lg-5">
                                        <button type="submit" class="btn btn-primary">@lang("Save changes")</button>
                                    </div>
                                @endif
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Content -->
@endsection

@push('css-lib')
    <link rel="stylesheet" href="{{ asset('assets/admin/css/summernote-bs5.min.css') }}">
@endpush
@push('js-lib')
    <script src="{{ asset("assets/admin/js/hs-file-attach.min.js") }}"></script>
    <script src="{{ asset('assets/admin/js/summernote-bs5.min.js') }}"></script>
@endpush

@push('script')
    <script>
        'use strict';
            $(document).ready(() => new HSFileAttach('.js-file-attach'));

            $(document).ready(function (){
                $('.summernote').summernote({
                    height: 200,
                    callbacks: {
                        onBlurCodeview: function () {
                            let codeviewHtml = $(this).siblings('div.note-editor').find('.note-codable').val();
                            $(this).val(codeviewHtml);
                        }
                    }
                });
            })
    </script>
@endpush


