@extends(template().'layouts.app')
@section('title')
    {{ 'Pay with '.$gateway->name ?? '' }}
@endsection

@section('content')
    <script src="https://js.stripe.com/v3/"></script>
    <style>
        .StripeElement {
            box-sizing: border-box;
            height: 40px;
            padding: 10px 12px;
            border: 1px solid transparent;
            border-radius: 4px;
            background-color: white;
            box-shadow: 0 1px 3px 0 #e6ebf1;
            -webkit-transition: box-shadow 150ms ease;
            transition: box-shadow 150ms ease;
        }

        .StripeElement--focus {
            box-shadow: 0 1px 3px 0 #cfd7df;
        }

        .StripeElement--invalid {
            border-color: #fa755a;
        }

        .StripeElement--webkit-autofill {
            background-color: #fefde5 !important;
        }
    </style>

    <section id="dashboard" class="section__padding">
        <div class="container add-fund pb-50">
            <div class="row justify-content-center">
                <div class="col-md-8 col-xl-8 col-sm-12">
                    <h3>@lang('Subscribe with '.optional($deposit->gateway)->name)</h3>
                    <div class="card secbg br-4 custom-card-payment">
                        <div class="card-body br-4">
                            <div class="row align-items-center">
                                <div class="col-md-3">
                                    <img
                                        src="{{getFile($gateway->driver, $gateway->image)}}"
                                        class="card-img-top gateway-img br-4" alt="{{config('basic.site_title')}}">
                                </div>
                                <div class="col-md-9">
                                    <h4>@lang('Please Pay') {{getAmount($deposit->amount)}} {{$deposit->payment_method_currency}}</h4>
                                    <form action="{{route('user.subscription.process',$deposit->trx_id)}}" method="POST"
                                          id="payment-form">
                                        @csrf
                                        <input type="hidden" name="token" class="token" value="">
                                        <div class="form-group">
                                            <label for="email">@lang('Email')</label>
                                            <input type="email" name="email" class="form-control mb-2" id="email" required>
                                        </div>
                                        <div class="form-group">
                                            <div id="card-element">

                                            </div>
                                        </div>
                                        <button type="submit" class="cmn-btn btn-custom mt-3">@lang('Subscribe')</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    @push('script')
        <script>
            const stripe = Stripe('{{ $gateway->parameters->publishable_key }}');
            const elements = stripe.elements();
            const cardElement = elements.create('card');
            cardElement.mount('#card-element');

            var form = document.getElementById('payment-form');
            form.addEventListener('submit', function (event) {
                event.preventDefault();

                stripe.createToken(cardElement).then(function (result) {
                    if (result.error) {
                        // Handle token creation error
                    } else {
                        // Token created successfully
                        var token = result.token;
                        // Send the token to your server to create a customer
                        $('.token').val(token.id)
                        $('#payment-form').submit();
                    }
                });
            });

        </script>
    @endpush

@endsection




