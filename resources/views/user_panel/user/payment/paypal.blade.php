@extends(template().'layouts.app')
@section('title', __('Pay with PayPal'))

@section('content')
        <div class="row">
            <div class="col-3"></div>
            <div class="col-7">
                <div class="payment-section-div">
                    <h3>@lang('Pay with '.optional($deposit->gateway)->name)</h3>
                    <div class="order-tracking paypal-payment px-3 mt-3">
                        <div id="paypal-button-container"></div>
                    </div>
                </div>
            </div>
            <div class="col-2"></div>
        </div>
@endsection

@push('script')
    <script src="https://www.paypal.com/sdk/js?client-id={{ $data->cleint_id }}">
    </script>
    <script>
        paypal.Buttons({
            createOrder: function (data, actions) {
                return actions.order.create({
                    purchase_units: [
                        {
                            description: "{{ $data->description }}",
                            custom_id: "{{ $data->custom_id }}",
                            amount: {
                                currency_code: "{{ $data->currency }}",
                                value: "{{ $data->amount }}",
                                breakdown: {
                                    item_total: {
                                        currency_code: "{{ $data->currency }}",
                                        value: "{{ $data->amount }}"
                                    }
                                }
                            }
                        }
                    ]
                });
            },
            onApprove: function (data, actions) {
                return actions.order.capture().then(function (details) {
                    var trx = "{{ $data->custom_id }}";
                    window.location = '{{ url('payment/paypal')}}/' + trx + '/' + details.id
                });
            }
        }).render('#paypal-button-container');
    </script>
@endpush
