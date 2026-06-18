@extends(template().'layouts.app')
@section('title')
    {{ __('Pay with ').__(optional($deposit->gateway)->name) }}
@endsection
@section('content')
    <div class="payment-section-div">
        <div class="content container" id="main">
            <div class="row">
                <div class="col-xl-6 col-lg-8 col-md-10 mx-auto">
                    <h3>@lang('Pay with '.optional($deposit->gateway)->name)</h3>
                    <div class="payment-box">
                        <div class="img-box p-3">
                            <img src="{{getFile(optional($deposit->gateway)->driver,optional($deposit->gateway)->image)}}" class="card-img-top gateway-img rounded-2 img-fluid">
                        </div>
                        <div class="text-box">
                            <h4 class="pb-2">@lang('Please Pay') {{getAmount($deposit->payable_amount)}} {{$deposit->payment_method_currency}}</h4>
                            <button type="button"
                                    class="btn-custom cmn-btn py-2"
                                    id="payment-button">@lang('Pay with Khalti') <span></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection

@push('script')
    <script
        src="https://khalti.s3.ap-south-1.amazonaws.com/KPG/dist/2020.12.17.0.0.0/khalti-checkout.iffe.js"></script>
    <script>

        $(document).ready(function () {
            $('body').addClass('antialiased')
        });

        let config = {
            "publicKey": "{{$data->publicKey}}",
            "productIdentity": "{{$data->productIdentity}}",
            "productName": "Payment",
            "productUrl": "{{url('/')}}",
            "paymentPreference": [
                "KHALTI",
                "EBANKING",
                "MOBILE_BANKING",
                "CONNECT_IPS",
                "SCT",
            ],
            "eventHandler": {
                onSuccess(payload) {
                    $.ajax({
                        type: 'POST',
                        url: "{{ route('khalti.verifyPayment',[$deposit->trx_id]) }}",
                        data: {
                            token: payload.token,
                            amount: payload.amount,
                            "_token": "{{ csrf_token() }}"
                        },
                        success: function (res) {
                            $.ajax({
                                type: "POST",
                                url: "{{ route('khalti.storePayment') }}",
                                data: {
                                    response: res,
                                    "_token": "{{ csrf_token() }}"
                                },
                                success: function (res) {
                                    window.location.href = "{{route('success')}}"
                                }
                            });
                        }
                    });
                },
                onError(error) {
                },
                onClose() {
                }
            }
        };
        let checkout = new KhaltiCheckout(config);
        let btn = document.getElementById("payment-button");
        btn.onclick = function () {
            checkout.show({amount: "{{$data->amount *100}}"});
        }
    </script>
@endpush
