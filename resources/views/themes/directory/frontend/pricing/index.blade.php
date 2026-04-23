@extends(template().'layouts.app')
@section('title',trans('Pricing plan'))

@section('content')
    @if (count($packages) > 0)
        <section class="pricing-section">
            <div class="container">
                @if(isset($singleContent))
                    <div class="row">
                        <div class="section-header text-center mb-50">
                            <div class="section-subtitle">@lang($singleContent['description']->title)</div>
                            <h2>@lang($singleContent['description']->sub_title)</h2>
                            <p class="cmn-para-text mx-auto">@lang($singleContent['description']->description)</p>
                        </div>
                    </div>
                @endif

                <div class="row g-2 justify-content-center">
                    @foreach ($packages as $item)
                        <div class="col-lg-4 col-md-6">
                            <div class="pricing-box">
                                <div class="section-header">
                                    <div class="title-area">
                                        <div class="icon-area"><img src="{{ getFile($item->driver, $item->image) }}" alt="image" width="64"/></div>
                                        <h4 class="title">@lang(optional($item->details)->title)</h4>
                                    </div>
                                </div>

                                <div class="section-body">
                                    <div class="title"><span class="price">{{ $item->price == null ? currencyPosition(0) : currencyPosition($item->price) }}</span></div>
                                    <div class="btn-area mb-30">
                                        @if($item->price == null)
                                            <button type="button" class="cmn-btn w-100 choosePlan {{ $item->isFreePurchase() == 'true' ? 'bg-danger' : '' }}"
                                                    {{ $item->isFreePurchase() == 'true' ? 'disabled' : '' }}
                                                    data-route="{{ route('user.pricing.plan.payment',$item->id) }}"
                                                    data-price="{{($item->price == null ? 0 : $item->price)}}"
                                                    data-plan="{{optional($item->details)->title}}"
                                                    data-listing="{{ $item->no_of_listing }}"
                                                    data-expiretime="{{ $item->expiry_time }}"
                                                    data-expiretype="{{ $item->expiry_time_type }}">
                                                {{ $item->isFreePurchase() == 'true' ? __('Purchased') : __('Try Free') }}
                                            </button>
                                        @else
                                            <button type="button" class="cmn-btn w-100 choosePlan"
                                                    data-route="{{ route('user.pricing.plan.payment',$item->id) }}"
                                                    data-price="{{$item->price}}"
                                                    data-plan="{{optional($item->details)->title}}"
                                                    data-listing="{{ $item->no_of_listing }}"
                                                    data-expiretime="{{ $item->expiry_time }}"
                                                    data-expiretype="{{ $item->expiry_time_type }}">
                                                @lang('choose plan')
                                            </button>
                                        @endif
                                    </div>
                                    <ul class="pricing-feature">
                                        <li>
                                            <i class="{{ $item->expiry_time < 0 ? 'fa-regular fa-circle-xmark cross' : 'fa-regular fa-circle-check' }}"></i>
                                            {{ $item->expiry_time != null ? $item->expiry_time.' '.$item->expiry_time_type : 'Unlimited' }}
                                            @lang('Package Expiration')
                                        </li>
                                        <li>
                                            <i class="fa-regular fa-circle-check"></i> {{ $item->no_of_listing == null ? 'Unlimited' : $item->no_of_listing }}
                                            @lang('Listing Allowed')
                                        </li>
                                        <li>
                                            <i class="{{ $item->is_image == 0 ? 'fa-regular fa-circle-xmark cross' : 'fa-regular fa-circle-check' }}"></i>
                                            {{ $item->is_image == 0 ? trans('No image Allowed per listing') : ($item->is_image == 1 && $item->no_of_img_per_listing == null ? trans('Unlimited image Allowed per listing') : $item->no_of_img_per_listing.trans(' image Allowed per listing')) }}
                                        </li>
                                        <li>
                                            <i class="fa-regular fa-circle-check"></i>
                                            {{ $item->no_of_categories_per_listing == 0 ? trans('No category allowed per listing') : $item->no_of_categories_per_listing.trans(' category allowed per listing') }}
                                        </li>
                                        <li>
                                            <i class="{{ $item->is_product == 1 ? 'fa-regular fa-circle-check' : 'fa-regular fa-circle-xmark cross' }}"></i>
                                            {{ $item->is_product == 0 ? trans('No product Allowed per listing') : ($item->is_product == 1 && $item->no_of_product == null ? trans('Unlimited product Allowed per listing') : $item->no_of_product.trans(' product Allowed per listing')) }}
                                        </li>
                                        <li>
                                            <i class="{{ $item->is_product == 1 ? 'fa-regular fa-circle-check' : 'fa-regular fa-circle-xmark cross' }}"></i>
                                            {{ $item->is_product == 0 ? trans('No image Allowed per product') : ($item->is_product == 1 && $item->no_of_img_per_product == null ? trans('Unlimited image Allowed per product') : $item->no_of_img_per_product.trans(' image Allowed per product')) }}
                                        </li>

                                        <li>
                                            <i class="{{ $item->is_video == 0 ? 'fa-regular fa-circle-xmark cross' : 'fa-regular fa-circle-check' }}"></i>
                                            {{ $item->is_video == 0 ? trans('No video Allowed per listing') : trans('video Allowed per listing') }}
                                        </li>

                                        <li>
                                            <i class="{{ $item->is_amenities == 1 ? 'fa-regular fa-circle-check' : 'fa-regular fa-circle-xmark cross' }}"></i>
                                            {{ $item->is_amenities == 0 ? trans('No amenity Allowed per listing') : ($item->is_amenities == 1 && $item->no_of_amenities_per_listing == null ? trans('Unlimited amenity Allowed per listing') : $item->no_of_amenities_per_listing.trans(' amenity Allowed per listing')) }}
                                        </li>
                                        <li>
                                            <i class="{{ $item->is_business_hour == 0 ? 'fa-regular fa-circle-xmark cross' : 'fa-regular fa-circle-check' }}"></i>
                                            {{ $item->is_business_hour == 0 ? trans('No business hour Allowed per listing') : trans('Business hour Allowed per listing') }}
                                        </li>
                                        <li>
                                            <i class="{{ $item->seo == 0 ? 'fa-regular fa-circle-xmark cross' : 'fa-regular fa-circle-check' }}"></i>
                                            {{ $item->seo == 0 ? trans('No seo Allowed per listing') : trans('SEO Allowed per listing') }}
                                        </li>
                                        <li>
                                            <i class="{{ $item->is_messenger == 0 ? 'fa-regular fa-circle-xmark cross' : 'fa-regular fa-circle-check' }}"></i>
                                            {{ $item->is_messenger == 0 ? trans('Messenger chat SDK not Available') : trans('Messenger chat SDK Available') }}
                                        </li>
                                        <li>
                                            <i class="{{ $item->is_whatsapp == 0 ? 'fa-regular fa-circle-xmark cross' : 'fa-regular fa-circle-check' }}"></i>
                                            {{ $item->is_whatsapp == 0 ? trans('Whatsapp chat SDK not Available') : trans('Whatsapp chat SDK Available') }}
                                        </li>
                                        <li>
                                            <i class="{{ $item->is_renew == 0 ? 'fa-regular fa-circle-xmark cross' : 'fa-regular fa-circle-check' }}"></i>
                                            {{ $item->is_renew == 0 ? trans('Package Renew not Available') : trans('Package Renew Available') }}
                                        </li>
                                        <li>
                                            <i class="{{ $item->is_create_from == 0 ? 'fa-regular fa-circle-xmark cross' : 'fa-regular fa-circle-check' }}"></i>
                                            {{ $item->is_create_from == 0 ? trans('Dynamic Form not Available') : trans('Dynamic Form Available') }}
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @else
        <div class="custom-not-found">
            <img src="{{ asset(template(true).'img/error/error.png') }}" alt="image" class="img-fluid">
        </div>
    @endif

    <form class="plan-modal-form purchasePackageForm" id="plan-modal-form" action="" method="get" enctype="multipart/form-data">
        <div class="modal fade" id="choosePlanModal" tabindex="-1"
             aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title plan-name"
                            id="exampleModalLabel">@lang('Purchase Plan Information')</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                    </div>
                    <div class="modal-body ">
                        <ul class="list-group">
                            <li class="list-group-item border-0 d-flex justify-content-between align-items-start">
                                <div class="ms-2 me-auto">@lang('Price')</div>
                                <span class="plan-price"> </span>
                            </li>
                            <li class="list-group-item border-0 d-flex justify-content-between align-items-start">
                                <div class="ms-2 me-auto">@lang('No. Of Listing')</div>
                                <span class="plan-listing"></span>
                            </li>
                            <li class="list-group-item border-0 d-flex justify-content-between align-items-start">
                                <div class="ms-2 me-auto">@lang('Validity')</div>
                                <span class="package-validity"></span>
                            </li>
                        </ul>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="cmn-btn w-100 purchasePackageSubmitBtn"></button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('script')
    <script>
        "use strict";
        (function ($) {
            $(document).on('click', '.choosePlan', function () {
                var planModal = new bootstrap.Modal(document.getElementById('choosePlanModal'))
                planModal.show()
                let dataRoute = $(this).data('route');
                let plan_name = $(this).data('plan');
                let symbol = "{{ trans(basicControl()->currency_symbol) }}";
                let price = $(this).data('price');
                let listing = $(this).data('listing');
                let plan_expire_time = $(this).data('expiretime');
                let plan_expire_type = $(this).data('expiretype');
                let packageValidity = plan_expire_time + ' ' + plan_expire_type;
                if (price == 0){
                    $('.purchasePackageSubmitBtn').text('Start Free Trial')
                }else{
                    $('.purchasePackageSubmitBtn').text('Purchase Now')
                }
                $('.plan-name').text(plan_name);
                $('.plan-price').text(`${symbol}${price}`);
                $('.purchasePackageForm').attr('action', dataRoute);
                if (listing == '') {
                    $('.plan-listing').text(`@lang('Unlimited')`);
                } else {
                    $('.plan-listing').text(`${listing}`);
                }
                if (plan_expire_time == '') {
                    $('.package-validity').text(`@lang('Unlimited')`);
                } else {
                    $('.package-validity').text(`${packageValidity}`);
                }
            });
        })(jQuery);
    </script>
@endpush
