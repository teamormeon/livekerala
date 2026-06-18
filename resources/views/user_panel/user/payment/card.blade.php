@extends(template().'layouts.app')
@section('title')
    {{ __('Pay with ').__(optional($deposit->gateway)->name) }}
@endsection
@section('content')
    @push('style')
        <link href="{{ asset('assets/admin/css/card-js.min.css') }}" rel="stylesheet" type="text/css"/>
        <style>
            .card-js .icon {
                top: 5px;
            }
        </style>
    @endpush

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
                            <form class="form-horizontal" id="example-form"
                                  action="{{ route('ipn', [optional($deposit->gateway)->code ?? '', $deposit->transaction]) }}"
                                  method="post">
                                @csrf
                                <input type="hidden" name="trx_id" value="{{ $deposit->trx_id }}">
                                <div class="card-js form-group --payment-card">
                                    <input class="card-number form-control"
                                           name="card_number"
                                           placeholder="@lang('Enter your card number')"
                                           autocomplete="off"
                                           required>
                                    <input class="name form-control"
                                           id="the-card-name-id"
                                           name="card_name"
                                           placeholder="@lang('Enter the name on your card')"
                                           autocomplete="off"
                                           required>
                                    <input class="expiry form-control"
                                           autocomplete="off"
                                           required>
                                    <input class="expiry-month" name="expiry_month">
                                    <input class="expiry-year" name="expiry_year">
                                    <input class="cvc form-control"
                                           name="card_cvc"
                                           autocomplete="off"
                                           required>
                                </div>
                                <button type="submit" class="cmn-btn btn-custom mt-3 py-2 w-100">@lang('Submit') <span></span></button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('script')
        <script src="{{ asset('assets/admin/js/card-js.min.js') }}"></script>
    @endpush

@endsection
