@extends(template().'layouts.app')
@section('title')
    {{ __('Pay with ').__(optional($deposit->gateway)->name) }}
@endsection
@section('content')
    <div class="payment-section-div">
        <div class="content container">
            <div class="row">
                <div class="col-xl-6 col-lg-8 col-md-10 mx-auto">
                    <h3>@lang('Pay with '.optional($deposit->gateway)->name)</h3>
                    <div class="payment-box">
                        <div class="img-box">
                            <img src="{{getFile(optional($deposit->gateway)->driver,optional($deposit->gateway)->image)}}" class="card-img-top gateway-img rounded-2 img-fluid">
                        </div>
                        <div class="text-box">
                            <h4 class="pb-2">@lang('Please Pay') {{getAmount($deposit->payable_amount)}} {{$deposit->payment_method_currency}}</h4>
                            <button type="button" class="cmn-btn btn-custom py-2" id="btn-confirm" onClick="payWithRave()">@lang('Pay Now') <span></span> </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection

@push('script')
    <script src="https://api.ravepay.co/flwv3-pug/getpaidx/api/flwpbf-inline.js"></script>
    <script>
        'use strict';
        let btn = document.querySelector("#btn-confirm");
        btn.setAttribute("type", "button");
        const API_publicKey = "{{$data->API_publicKey }}";

        function payWithRave() {
            let x = getpaidSetup({
                PBFPubKey: API_publicKey,
                customer_email: "{{ $data->customer_email }}",
                amount: "{{ $data->amount }}",
                customer_phone: "{{ $data->customer_phone }}",
                currency: "{{ $data->currency }}",
                txref: "{{ $data->txref }}",
                onclose: function () {
                },
                callback: function (response) {
                    let txref = response.tx.txRef;
                    let status = response.tx.status;
                    window.location = '{{ url('payment/flutterwave') }}/' + txref + '/' + status;
                }
            });
        }
    </script>
@endpush
