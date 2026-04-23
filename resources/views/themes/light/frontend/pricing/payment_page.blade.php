@extends(template().'layouts.app')
@section('title',trans('Pricing | Payment'))

@section('content')
    <div class="container" id="listing-payment">
        <div class="row mt-5 mb-5">
            <div class="container-fluid">
                <div class="main row g-4">
                    <div class="col-12">
                        <form action="{{ route('payment.request') }}" method="post" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="plan_id" value="{{ $plan_id }}">
                            <input type="hidden" name="purchase_id" value="{{ $purchase_id }}">
                            <input type="hidden" name="type" value="{{ $type }}">
                            <input type="hidden" name="cvt_amount" value="" id="cvtAmount">
                            <div class="row g-4">
                                <div class="col-lg-7 col-md-6">
                                    <div class="card method-card-header">
                                        <div class="card-body">
                                            <h5 class="mb-2">@lang('How would you like to pay?')</h5>
                                            <div class="payment-section">
                                                <ul class="payment-container-list">
                                                    @forelse($gateways as $method)
                                                        <li class="item paymentCheck">
                                                            <input class="form-check-input selectPayment" type="radio" name="gateway_id"
                                                                   id="{{ $method->name }}" value="{{ $method->id }}">
                                                            <label class="form-check-label" for="{{ $method->name }}">
                                                                <div class="image-area">
                                                                    <img src="{{ getFile($method->driver, $method->image ) }}" alt="">
                                                                </div>
                                                                <div class="content-area">
                                                                    <h5>@lang($method->name)</h5>
                                                                    <span>@lang($method->description)</span>
                                                                </div>
                                                            </label>
                                                        </li>
                                                    @empty
                                                    @endforelse
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 col-lg-5 paymentDiv">
                                    <div class="card method-card-details">
                                        <div class="card-body">
                                            <h5>@lang('Package Info')</h5>
                                            <div class="estimation-box">
                                                <div class="details_list">
                                                    <ul class="list-group">
                                                        <li class="list-group-item border-0 d-flex justify-content-between align-items-start">
                                                            <div class="ms-2 me-auto">@lang('Package Title')</div>
                                                            <span>@lang(optional($package->details)->title)</span>
                                                        </li>
                                                        <li class="list-group-item border-0 d-flex justify-content-between align-items-start">
                                                            <div class="ms-2 me-auto">@lang('Package Price')</div>
                                                            <span>{{ currencyPosition($package->price) }}</span>
                                                        </li>
                                                        <li class="list-group-item border-0 d-flex justify-content-between align-items-start">
                                                            <div class="ms-2 me-auto">@lang('Package Validity')</div>
                                                            <span>@lang($package->expiry_time . ' ' . $package->expiry_time_type)</span>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card method-card-details mt-4">
                                        <div class="card-body payment-summery">
                                            <h5>@lang('Payment Summary')</h5>
                                            <span>@lang('Total Amount ')<sub class="fw-normal">( {{ basicControl()->currency_symbol }} )</sub></span>
                                            <input type="text" class="form-control" name="amount" id="total_amount" value="{{ $package->price }}" readonly/>
                                            <span class="invalid-feedback"></span>
                                            <div class="col-md-12 input-box mt-3 selectCurrencyInput">
                                                <label for="">@lang('Select Currency')</label>
                                                <select class="cmn-select2 js-example-basic-single form-control"
                                                        name="supported_currency"
                                                        id="supported_currency">
                                                </select>
                                            </div>
                                            <div class="col-md-12 input-box mt-3 add-select-field selectCurrencyInput">

                                            </div>
                                            <div class="showCharge cart-total">

                                            </div>
                                            <button type="submit" class="btn-custom mt-3 w-100" data-type="{{ $type }}">@lang('confirm and continue')</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Modal for Payment Gateways (for mobile) -->
                            <div class="modal fade" id="gatewayModal" tabindex="-1" aria-labelledby="gatewayModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="gatewayModalLabel">{{ trans('Payment Info & Summary') }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body" id="paymentModalBody">

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('style')
    <style>
        @media (max-width: 767px) {
            .method-card-details {
                display: none;
            }
        }
    </style>
