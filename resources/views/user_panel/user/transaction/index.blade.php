
@extends('user_panel.layouts.user')
@section('title',trans('Transactions'))

@push('style')
    <link rel="stylesheet" href="{{ asset('assets/global/css/bootstrap-datepicker.css') }}" />
@endpush

@section('content')
    <div class="container-fluid">
        <div class="main row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="mb-0">@lang('All Transactions')</h3>
                </div>

                <!-- table -->
                <div class="table-parent table-responsive">
                    <div class="table-heading py-3 d-flex justify-content-between align-items-center">
                        <h4>@lang('Transactions')</h4>
                        <button type="button" class="cmn-btn customButton" data-bs-toggle="offcanvas" data-bs-target="#offcanvasExample" aria-controls="offcanvasExample">@lang('Filter')<i class="fal fa-filter"></i>
                        </button>
                    </div>
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th scope="col">@lang('Transaction ID')</th>
                            <th scope="col">@lang('Amount')</th>
                            <th scope="col">@lang('Charge')</th>
                            <th scope="col">@lang('Remarks')</th>
                            <th scope="col">@lang('Date-Time')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse ($transactions as $key => $transaction)
                            <tr>
                                <td data-label="Transaction ID">
                                    @lang($transaction->trx_id)
                                </td>
                                <td data-label="Amount">
                                    <span class="font-weight-bold text-dark">
                                        {{ currencyPosition($transaction->amount)}}
                                    </span>
                                </td>
                                <td data-label="Charge">
                                    <span class="font-weight-bold text-danger">
                                        {{ currencyPosition($transaction->charge)}}
                                    </span>
                                </td>

                                <td data-label="Remarks">
                                    @lang($transaction->remarks)
                                </td>

                                <td data-label="Date-Time">
                                    {{ dateTime($transaction->created_at) }}
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
                </div>
                {{ $transactions->appends($_GET)->links('user_panel.partials.pagination') }}
            </div>
        </div>
    </div>

    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasExample" aria-labelledby="offcanvasExampleLabel">
        <div class="offcanvas-header">
            <h4 class="offcanvas-title" id="offcanvasExampleLabel">@lang('Filter Listing')</h4>
            <button type="button" class="cmn-btn-close" data-bs-dismiss="offcanvas" aria-label="Close">
                <i class="fal fa-arrow-right"></i>
            </button>
        </div>
        <div class="offcanvas-body">
            <form action="{{ route('user.transaction') }}" method="get" enctype="multipart/form-data">
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
            $( ".datepicker" ).datepicker({
                autoclose: true,
                clearBtn: true
            });

            $('.from_date').on('change', function (){
                $('.to_date').removeAttr('disabled');
            });

        });
    </script>
@endpush

