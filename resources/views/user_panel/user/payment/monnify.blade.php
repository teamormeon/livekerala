@extends(template().'layouts.app')
@section('title')
    {{ __('Pay with ').__(optional($deposit->gateway)->name) }}
@endsection

@section('content')
    <div class="payment-section-div">
        <div class="content container">
            <div class="main row">
                <div class="col-xl-6 col-lg-8 col-md-10 mx-auto">
                    <h3>@lang('Pay with '.optional($deposit->gateway)->name)</h3>
                    <div class="payment-box">
                        <div class="img-box">
                            <img src="{{getFile(optional($deposit->gateway)->driver,optional($deposit->gateway)->image)}}" class="card-img-top gateway-img rounded-2 img-fluid">
                        </div>
                        <div class="text-box">
                            <h4 class="pb-2">@lang('Please Pay') {{getAmount($deposit->payable_amount)}} {{$deposit->payment_method_currency}}</h4>
                            <button type="button" class="cmn-btn btn-custom py-2" onclick="payWithMonnify()">@lang('Pay Now') <span></span> </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection
@push('script')
	<script type="text/javascript" src="//sdk.monnify.com/plugin/monnify.js"></script>
	<script type="text/javascript">
		'use strict';
            function payWithMonnify() {
                MonnifySDK.initialize({
                    amount: {{ $data->amount }},
                    currency: "{{ $data->currency }}",
                    reference: "{{ $data->ref }}",
                    customerName: "{{$data->customer_name }}",
                    customerEmail: "{{$data->customer_email }}",
                    customerMobileNumber: "{{ $data->customer_phone }}",
                    apiKey: "{{ $data->api_key }}",
                    contractCode: "{{ $data->contract_code }}",
                    paymentDescription: "{{ $data->description }}",
                    isTestMode: true,
                    onComplete: function (response) {
                        if (response.paymentReference) {
                            window.location.href = '{{ route('ipn', ['monnify', $data->ref]) }}';
                        } else {
                            window.location.href = '{{ route('failed') }}';
                        }
                    },
                    onClose: function (data) {
                    }
                });
            }
	</script>
@endpush
