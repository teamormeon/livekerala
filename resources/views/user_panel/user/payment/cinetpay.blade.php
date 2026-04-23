@extends(template().'layouts.app')
@section('title')
    {{ 'Pay with '.optional($deposit->gateway)->name ?? '' }}
@endsection


@section('content')

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://cdn.cinetpay.com/seamless/main.js"></script>
    <style>
        .sdk {
            display: block;
            position: absolute;
            background-position: center;
            text-align: center;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
        }
    </style>

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
                            <button class="btn-custom cmn-btn py-2" onclick="checkout()">@lang('Pay Now') <span></span></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('script')
    <script>
        function checkout() {
            CinetPay.setConfig({
                apikey: '{{ optional($deposit->gateway)->parameters->apiKey }}',//   YOUR APIKEY
                site_id: '{{ optional($deposit->gateway)->parameters->site_id }}',//YOUR_SITE_ID
                notify_url: '{{ route('ipn', [$deposit->gateway->code, $deposit->trx_id]) }}',
                return_url: '{{ route('success') }}',
                mode: 'PRODUCTION'
                // mode: 'SANDBOX'
            });
            CinetPay.getCheckout({
                transaction_id: '{{ $deposit->trx_id }}', // YOUR TRANSACTION ID
                amount: {{ (int) $deposit->payable_amount }},
                currency: '{{ $deposit->payment_method_currency }}',
                channels: 'ALL',
                description: 'Test de paiement',
                //Fournir ces variables pour le paiements par carte bancaire
                customer_name: "{{ optional($deposit->user)->username ?? 'abc' }}",//Le nom du client
                customer_surname: "{{ optional($deposit->user)->username ?? 'abc' }}",//Le prenom du client
                customer_email: "{{ optional($deposit->user)->email ?? 'abc@gmail.com' }}",//l'email du client
                customer_phone_number: "{{ optional($deposit->user)->phone ?? '0179386' }}",//l'email du client
                customer_address: "BP 0024",//addresse du client
                customer_city: "Antananarivo",// La ville du client
                customer_country: "CM",// le code ISO du pays
                customer_state: "CM",// le code ISO l'état
                customer_zip_code: "06510", // code postal

            });
            CinetPay.waitResponse(function (data) {
                if (data.status == "REFUSED") {
                    if (alert("Votre paiement a échoué")) {
                        window.location.reload();
                    }
                } else if (data.status == "ACCEPTED") {
                    if (alert("Votre paiement a été effectué avec succès")) {
                        window.location.reload();
                    }
                }
            });
            CinetPay.onError(function (data) {
                // console.log(data);
            });
        }
    </script>
@endpush



