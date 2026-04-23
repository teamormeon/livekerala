@extends('user_panel.layouts.user')
@section('title',trans('Analytics'))
@push('style')
    <link rel="stylesheet" href="{{ asset('assets/global/css/bootstrap-datepicker.css') }}"/>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="main row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="mb-0">@lang('Analytics')</h3>
                </div>

                <!-- table -->
                <div class="table-parent table-responsive">
                    <div class="py-3 d-flex justify-content-between align-items-center">
                        <h4>@lang('Analytics')</h4>
                        <button type="button" class="cmn-btn customButton" data-bs-toggle="offcanvas" data-bs-target="#offcanvasExample" aria-controls="offcanvasExample">@lang('Filter')<i class="fal fa-filter"></i>
                        </button>
                    </div>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th scope="col">@lang('Listing')</th>
                                <th scope="col">@lang('Country')</th>
                                <th scope="col">@lang('Total Visit')</th>
                                <th scope="col">@lang('Last Visited At')</th>
                                <th scope="col" class="text-end">@lang('Action')</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($allAnalytics as $key => $analytic)
                                <tr>
                                    <td data-label="@lang('Listing')">
                                        <a href="{{ route('listing.details',optional($analytic->getListing)->slug) }}" class="color-change-listing text-secondary"
                                           target="_blank">@lang(\Illuminate\Support\Str::limit(optional($analytic->getListing)->title, 50))</a>
                                    </td>

                                    <td data-label="@lang('Country')">
                                        {{ ($analytic->country) ? __($analytic->country) : __('N/A') }}
                                    </td>

                                    <td data-label="@lang('Total Visit')"><span class="badge rounded-pill bg-light text-secondary">{{$analytic->list_count_count}}</span></td>

                                    <td data-label="@lang('Last Visited At')">
                                        {{ dateTime(optional($analytic->lastVisited)->created_at) }}
                                    </td>

                                    <td class="action" data-label="@lang('Action')">
                                        <div class="d-flex justify-content-end">
                                            <a class="btn2 btn" href="{{ route('user.analytics.show', $analytic->listing_id) }}">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    </td>
                            @empty
                                <td class="text-center" colspan="100%">
                                    <img class="noDataImg" src="{{ asset('assets/admin/img/oc-error.svg') }}" alt="image">
                                    <p class="mt-3">@lang('No data available')</p>
                                </td>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $allAnalytics->appends($_GET)->links('user_panel.partials.pagination') }}
            </div>
        </div>
    </div>

    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasExample" aria-labelledby="offcanvasExampleLabel">
        <div class="offcanvas-header">
            <h4 class="offcanvas-title" id="offcanvasExampleLabel">@lang('Filter Analytics')</h4>
            <button type="button" class="cmn-btn-close" data-bs-dismiss="offcanvas" aria-label="Close">
                <i class="fal fa-arrow-right"></i>
            </button>
        </div>
        <div class="offcanvas-body">
            <form action="" method="get" enctype="multipart/form-data">
                <div class="row g-4">
                    <div class="input-box">
                        <label for="search" class="form-label">@lang('Search')</label>
                        <input type="text" name="listing" value="{{ old('listing',request()->listing) }}" class="form-control" placeholder="@lang('Search')"/>
                    </div>
                    <div class="input-box">
                        <label for="search" class="form-label">@lang('From Date')</label>
                        <input type="text" class="form-control datepicker from_date" name="from_date" autofocus="off" readonly placeholder="@lang('From Date')" value="{{ old('from_date',request()->from_date) }}">
                    </div>
                    <div class="input-box">
                        <label class="form-label">@lang('To Date')</label>
                        <input type="text" class="form-control datepicker to_date" name="to_date" autofocus="off" readonly placeholder="@lang('To Date')" value="{{ old('to_date',request()->to_date) }}" disabled="true">
                    </div>
                    <div class="btn-area">
                        <button type="submit" class="cmn-btn w-100">@lang('Filter')</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('script')
    <script src="{{ asset('assets/global/js/bootstrap-datepicker.js') }}"></script>
    <script>
        'use strict'
        $(document).ready(function () {
            $(".datepicker").datepicker({
                autoclose: true,
                clearBtn: true
            });

            $('.from_date').on('change', function () {
                $('.to_date').removeAttr('disabled');
            });
        });
    </script>
@endpush
