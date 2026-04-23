@extends('user_panel.layouts.user')
@section('title',trans('Payment History'))
@push('style')
    <link rel="stylesheet" href="{{ asset('assets/global/css/bootstrap-datepicker.css') }}"/>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="main row">
            <div class="col-12">
                <div
                    class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="mb-0">@lang('Payment History')</h3>
                </div>

                <!-- table -->
                <div class="table-parent table-responsive">
                    <div class="table-heading py-3 d-flex justify-content-between align-items-center">
                        <h4>@lang('Payment History For') (@lang( optional(optional($purchasePackage->get_package)->details)->title))</h4>
                        <button type="button" class="cmn-btn customButton" data-bs-toggle="offcanvas" data-bs-target="#offcanvasExample" aria-controls="offcanvasExample">@lang('Filter')<i class="fal fa-filter"></i>
                        </button>
                    </div>

                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>@lang('TRX')</th>
                                <th>@lang('Amount')</th>
                                <th>@lang('Payment Method')</th>
                                <th>@lang('Payment Status')</th>
                                <th>@lang('Remark')</th>
                                <th>@lang('Date-Time')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($allTransaction as $key => $transaction)
                                <tr>
                                    <td data-label="@lang('Transaction_ID')">
                                        @lang($transaction->trx_id)
                                    </td>
                                    <td data-label="@lang('Amount')">
                                        {{ currencyPosition($transaction->amount) }}
                                    </td>
                                    <td data-label="@lang('Payment Method')">
                                        {{ $transaction->gateway == null ? __('N/A') : __(optional($transaction->gateway)->name) }}
                                    </td>
                                    <td data-label="@lang('Payment Status')">
                                        @if ($transaction->status == 0)
                                            <span class="badge rounded-pill bg-warning">@lang('Pending')</span>
                                        @elseif($transaction->status == 1)
                                            <span class="badge rounded-pill bg-success">@lang('Completed')</span>
                                        @elseif($transaction->status == 2)
                                            <span class="badge rounded-pill bg-info">@lang('Requested')</span>
                                        @else
                                            <span class="badge rounded-pill bg-danger">@lang('Rejected')</span>
                                        @endif
                                    </td>
                                    <td data-label="@lang('Remark')">
                                        @lang(ucwords($transaction->purchase_type .' '.optional(optional($purchasePackage->get_package)->details)->title))
                                    </td>
                                    <td data-label="@lang('Date-Time')">
                                        {{ dateTime($transaction->created_at) }}
                                    </td>
                            @empty
                                <td class="text-center" colspan="100%">
                                    <img class="noDataImg" src="{{ asset('assets/admin/img/oc-error.svg') }}" alt="image">
                                    <p class="mt-3">@lang('No data available')</p>
                                </td>
                            @endforelse
                        </tbody>
                    </table>
                    {{ $allTransaction->appends($_GET)->links('user_panel.partials.pagination') }}
                </div>
            </div>
        </div>
    </div>


    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasExample" aria-labelledby="offcanvasExampleLabel">
        <div class="offcanvas-header">
            <h4 class="offcanvas-title" id="offcanvasExampleLabel">@lang('Filter Payment History')</h4>
            <button type="button" class="cmn-btn-close" data-bs-dismiss="offcanvas" aria-label="Close">
                <i class="fal fa-arrow-right"></i>
            </button>
        </div>
        <div class="offcanvas-body">
            <form action="" method="get" enctype="multipart/form-data">
                <div class="row g-4">
                    <div class="input-box">
                        <label for="search" class="form-label">@lang('Transaction ID')</label>
                        <input type="text" name="transaction_id" value="{{@request()->transaction_id}}" class="form-control" placeholder="@lang('Transaction ID')">
                    </div>
                    <div class="input-box">
                        <label for="search" class="form-label">@lang('Remark')</label>
                        <input type="text" name="remark" value="{{@request()->remark}}" class="form-control" placeholder="@lang('Remark')">
                    </div>
                    <div class="input-box">
                        <label class="form-label">@lang('Date')</label>
                        <input type="text" class="form-control datepicker from_date" name="datetrx" autofocus="off" readonly placeholder="@lang('Choose Date')" value="{{ old('datetrx',request()->datetrx) }}">
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
