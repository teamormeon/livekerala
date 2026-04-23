@extends(template().'layouts.user')
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
                            <th>@lang('Country')</th>
                            <th>@lang('City')</th>
                            <th>@lang('Browser')</th>
                            <th>@lang('Operatin System')</th>
                            <th>@lang('Visited At')</th>
                        </tr>
                        </thead>
                        <tbody>

                            @forelse($allSingleListingAnalytics as $key => $analytic)
                                <tr>
                                    <td data-label="@lang('Country')">
                                        {{ ($analytic->country) ? __($analytic->country) : __('N/A') }}
                                    </td>
                                    <td data-label="@lang('City')">
                                        {{ ($analytic->city) ? __($analytic->city) : __('N/A') }}
                                    </td>
                                    <td data-label="@lang('Browser')">
                                        @lang($analytic->browser)
                                    </td>
                                    <td data-label="@lang('Operating System')">
                                        @lang($analytic->os_platform)
                                    </td>

                                    <td data-label="@lang('Visited At')">
                                        {{ dateTime($analytic->created_at) }}
                                    </td>
                                    @empty
                                        <td colspan="100%" class="text-center">@lang('No Data Found')</td>
                            @endforelse
                        </tbody>
                    </table>
                    {{ $allSingleListingAnalytics->appends($_GET)->links() }}
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
