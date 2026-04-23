
@extends('admin.layouts.app')
@section('page_title',__('Listing Settings'))
@section('content')
    <div class="content container-fluid">
        <div class="page-header">
            <div class="row align-items-end">
                <div class="col-sm mb-2 mb-sm-0">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb breadcrumb-no-gutter">
                            <li class="breadcrumb-item">
                                <a class="breadcrumb-link" href="{{ route('admin.dashboard') }}">
                                    @lang('Dashboard')
                                </a>
                            </li>
                            <li class="breadcrumb-item" aria-current="page">@lang('Manage Listing')</li>
                            <li class="breadcrumb-item active" aria-current="page">@lang('Listing Settings')</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-8 m-auto">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('admin.listing.setting.update') }}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <label class="form-label">@lang('Listing Approval')</label>
                                <div class="list-group-item">
                                    <div class="d-flex">
                                        <div class="flex-grow-1">
                                            <div class="row align-items-center">
                                                <div class="col">
                                                <span class="d-block fs-6 text-body">
                                                    @lang('If you want the listing to require approval, then turn it on')
                                                </span>
                                                </div>
                                                <div class="col-auto">
                                                    <label class="row form-check form-switch mb-3" for="listing_approval">
                                                                <span class="col-4 col-sm-3 text-end">
                                                                    <input type='hidden' value='0' name='listing_approval'>
                                                                    <input class="form-check-input @error('listing_approval') is-invalid @enderror"
                                                                           type="checkbox" name="listing_approval" id="listing_approval" value="1"
                                                                        {{ old('listing_approval', basicControl()->listing_approval) == "1" ? 'checked' : '' }}>
                                                                </span>
                                                        @error('listing_approval')
                                                        <span class="invalid-feedback d-block">{{ $message }}</span>
                                                        @enderror
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group my-3 listingApprovalSelect">
                                <label class="form-label" for="before_expiry_date"> @lang('Package Expiry Notification')</label>
                                <select name="before_expiry_date[]" class="form-control w-100" multiple>
                                    <option disabled>@lang('Choose Time')</option>
                                    <option value="30" @foreach($packageExpiryCrons as $cron) @if($cron->before_expiry_date == '30') selected @endif @endforeach>@lang('Before 30 Days')</option>
                                    <option value="15" @foreach($packageExpiryCrons as $cron) @if($cron->before_expiry_date == '15') selected @endif @endforeach>@lang('Before 15 Days')</option>
                                    <option value="10" @foreach($packageExpiryCrons as $cron) @if($cron->before_expiry_date == '10') selected @endif @endforeach>@lang('Before 10 Days')</option>
                                    <option value="7" @foreach($packageExpiryCrons as $cron) @if($cron->before_expiry_date == '7') selected @endif @endforeach>@lang('Before 7 Days')</option>
                                    <option value="3" @foreach($packageExpiryCrons as $cron) @if($cron->before_expiry_date == '3') selected @endif @endforeach>@lang('Before 3 Days')</option>
                                    <option value="1" @foreach($packageExpiryCrons as $cron) @if($cron->before_expiry_date == '1') selected @endif @endforeach>@lang('Before 1 Day')</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary btn-block  btn-rounded ">
                                    <span>@lang('Save Changes')</span></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endsection


        @push('css-lib')
            <link rel="stylesheet" href="{{ asset('assets/global/css/select2.min.css') }}">
        @endpush
        @push('js-lib')
            <script src="{{ asset('assets/global/js/select2.min.js') }}"></script>
        @endpush

        @push('script')
            <script>
                'use strict'
                $(document).ready(function () {
                    $('select').select2({
                        selectOnClose: true
                    });
                });
            </script>
    @endpush




