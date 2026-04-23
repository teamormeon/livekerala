@extends(template().'layouts.app')
@section('title')
	{{ 'Pay with '.$gateway->name ?? '' }}
@endsection
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
									<div class="form-group">
										<label for="email">@lang('Email')</label>
										<input type="email" name="email" class="form-control mb-2" id="email" required>
									</div>
									<button type="submit" class="cmn-btn btn-custom mt-3">@lang('Subscribe')</button>
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


