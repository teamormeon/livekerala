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
							<div class="col-md-6">
								<form action="{{route('user.subscription.process',$deposit->trx_id)}}" method="POST"
									  id="payment-form">
									@csrf
									<button type="submit" class="cmn-btn btn-custom mt-3"
											id="rzp-button1">@lang('Subscribe')</button>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
@endsection
@push('extra-js')

@endpush


