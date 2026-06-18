@extends('admin.layouts.app')
@section('page_title',__('Map Setting'))
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
                            <li class="breadcrumb-item active" aria-current="page">@lang('Control Panel')</li>
                            <li class="breadcrumb-item active" aria-current="page">@lang('Map Settings')</li>
                        </ol>
                    </nav>
                    <h1 class="page-header-title">@lang('Map Settings')</h1>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-3">
                @include('admin.control_panel.components.sidebar', ['settings' => config('generalsettings.settings'), 'suffix' => 'Settings'])
            </div>

            <div class="col-lg-7">
                <div class="d-grid gap-3 gap-lg-5">
                    <form action="{{ route('admin.map.config.update') }}" method="get" enctype="multipart/form-data">
                        @csrf
                        <div class="card">
                            <div class="card-header card-header-content-sm-between">
                                <h2 class="card-title h4">@lang('Map Settings')</h2>
                                <ul class="nav nav-segment nav-fill" id="projectsTab" role="tablist">
                                    <li class="nav-item" data-bs-toggle="chart" data-datasets="0" data-trigger="click" data-action="toggle">
                                        <a class="nav-link @if($basicControl->is_google_map == 0) active @endif" href="{{ route('admin.map.config.update', 'leaflet') }}">
                                            @lang("Leaflet Map")
                                        </a>
                                    </li>
                                    <li class="nav-item" data-bs-toggle="chart" data-datasets="1" data-trigger="click" data-action="toggle">
                                        <a class="nav-link @if($basicControl->is_google_map == 1) active @endif" href="{{ route('admin.map.config.update', 'google') }}">
                                            @lang("Google Map")
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <div class="card-body">
                                <div class="card-body" id="mapCardBody">
                                    @if($basicControl->is_google_map == 0)
                                        <div class="row">
                                            <div class="shadow p-3 alert-soft-blue" role="alert">
                                                <div class="alert-box d-flex align-items-center">
                                                    <div>
                                                        <img class="avatar avatar-xl"
                                                             src="{{ asset('assets/admin/img/oc-megaphone.svg') }}"
                                                             alt="Image Description" data-hs-theme-appearance="default">
                                                        <img class="avatar avatar-xl"
                                                             src="{{ asset('assets/admin/img/oc-megaphone-light.svg') }}"
                                                             alt="Image Description" data-hs-theme-appearance="dark">
                                                    </div>

                                                    <div class=" ms-3">
                                                        <h3 class="mb-1">@lang("Attention!")</h3>
                                                            <p class="mb-0 text-body">
                                                                @lang('Leaflet Map is free and doesn\'t require an API key, but to enable direction functionality, you\'ll need to set up a Google API key. Click the Google Map button then input the Google Map Api Key.')
                                                            </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <label for="MapLabel" class="form-label">@lang('Google Map Api Key')</label>
                                                <input type="text"
                                                       class="form-control  @error('google_map_app_key') is-invalid @enderror"
                                                       name="google_map_app_key" id="MapLabel"
                                                       placeholder="@lang("Google Map App Key")" aria-label="@lang("Google Map App Key")"
                                                       autocomplete="off"
                                                       value="{{ old('google_map_app_key', $basicControl->google_map_app_key) }}" required>
                                                @error('google_map_app_key')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="col-sm-12 mt-2">
                                                <label for="MapIDLabel" class="form-label">@lang('Google Map ID')</label>
                                                <input type="text"
                                                       class="form-control  @error('google_map_id') is-invalid @enderror"
                                                       name="google_map_id" id="MapIDLabel"
                                                       placeholder="@lang("Google Map ID")" aria-label="@lang("Google Map ID")"
                                                       autocomplete="off"
                                                       value="{{ old('google_map_id', $basicControl->google_map_id) }}" required>
                                                @error('google_map_id')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                @if(adminAccessRoute(config('role.control_panel.access.edit')))
                                    <div class="d-flex justify-content-start mt-lg-5 {{ $basicControl->is_google_map == 0 ? 'd-none' : '' }}">
                                        <button type="submit" class="btn btn-primary">@lang("Save changes")</button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- End Content -->
@endsection

@push('css-lib')
@endpush
@push('js-lib')
@endpush

@push('script')
    <script>
        'use strict';
    </script>
@endpush


