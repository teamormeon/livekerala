@extends(template().'layouts.app')
@section('title')
	{{ 'Pay with '.$gateway->name ?? '' }}
@endsection
@push('css-lib')

@endpush
@section('content')
	<section class="feature-section">
		<div class="container">
			<div class="row justify-content-center">
				<div class="col-md-6">
					<div class="feature-box">
						<div class="row justify-content-center">
							<div class="col-md-3">
								<img
									src="{{getFile($gateway->driver,$gateway->image)}}"
									class="card-img-top gateway-img">
							</div>
							<div class="col-md-6" id="paypal-button-container">

							</div>
							<form action="{{route('user.subscription.process',$deposit->trx_id)}}" method="POST"
								  id="payment-form" class="d-none">
								@csrf
								<input type="hidden" class="token" name="token" value="">
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
@endsection
@push('extra-js')
	<script
		src="https://www.paypal.com/sdk/js?client-id={{$gateway->parameters->cleint_id}}&vault=true&intent=subscription"></script>
	<script>
		'use strict'
		var planId = "{{optional(optional($deposit->depositable)->subscriptionPlan)->gateway_plan_id->paypal??null}}"
		paypal.Buttons({
			createSubscription: function (data, actions) {
				return actions.subscription.create({
					'plan_id': planId // Creates the subscription
				});
			},
			onApprove: function (data, actions) {
				$('.token').val(data.subscriptionID)
				$('#payment-form').submit();
			}
		}).render('#paypal-button-container'); // Renders the PayPal button
	</script>
@endpush


