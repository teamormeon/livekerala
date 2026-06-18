@extends(template().'layouts.app')
@section('title')
    {{ 'Pay with '.$gateway->name ?? '' }}
@endsection
@push('css-lib')

@endpush
@section('content')
<section id="dashboard" class="section__padding">
    <div class="container add-fund pb-50">
        <div class="row justify-content-center">
            <div class="col-md-8 col-xl-8 col-sm-12">
                <h3>@lang('Subscribe with '.optional($deposit->gateway)->name)</h3>
                <div class="card secbg br-4 custom-card-payment">
                    <div class="card-body br-4">
                        <div class="row align-items-center">
                            <div class="col-md-3">
                                <img src="{{getFile($gateway->driver, $gateway->image)}}"
                                    class="card-img-top gateway-img br-4" alt="{{basicControl()->site_title}}">
                            </div>
                            <div class="col-md-9" id="paypal-button-container">
                                <h4>@lang('Please Pay') {{getAmount($deposit->amount)}} {{$deposit->payment_method_currency}}</h4>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
@push('extra-js')
    <script src="https://cdn.paddle.com/paddle/v2/paddle.js"></script>
    <script>
        console.log('log')
    </script>
    <script type="text/javascript">
        var gatewayMode = "{{$gateway->environment == 'test'}}";
        var subPurchaseId = "{{$deposit->depositable_id}}";
        var priceId = "{{json_decode($deposit->depositable->gateway_plan_id)->paddle}}";
        var clientToken = "{{@$gateway->parameters->client_side_token}}";
        if (gatewayMode) {
            Paddle.Environment.set("sandbox");
        }
        var itemsList = [
            {
                priceId: priceId,
                quantity: 1
            },
        ];
        Paddle.Setup({
            token: clientToken, // replace with a client-side token
            checkout: {
                settings: {
                    displayMode: "overlay",
                    theme: "light",
                    locale: "en",
                }
            },
            eventCallback: function (data) {
                if (data.name == "checkout.completed") {
                    let route = '{{ route('paddleSubscription') }}/';
                    window.location.href = route + subPurchaseId + '/' + data.data.transaction_id;
                }
            }
        });


        Paddle.Checkout.open({
            items: itemsList,
        });
    </script>
@endpush
