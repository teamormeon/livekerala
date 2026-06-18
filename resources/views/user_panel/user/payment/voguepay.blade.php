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
                            <button type="button" class="cmn-btn btn-custom py-2" id="btn-confirm">@lang('Pay Now') <span></span> </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="https://pay.voguepay.com/js/voguepay.js"></script>
	<script>
		closedFunction = function () {
		}
		successFunction = function (transaction_id) {
			let txref = "{{ $data->merchant_ref }}";
			window.location.href = '{{ url('payment/voguepay') }}/' + txref + '/' + transaction_id;
		}
		failedFunction = function (transaction_id) {
			window.location.href = '{{ route('failed') }}';
		}

		function pay(item, price) {
			Voguepay.init({
				v_merchant_id: "{{ $data->v_merchant_id }}",
				total: price,
				notify_url: "{{ $data->notify_url }}",
				cur: "{{ $data->cur }}",
				merchant_ref: "{{ $data->merchant_ref }}",
				memo: "{{ $data->memo }}",
				developer_code: '5af93ca2913fd',
				custom: "{{ $data->custom }}",
                customer: {
                    name: "{{ $data->customer_name }}",
                    address: "{{ $data->customer_address }}",
                    email: "{{ $data->customer_email }}"
                },
				closed: closedFunction,
				success: successFunction,
				failed: failedFunction
			});
		}
		$(document).ready(function () {
			$(document).on('click', '#btn-confirm', function (e) {
				e.preventDefault();
				pay('Buy', {{ $data->Buy }});
			});
		});
	</script>
@endpush