@endpush

@push('script')
    <script>
        'use strict';
        $(document).ready(function () {
            let amountField = $('#total_amount');
            let amountStatus = false;
            let selectedGateway = "";
            function clearMessage(fieldId) {
                $(fieldId).removeClass('is-valid')
                $(fieldId).removeClass('is-invalid')
                $(fieldId).closest('div').find(".invalid-feedback").html('');
                $(fieldId).closest('div').find(".is-valid").html('');
            }

            $('#showGatewaysButton').on('click', function () {
                $('#gatewayModal').modal('show');
            });

            $(document).on('click', '.selectPayment', function () {
                let id = this.id;
                $('#paymentModalBody').html('');
                let updatedWidth = window.innerWidth;
                window.addEventListener('resize', () => {
                    updatedWidth = window.innerWidth;
                });
                let html = `<div class="card method-card-details">
                                        <div class="card-body">
                                            <h5>@lang('Package Info')</h5>
                                            <div class="estimation-box">
                                                <div class="details_list">
                                                    <ul class="list-group">
                                                        <li class="list-group-item border-0 d-flex justify-content-between align-items-start">
                                                            <div class="ms-2 me-auto">@lang('Package Title')</div>
                                                            <span>@lang(optional($package->details)->title)</span>
                                                        </li>
                                                        <li class="list-group-item border-0 d-flex justify-content-between align-items-start">
                                                            <div class="ms-2 me-auto">@lang('Package Price')</div>
                                                            <span>{{ currencyPosition($package->price) }}</span>
                                                        </li>
                                                        <li class="list-group-item border-0 d-flex justify-content-between align-items-start">
                                                            <div class="ms-2 me-auto">@lang('Package Validity')</div>
                                                            <span>@lang($package->expiry_time . ' ' . $package->expiry_time_type)</span>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card method-card-details mt-4">
                                        <div class="card-body payment-summery">
                                            <h5>@lang('Payment Summary')</h5>
                                            <span>@lang('Total Amount ')<sub class="fw-normal">( {{ basicControl()->currency_symbol }} )</sub></span>
                                            <input type="text" class="form-control" name="amount" id="total_amount" value="{{ $package->price }}" readonly/>
                                            <span class="invalid-feedback"></span>
                                            <div class="col-md-12 input-box mt-3 selectCurrencyInput">
                                                <label for="">@lang('Select Currency')</label>
                                                <select class="cmn-select2 js-example-basic-single form-control"
                                                        name="supported_currency"
                                                        id="supported_currency">
                                                </select>
                                            </div>
                                            <div class="col-md-12 input-box mt-3 add-select-field selectCurrencyInput">

                                            </div>
                                            <div class="showCharge cart-total">

                                            </div>
                                            <button type="submit" class="btn-custom mt-3 w-100" data-type="{{ $type }}">@lang('confirm and continue')</button>
                                        </div>
                                    </div>`;
                if (updatedWidth <= 767) {
                    $('.paymentDiv').html('');
                    $('#paymentModalBody').html(html);
                    $('#gatewayModal').modal('show');
                    $('#paymentModalBody .method-card-details').css('display', 'block');
                }else {
                    $('.paymentDiv').html(html);
                }
                selectedGateway = $(this).val();
                supportCurrency(selectedGateway);
            });

            function supportCurrency(selectedGateway) {
                if (!selectedGateway) {
                    console.error('Selected Gateway is undefined or null.');
                    return;
                }

                $('#supported_currency').empty();

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    url: "{{ route('supported.currency') }}",
                    data: {gateway: selectedGateway},
                    type: "GET",
                    success: function (response) {

                        if (response.data === "") {
                            let markup = `<option value="USD">USD</option>`;
                            $('#supported_currency').append(markup);
                        }

                        let markup = '<option value="">Selected Currency</option>';
                        $('#supported_currency').append(markup);


                        if (response.currencyType === 1) {
                            $(response.data).each(function (index, value) {
                                let markup = `<option value="${value}">${value}</option>`;
                                $('#supported_currency').append(markup);
                            });
                        } else {
                            let markup = `<option value="USD">USD</option>`;
                            $('#supported_currency').append(markup);
                        }

                        let markup2 = '<option value="">Selected Crypto Currency</option>';
                        $('#supported_crypto_currency').append(markup2);

                        if (response.currencyType === 0){
                            let markup2 = `<label for="">Pay To Crypto Currency</label>
                                        <select class="js-example-basic-single form-control"
                                                name="supported_crypto_currency"
                                                id="supported_crypto_currency">
                                              <option value="">Selected Crypto Currency</option>
                                        </select>`;
                            $('.add-select-field').append(markup2);

                            $(response.data).each(function (index, value) {
                                let markupOption = `<option value="${value}">${value}</option>`;
                                $('#supported_crypto_currency').append(markupOption);
                            });
                        }
                    },
                    error: function (error) {
                        console.error('AJAX Error:', error);
                    }
                });
            }

            $(document).on('change, input', "#totalAmount, #supported_currency, .selectPayment, #supported_crypto_currency", function (e) {
                let amount = amountField.val();
                // console.log(amount)
                let selectedCurrency = $('#supported_currency').val();
                let selectedCryptoCurrency = $('#supported_crypto_currency').val();
                let currency_type = 1;

                if (!isNaN(amount) && amount > 0) {

                    let fraction = amount.split('.')[1];
                    let limit = currency_type == 0 ? 8 : 2;

                    if (fraction && fraction.length > limit) {
                        amount = (Math.floor(amount * Math.pow(10, limit)) / Math.pow(10, limit)).toFixed(limit);
                        amountField.val(amount);
                    }

                    checkAmount(amount, selectedCurrency, selectedGateway, selectedCryptoCurrency)

                    if (selectedCurrency != null) {

                    }
                } else {
                    clearMessage(amountField)
                    $('.showCharge').html('')
                }
            });

            function checkAmount(amount, selectedCurrency, selectGateway, selectedCryptoCurrency = null) {
                $.ajax({
                    method: "GET",
                    url: "{{ route('deposit.checkConvertAmount') }}",
                    dataType: "json",
                    data: {
                        'amount': amount,
                        'selected_currency': selectedCurrency,
                        'select_gateway': selectGateway,
                        'selectedCryptoCurrency': selectedCryptoCurrency,
                    }
                }).done(function (response) {
                    // console.log(response.message)
                    $('#cvtAmount').val(response.amount);
                    let amountField = $('#total_amount');
                    if (response.status) {
                        clearMessage(amountField);
                        $(amountField).addClass('is-valid');
                        $(amountField).closest('div').find(".valid-feedback").html(response.message);
                        amountStatus = true;
                        let base_currency = "{{basicControl()->base_currency}}"
                        showCharge(response, base_currency);
                        $('.cmn-btn').removeAttr('disabled');
                    } else {
                        amountStatus = false;
                        $('.showCharge').html('');
                        clearMessage(amountField);
                        $(amountField).addClass('is-invalid');
                        $(amountField).closest('div').find(".invalid-feedback").html(response.message);
                    }
                });
            }

            function showCharge(response, currency) {
                let txnDetails = `<ul">
                                    <li class="d-flex justify-content-between">
                                        <span>{{ __('Amount In') }} ${response.currency}</span>
                                        <span class="text-success"> ${response.amount} ${response.currency}</span>
                                    </li>
                                    <li class="d-flex justify-content-between">
                                        <span>{{ __('Charge') }}</span>
                                        <span class="text-danger">  ${response.charge} ${response.currency}</span>
                                    </li>
                                    <li class="d-flex justify-content-between">
                                        <span>{{ __('Payable Amount') }}</span>
                                        <span class="text-info"> ${response.payable_amount} ${response.currency}</span>
                                    </li>
                                    <li class="d-flex justify-content-between">
                                        <span>{{ __('Payable Amount') }} <sub>(In Base Currency)</sub></span>
                                        <span class="text-info"> ${response.amount_in_base_currency} ${currency}</span>
                                    </li>
                                </ul>`;
                $('.showCharge').html(txnDetails)
            }


        });
    </script>
@endpush
