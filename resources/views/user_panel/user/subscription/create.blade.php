@extends(template().'layouts.app')
@section('title',trans('Subscription'))

@section('content')
    <section class="payment-section">
        <div class="container">
            <form action="{{route('user.subscriptionPurchase').'?id='.$subscriptionPlan->id}}" method="post">
                @csrf
                <div class="row g-5">
                    <div class="col-lg-7 col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="mb-15">@lang('How would you like to pay?')</h4>
                                <ul class="payment-container-list">
                                    @foreach($methods as $key => $method)
                                        <li class="item">
                                            <input class="form-check-input methodId" type="radio" name="methodId"
                                                   id="flexRadioDefault{{$key}}" value="{{ $method->id }}"
                                                {{ old('methodId') == $method->id || $key == 0 ? ' checked' : ''}}>
                                            <label class="form-check-label" for="flexRadioDefault{{$key}}">
                                                <div class="image-area">
                                                    <img src="{{ getFile($method->driver,$method->image ) }}" alt="...">
                                                </div>
                                                <div class="content-area">
                                                    <h5>@lang($method->name)</h5>
                                                    <span>@lang($method->description)</span>
                                                </div>
                                            </label>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5 col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <!-- Transfer details section start -->
                                <div class="transfer-details-section">
                                    <ul class="transfer-list">
                                        <div>
                                            <label for="">@lang('Select Currency')</label>
                                            <select class="js-example-basic-single form-control mt-2"
                                                    name="supported_currency"
                                                    id="supported_currency">
                                            </select>
                                        </div>
                                        <hr>
                                        <li class="item title">
                                            <h6>@lang('Purchase summary')</h6>
                                        </li>
                                        <div class="showCharge">
                                        </div>
                                    </ul>
                                    <button type="submit" class="cmn-btn w-100">@lang('confirm and continue')</button>
                                </div>
                                <!-- Transfer details section end -->
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>
    <input type="hidden" id="amount" value="{{$subscriptionPlan->price}}">
@endsection

@push('extra_scripts')
    <script>
        'use strict'
        $(document).ready(function () {
            let amountField = $('#amount');
            let amountStatus = false;
            let selectedGateway = "{{$methods[0]->id}}";
            supportCurrency(selectedGateway);

            function clearMessage(fieldId) {
                $(fieldId).removeClass('is-valid')
                $(fieldId).removeClass('is-invalid')
                $(fieldId).closest('div').find(".invalid-feedback").html('');
                $(fieldId).closest('div').find(".is-valid").html('');
            }

            $(document).on('click', '.methodId', function () {
                let id = this.id;

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
                    success: function (data) {
                        if (data === "") {
                            let markup = `<option value="USD">USD</option>`;
                            $('#supported_currency').append(markup);
                        }

                        let markup = '<option value="">Selected Currency</option>';
                        $('#supported_currency').append(markup);

                        $(data).each(function (index, value) {

                            let markup = `<option value="${value}">${value}</option>`;
                            $('#supported_currency').append(markup);
                        });
                    },
                    error: function (error) {
                        console.error('AJAX Error:', error);
                    }
                });
            }


            $(document).on('change, input', "#amount, #supported_currency, .selectPayment", function (e) {
                let amount = amountField.val();
                let selectedCurrency = $('#supported_currency').val();
                let currency_type = 1;


                if (!isNaN(amount) && amount > 0) {

                    let fraction = amount.split('.')[1];
                    let limit = currency_type == 0 ? 8 : 2;


                    if (fraction && fraction.length > limit) {
                        amount = (Math.floor(amount * Math.pow(10, limit)) / Math.pow(10, limit)).toFixed(limit);
                        amountField.val(amount);
                    }

                    checkAmount(amount, selectedCurrency, selectedGateway)

                    if (selectedCurrency != null) {

                    }
                } else {
                    clearMessage(amountField)
                    $('.showCharge').html('')
                }
            });


            function checkAmount(amount, selectedCurrency, selectGateway) {

                $.ajax({
                    method: "GET",
                    url: "{{ route('deposit.checkAmount') }}",
                    dataType: "json",
                    data: {
                        'amount': amount,
                        'selected_currency': selectedCurrency,
                        'select_gateway': selectGateway,
                    }
                }).done(function (response) {



                    let amountField = $('#amount');
                    if (response.status) {
                        clearMessage(amountField);
                        $(amountField).addClass('is-valid');
                        $(amountField).closest('div').find(".valid-feedback").html(response.message);
                        amountStatus = true;
                        // submitButton();
                        showCharge(response, response.currency);
                    } else {
                        amountStatus = false;
                        // submitButton();
                        $('.showCharge').html('');
                        clearMessage(amountField);
                        $(amountField).addClass('is-invalid');
                        $(amountField).closest('div').find(".invalid-feedback").html(response.message);
                    }


                });
            }

            function showCharge(response, currency) {
                let txnDetails = `<li class="item"><span>{{$subscriptionPlan->plan_name}}</span><span>{{$subscriptionPlan->price}} {{basicControl()->base_currency}}</span></li>
                <li class="item"><span>{{ __('Payment Method')}}</span><h6>${response.gatewayName}</span></li>
            <li class="item text-danger"><span>{{ __('Transfer Charge')}}</span><span>${response.percentage_charge} + ${response.fixed_charge} = ${response.charge} ${response.base_currency}</span></li>
            <li class="item"><span>{{ __('Exchange Rate')}}</span><span>1  ${response.base_currency} <i class="fa-light fa-arrow-right-arrow-left fa-sm"></i>  ${response.conversion_rate} ${currency}</span></li>
            <li class="item"><span><strong>{{ __('You will pay')}}</strong></span><span><strong>${response.payable_amount} ${response.currency}</strong></span></li>
            `;
                $('.showCharge').html(txnDetails)
            }

        });
    </script>

    @if ($errors->any())
        @php
            $collection = collect($errors->all());
            $errors = $collection->unique();
        @endphp
        <script>
            "use strict";
            @foreach ($errors as $error)
            Notiflix.Notify.failure("{{ trans($error) }}");
            @endforeach
        </script>
    @endif
@endpush
