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
                            <form action="{{$data->url}}" method="{{$data->method}}">
                                <script src="{{$data->checkout_js}}"
                                        @foreach($data->val as $key=>$value)
                                            data-{{$key}}="{{$value}}"
                                    @endforeach >
                                </script>
                                <input type="hidden" custom="{{$data->custom}}" name="hidden">
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
	<script>
		$(document).ready(function () {
			$('input[type="submit"]').addClass("cmn-btn btn-custom border-0");
		})
	</script>
@endpush
