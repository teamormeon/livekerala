@extends(template().'layouts.app')
@section('title',trans('Pricing plan'))


@section('content')
    @if (count($packages) > 0)
        <section class="pricing-section">
            <div class="container">
                @if(isset($singleContent))
                    <div class="row">
                        <div class="col">
                            <div class="header-text text-center mb-5">
                                <h2>@lang($singleContent['description']->title)</h2>
                                <p class="mx-auto">
                                    @lang(strip_tags($singleContent['description']->description))
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="row gy-3 g-md-5">
                    @foreach ($packages as $item)
                        <div class="col-lg-4 col-md-6">
                            <div class="box">
                                <div class="icon-box">
                                    <img src="{{ getFile($item->driver, $item->image) }}" alt="image" width="64"/>
                                </div>

                                <div class="text-box">
                                    <h5>@lang(optional($item->details)->title)</h5>
                                    <h3>{{ $item->price == null ? currencyPosition(0) : currencyPosition($item->price) }}</h3>
                                    <ul>
                                        <li>
                                            <span><i class="{{ $item->expiry_time < 0 ? 'fal fa-times-circle text-danger' : 'fal fa-check-circle text-primary' }}"></i>@lang('Package expiry')</span>
                                            <span class="float-end">{{ $item->expiry_time != null ? $item->expiry_time.' '.$item->expiry_time_type : 'Unlimited' }}</span>
                                        </li>

                                        <li>
                                            <span><i class="fal fa-check-circle text-primary"></i>@lang('No of Listing')</span>
                                            <span class="float-end">{{ $item->no_of_listing == null ? 'Unlimited' : $item->no_of_listing }}</span>
                                        </li>

                                        <li>
                                            <span><i class="{{ $item->is_image == 0 ? 'fal fa-times-circle text-danger' : 'fal fa-check-circle text-primary' }}"></i>@lang('No of images per listing')</span>
                                            <span class="float-end">{{ $item->is_image == 0 ? 'No' : ($item->is_image == 1 && $item->no_of_img_per_listing == null ? 'Unlimited' : $item->no_of_img_per_listing) }}</span>
                                        </li>

                                        <li>
                                            <span><i class="fal fa-check-circle text-primary"></i>@lang('No of categories per listing')</span>
                                            <span class="float-end"> {{ $item->no_of_categories_per_listing ?? 0 }} </span>
                                        </li>

                                        <li>
                                            <span><i class="{{ $item->is_product == 1 ? 'fal fa-check-circle text-primary' : 'fal fa-times-circle text-danger' }}"></i>@lang('Products')</span>
                                            <span class="float-end">{{ $item->is_product == 1 ? 'Yes' : 'No' }}</span>
                                        </li>

                                        <li>
                                            <span><i class="{{ $item->is_product == 0 ? 'fal fa-times-circle text-danger' : 'fal fa-check-circle text-primary' }}"></i>@lang('No of Product')</span>
                                            <span class="float-end">
                                               <span class="float-end"> {{ $item->is_product == 0 ? 'No' : ($item->is_product == 1 && $item->no_of_product == null ? 'Unlimited' : $item->no_of_product) }} </span>
                                            </span>
                                        </li>

                                        <li>
                                             <span><i class="{{ $item->is_product == 0 ? 'fal fa-times-circle text-danger' : 'fal fa-check-circle text-primary' }}"></i>@lang('No of images per product')</span>
                                            <span class="float-end">
                                                <span class="float-end">{{ $item->is_product == 0 ? 'No' : ($item->is_product == 1 && $item->no_of_img_per_product == null ? 'Unlimited' : $item->no_of_img_per_product) }}</span>
                                            </span>
                                        </li>

                                        <li>
                                            <span><i class="{{ $item->is_video == 0 ? 'fal fa-times-circle text-danger' : 'fal fa-check-circle text-primary' }}"></i>@lang('Video')</span>
                                            <span class="float-end">{{ $item->is_video == 1 ? 'Yes' : 'No' }}</span>
                                        </li>

                                        <li>
                                            <span><i class="{{ $item->is_amenities == 0 ? 'fal fa-times-circle text-danger' : 'fal fa-check-circle text-primary' }}"></i>@lang('Amenities')</span>
                                            <span class="float-end">{{ $item->is_amenities == 1 ? 'Yes' : 'No' }}</span>
                                        </li>

                                        <li>
                                            <span><i class="{{ $item->is_amenities == 0 ? 'fal fa-times-circle text-danger' : 'fal fa-check-circle text-primary' }}"></i>@lang('No of amenities per listing')</span>
                                            <span class="float-end"> {{ $item->is_amenities == 0 ? 'No' : ($item->is_amenities == 1 && $item->no_of_amenities_per_listing == null ? 'Unlimited' : $item->no_of_amenities_per_listing) }} </span>
                                        </li>

                                        <li>
                                            <span> <i class="{{ $item->is_business_hour == 0 ? 'fal fa-times-circle text-danger' : 'fal fa-check-circle text-primary' }}"></i>@lang('Business Hour')</span>
                                            <span class="float-end">{{ $item->is_business_hour == 1 ? 'Yes' : 'No' }}</span>
                                        </li>

                                        <li>
                                            <span> <i class="{{ $item->seo == 0 ? 'fal fa-times-circle text-danger' : 'fal fa-check-circle text-primary' }}"></i>@lang('SEO')</span>
                                            <span class="float-end">{{ $item->seo == 1 ? 'Yes' : 'No' }}</span>
                                        </li>

                                        <li>
                                            <span> <i class="{{ $item->is_messenger == 0 ? 'fal fa-times-circle text-danger' : 'fal fa-check-circle text-primary' }}"></i>@lang('Messenger chat SDK')</span>
                                            <span class="float-end">{{ $item->is_messenger == 1 ? 'Yes' : 'No' }}</span>
                                        </li>

                                        <li>
                                            <span> <i class="{{ $item->is_whatsapp == 0 ? 'fal fa-times-circle text-danger' : 'fal fa-check-circle text-primary' }}"></i>@lang('Whatsapp chat SDK')</span>
                                            <span class="float-end">{{ $item->is_whatsapp == 1 ? 'Yes' : 'No' }}</span>
                                        </li>

                                        <li>
                                            <span> <i class="{{ $item->is_renew == 0 ? 'fal fa-times-circle text-danger' : 'fal fa-check-circle text-primary' }}"></i>@lang('Package Renew')</span>
                                            <span class="float-end">{{ $item->is_renew == 1 ? 'Yes' : 'No' }}</span>
                                        </li>

                                        <li>
                                            <span> <i class="{{ $item->is_create_from == 0 ? 'fal fa-times-circle text-danger' : 'fal fa-check-circle text-primary' }}"></i>@lang('Dynamic Form')</span>
                                            <span class="float-end">{{ $item->is_create_from == 1 ? 'Yes' : 'No' }}</span>
                                        </li>
                                    </ul>

                                    @if($item->price == null)
                                        <button type="button" class="btn btn-primary btn-custom choosePlan {{ $item->isFreePurchase() == 'true' ? 'bg-danger disabled' : '' }}"
                                                data-route="{{ route('user.pricing.plan.payment',$item->id) }}"
                                                data-price="{{($item->price == null ? 0 : $item->price)}}"
                                                data-plan="{{optional($item->details)->title}}"
                                                data-listing="{{ $item->no_of_listing }}"
                                                data-expiretime="{{ $item->expiry_time }}"
                                                data-expiretype="{{ $item->expiry_time_type }}">
                                            {{ $item->isFreePurchase() == 'true' ? __('Purchased') : __('Try Free') }}
                                        </button>
                                    @else
                                        <button type="button" class="btn btn-primary btn-custom choosePlan"
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
                            </div>
                        </div>
                    @endforeach

                    @push('frontend_modal')
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
                                            <button type="submit" class="btn btn-primary btn-custom d-block w-100 purchasePackageSubmitBtn"></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    @endpush
                </div>
            </div>
        </section>
    @else
        <div class="custom-not-found2">
            <img src="{{ asset(template(true).'img/no_data_found.png') }}" alt="image"
                 class="img-fluid">
        </div>
    @endif

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
