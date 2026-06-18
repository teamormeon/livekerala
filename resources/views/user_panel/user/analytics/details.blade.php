@extends('user_panel.layouts.user')
@section('title',trans('Analytics Details'))
@push('style')
    <link rel="stylesheet" href="{{ asset('assets/global/css/bootstrap-datepicker.css') }}"/>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="main row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="mb-0">@lang('Analytics Details') </h3>
                </div>

                <!-- table -->
                <div class="table-parent table-responsive">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>@lang('Browser')</th>
                            <th>@lang('Operating System')</th>
                            <th>@lang('Country')</th>
                            <th>@lang('City')</th>
                            <th>@lang('Visited At')</th>
                        </tr>
                        </thead>
                        <tbody>
                            @forelse($allSingleListingAnalytics as $key => $analytic)
                                <tr>
                                    <td class="align-items-center d-flex">
                                        <img class="avatar avatar-xss me-2"
                                             src="{{ asset("assets/admin/img/browser/".browserIcon($analytic->browser).".svg") }}"
                                             alt="Image Description"> {{ $analytic->browser }}
                                    </td>
                                    <td>
                                        <i class="{{deviceIcon($analytic->os_platform,'web')}} fs-3 me-2"></i> {{ $analytic->os_platform }}
                                        @if($key== 0 )
                                            <span class="badge rounded-pill bg-success text-success ms-1">@lang("Current")</span>
                                        @endif
                                    </td>
                                    <td data-label="@lang('Country')">
                                        {{ ($analytic->country) ? __($analytic->country) : __('N/A') }}
                                    </td>
                                    <td data-label="@lang('City')">
                                        {{ ($analytic->city) ? __($analytic->city) : __('N/A') }}
                                    </td>

                                    <td data-label="@lang('Visited At')">
                                        {{ timeAgo($analytic->created_at) }}
                                    </td>
                                </tr>
                            @empty
                                <td class="text-center" colspan="100%">
                                    <img class="noDataImg" src="{{ asset('assets/admin/img/oc-error.svg') }}" alt="image">
                                    <p class="mt-3">@lang('No data available')</p>
                                </td>
                            @endforelse
                        </tbody>
                    </table>
                    {{ $allSingleListingAnalytics->appends($_GET)->links('user_panel.partials.pagination') }}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="{{ asset('assets/global/js/bootstrap-datepicker.js') }}"></script>
    <script>
        'use strict'
        $(document).ready(function () {
            $(".datepicker").datepicker({});

            $('.from_date').on('change', function () {
                $('.to_date').removeAttr('disabled');
            });
        });
    </script>
@endpush
