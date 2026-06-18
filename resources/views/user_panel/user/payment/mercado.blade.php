@extends(template().'layouts.app')
@section('title')
	{{ __('Pay with ').__(optional($deposit->gateway)->name) }}
@endsection
@section('section')
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
                            <form
                                action="{{ route('ipn', [optional($deposit->gateway)->code ?? 'mercadopago', $deposit->utr]) }}"
                                method="POST">
                                <script src="https://www.mercadopago.com.co/integrations/v1/web-payment-checkout.js"
                                        data-preference-id="{{ $data->preference }}">
                                </script>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
