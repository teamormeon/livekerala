@extends('admin.layouts.app')
@section('page_title', __('City Edit'))
@section('content')
    <div class="content container-fluid">
        <div class="page-header">
            <div class="row align-items-end">
                <div class="col-sm">
                    <h1 class="page-header-title">@lang("City Edit")</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb breadcrumb-no-gutter">
                            <li class="breadcrumb-item"><a class="breadcrumb-link"
                                                           href="{{ route('admin.dashboard') }}">@lang('Dashboard')</a></li>
                            <li class="breadcrumb-item" aria-current="page">@lang("Countries")</li>
                            <li class="breadcrumb-item" aria-current="page">@lang("Manage State")</li>
                            <li class="breadcrumb-item" aria-current="page">@lang("State List")</li>
                            <li class="breadcrumb-item" aria-current="page">@lang("Manage City")</li>
                            <li class="breadcrumb-item active" aria-current="page">@lang("City Edit")</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>


        <div class="row d-flex justify-content-center">
            <div class="col-lg-10">
                <div class="d-grid gap-3 gap-lg-5">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <h3 class="card-title mt-2">@lang("City Edit")</h3>
                            <a href="{{ route('admin.country.state.all.city',[$city->country_id, $city->state_id]) }}" class="btn btn-primary">@lang("Back")</a>
                        </div>
                        <div class="card-body mt-2">
                            <form action="{{ route('admin.country.state.city.update', [$city->country_id,$city->state_id,$city->id]) }}" method="post" enctype="multipart/form-data">
                                @csrf

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="NameLabel" class="form-label  ">@lang("City Name")</label>
                                            <div class="input-group input-group-sm-vertical">
                                                <input type="text" class="form-control change_name_input" name="name"
                                                       id="NameLabel" value="{{ $city->name }}"
                                                       placeholder="@lang("City Name")" autocomplete="off">
                                            </div>
                                            @error("name")
                                            <span class="invalid-feedback d-block">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="Latitude" class="form-label  ">@lang("Latitude")</label>
                                            <div class="input-group input-group-sm-vertical">
                                                <input type="text" class="form-control change_name_input" name="latitude"
                                                       id="Latitude" value="{{ $city->latitude }}"
                                                       placeholder="@lang("Latitude")" autocomplete="off">
                                            </div>
                                            @error("latitude")
                                            <span class="invalid-feedback d-block">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6 mt-2">
                                        <div class="form-group">
                                            <label for="Longitude" class="form-label  ">@lang("Longitude")</label>
                                            <div class="input-group input-group-sm-vertical">
                                                <input type="text" class="form-control change_name_input" name="longitude"
                                                       id="Longitude" value="{{ $city->longitude }}"
                                                       placeholder="@lang("Longitude")" autocomplete="off">
                                            </div>
                                            @error("longitude")
                                            <span class="invalid-feedback d-block">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="list-group list-group-lg list-group-flush list-group-no-gutters">
                                            <div class="list-group-item">
                                                <div class="d-flex">
                                                    <div class="flex-grow-1 ms-3">
                                                        <div class="row align-items-center">
                                                            <div class="col-sm mb-2 mb-sm-0">
                                                                <h5 class="mb-0">@lang('Status')</h5>
                                                                <p class="fs-5 text-body mb-0">@lang('City status enable or Disable for hide or unhide city. ')</p>
                                                            </div>
                                                            <div class="col-sm-auto d-flex align-items-center">
                                                                <div class="form-check form-switch form-switch-google">
                                                                    <input type="hidden" name="status" value="0">
                                                                    <input class="form-check-input" name="status"
                                                                           type="checkbox" id="status" value="1" {{ $city->status == 1 ? 'checked' : '' }}>
                                                                    <label class="form-check-label"
                                                                           for="status"></label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-end mt-4">
                                        <button type="submit"
                                                class="btn btn-primary submit_btn">@lang("Update")</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection








