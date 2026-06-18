@extends('user_panel.layouts.user')
@section('title',trans('All Packages'))

@push('style')
    <link rel="stylesheet" href="{{ asset('assets/global/css/bootstrap-datepicker.css') }}"/>
    <link rel="stylesheet" href="{{ asset('assets/global/css/select2.min.css') }}"/>
@endpush
@section('content')
    <div class="container-fluid">
        <div class="main row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="mb-0">@lang('My Packages')</h3>
                </div>

                <!-- table -->
                <div class="table-parent table-responsive">
                    <div class="table-heading py-3 d-flex justify-content-between align-items-center">
                        <h4>@lang('Packages')</h4>
                        <button type="button" class="cmn-btn customButton" data-bs-toggle="offcanvas" data-bs-target="#offcanvasExample"
                                aria-controls="offcanvasExample">@lang('Filter')<i class="fal fa-filter"></i>
                        </button>
                    </div>
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th scope="col">@lang('Package')</th>
                            <th scope="col">@lang('No. of listing')</th>
                            <th scope="col">@lang('Validity')</th>
                            <th scope="col">@lang('Subscription Type')</th>
                            <th scope="col">@lang('Status')</th>
                            <th scope="col">@lang('Purchased Date')</th>
                            <th scope="col">@lang('Expired Date')</th>
                            <th scope="col" class="text-end">@lang('Action')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse ($my_packages as $key => $item)
                            <tr>
                                <td data-label="Package">
                                    @lang(optional(optional($item->get_package)->details)->title)
                                </td>
                                <td data-label="No. of listing">
                                    @if ($item->no_of_listing == null)
                                        <span class="badge rounded-pill bg-primary">@lang('Unlimited')</span>
                                    @else
                                        <span class="badge rounded-pill bg-light text-secondary">{{ $item->no_of_listing }}</span>
                                    @endif
                                </td>

                                <td data-label="Validity">
                                    @if (\Carbon\Carbon::now()->format('Y-m-d') <= \Carbon\Carbon::parse($item->expire_date))
                                        <span class="badge rounded-pill bg-success">@lang('Active')</span>
                                    @elseif ($item->expire_date == null)
                                        <span class="badge rounded-pill bg-success">@lang('Active')</span>
                                    @else
                                        <span class="badge rounded-pill bg-danger">@lang('Expired')</span>
                                    @endif
                                </td>
                                <td data-label="@lang('Subscription Type')">
                                    @if($item->api_subscription_id)
                                        <span
                                            class="badge rounded-pill bg-info">@lang('Automatic')</span>
                                    @else
                                        <span
                                            class="badge rounded-pill bg-warning">@lang('Manual')</span>
                                    @endif
                                </td>

                                <td data-label="Status">
                                    @if ($item->status == 0)
                                        <span class="badge rounded-pill bg-warning">@lang('Pending')</span>
                                    @elseif($item->status == 1)
                                        <span class="badge rounded-pill bg-success">@lang('Approved')</span>
                                    @else
                                        <span class="badge rounded-pill bg-danger">@lang('Cancelled')</span>
                                    @endif
                                </td>

                                <td data-label="Purchased Date">
                                    {{ dateTime($item->purchase_date) }}
                                </td>

                                <td data-label="Expired Date">
                                    @if ($item->expire_date == null)
                                        <span class="badge rounded-pill bg-primary">@lang('Unlimited')</span>
                                    @else
                                        {{ dateTime($item->expire_date) }}
                                    @endif
                                    <p class="expire__date" data-date="{{ \Illuminate\Support\Carbon::parse($item->expire_date)->format('Y-m-d') }}" data-package="{{ $item->id }}"></p>
                                </td>

                                <td data-label="@lang('Action')">
                                    <div class="dropdown-btns">
                                        <button type="button" class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="far fa-ellipsis-v"></i>
                                        </button>

                                        <ul class="dropdown-menu dropdown-menu-end">
                                            @if($item->price != null)
                                                <li>
                                                    <a href="{{ route('user.paymentHistory', $item->id) }}" class="btn  dropdown-item">
                                                        <i class="fal fa-sack-dollar"></i> @lang('Payment History')</a>
                                                </li>
                                            @endif

                                            @if($item->api_subscription_id)
                                                <li>
                                                    <a href="javascript:void(0)" class="btn dropdown-item cancelSubscriptionBtn"
                                                       data-bs-toggle="modal"
                                                       data-bs-target="#cancelSubscriptionModal"
                                                       data-route="{{route('user.subscription.cancel',$item->id)}}">
                                                        <i class="fal fa-times"></i> @lang('Cancel Subscription')</a>
                                                </li>
                                            @endif

                                            @if($item->expire_date != null && $item->status != 0 && $item->is_renew == 1)
                                                <li>
                                                    <a href="javascript:void(0)" class="btn  notiflix-confirm renewPackage dropdown-item"
                                                       data-price="{{(optional($item->get_package)->price == null ? 0 : optional($item->get_package)->price)}}"
                                                       data-plan="{{ optional(optional($item->get_package)->details)->title}}"
                                                       data-route="{{route('user.pricing.plan.payment', ['id'=>$item->package_id, 'type'=>'renew', 'purchase_id' => $item->id])}}"
                                                       data-listing="{{ $item->no_of_listing }}" data-expiretime="{{ optional($item->get_package)->expiry_time }}"
                                                       data-expiretype="{{ optional($item->get_package)->expiry_time_type }}"
                                                       data-purchasepackageexpiredate="{{ \Illuminate\Support\Carbon::parse($item->expire_date)->format('Y-m-d') }}">
                                                        <i class="fal fa-wind-turbine"></i> @lang('Renew Package')
                                                    </a>
                                                </li>
                                            @endif

                                            @if(($item->no_of_listing > 0 || $item->no_of_listing == null) && ($item->expire_date == null ||  \Carbon\Carbon::now() <= \Carbon\Carbon::parse($item->expire_date)) && ($item->status == 1))
                                                <li>
                                                    <a href="{{ route('user.addListing', $item->id) }}" class="btn  dropdown-item"> <i class="fal fa-box-open"></i> @lang('Add Listing')</a>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <td class="text-center" colspan="100%">
                                <img class="noDataImg" src="{{ asset('assets/admin/img/oc-error.svg') }}" alt="image">
                                <p class="mt-3">@lang('No data available')</p>
                            </td>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $my_packages->appends($_GET)->links('user_panel.partials.pagination') }}
            </div>
        </div>
    </div>

    @push('loadModal')
        <div class="modal fade" id="delete-modal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-top modal-md">
                <div class="modal-content">
                    <div class="modal-header modal-primary modal-header-custom">
                        <h4 class="modal-title" id="editModalLabel">@lang('Delete Confirmation')</h4>
                        <button type="button" class="close-btn" data-bs-dismiss="modal" aria-label="Close">
                            <i class="fal fa-times"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        @lang('Are you sure delete?')
                    </div>
                    <hr>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('Close')</button>
                        <form action="" method="post" class="deleteRoute">
                            @csrf
                            @method('delete')
                            <button type="submit" class="btn btn-primary addCreateListingRoute">@lang('Confirm')</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="cancelSubscriptionModal" tabindex="-1" aria-labelledby="cancelSubscriptionModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-top modal-md">
                <div class="modal-content">
                    <div class="modal-header modal-primary modal-header-custom">
                        <h4 class="modal-title" id="cancelSubscriptionModalLabel">@lang('Subscription Cancel Confirmation')</h4>
                        <button type="button" class="close-btn" data-bs-dismiss="modal" aria-label="Close">
                            <i class="fal fa-times"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        @lang('Are you want to cancel this subscription?')
                    </div>
                    <hr>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('Close')</button>
                        <form action="" method="post" class="cancelRoute">
                            @csrf
                            <button type="submit" class="btn btn-primary">@lang('Confirm')</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="renewPackageModal" tabindex="-1" aria-labelledby="addListingmodal"
             aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header modal-header-custom">
                        <h4 class="modal-title text-white" id="editModalLabel">@lang('Package Renew Information')</h4>
                        <button type="button" class="close-btn" data-bs-dismiss="modal" aria-label="Close">
                            <i class="fal fa-times"></i>
                        </button>
                    </div>
                    <form action="" method="get" enctype="multipart/form-data" class="renewPackageForm">
                        <div class="modal-body">
                            <div class="card">
                                <div class="card-header payment-method-details plan-name">
                                </div>
                                <div class="card-body">
                                    <div class="estimation-box">
                                        <div class="details_list">
                                            <ul>
                                                <li class="d-flex justify-content-between"><span>@lang('Price')</span><span class="plan-price"></span></li>
                                                <li class="d-flex justify-content-between"><span>@lang('No. Of Listing')</span> <span class="plan-listing"></span></li>
                                                <li class="d-flex justify-content-between"><span>@lang('Validity')</span> <span class="package-validity"></span></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('Close')</button>
                            <button type="submit" class="rounded-3 text-capitalize btn-custom addCreateListingRoute">@lang('Confirm')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endpush


    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasExample" aria-labelledby="offcanvasExampleLabel">
        <div class="offcanvas-header">
            <h4 class="offcanvas-title" id="offcanvasExampleLabel">@lang('Filter Package')</h4>
            <button type="button" class="cmn-btn-close" data-bs-dismiss="offcanvas" aria-label="Close">
                <i class="fal fa-arrow-right"></i>
            </button>
        </div>
        <div class="offcanvas-body">
            <form action="" method="get" enctype="multipart/form-data">
                <div class="row g-4">
                    <div class="input-box">
                        <label for="search" class="form-label">@lang('Search')</label>
                        <input type="text" name="name" value="{{ old('name',request()->name) }}" class="form-control" placeholder="@lang('Search')"/>
                    </div>
                    <div class="input-box">
                        <label for="search" class="form-label">@lang('Purchased Date')</label>
                        <input type="text" class="form-control datepicker" name="purchase_date" autofocus="off" readonly placeholder="@lang('Purchased Date')" value="{{ old('purchase_date',request()->purchase_date) }}">
                    </div>
                    <div class="input-box">
                        <label for="search" class="form-label">@lang('Expired Date')</label>
                        <input type="text" class="form-control datepicker" name="expire_date" autofocus="off" readonly placeholder="@lang('Expired Date')" value="{{ old('expire_date',request()->expire_date) }}">
                    </div>
                    <div class="input-box">
                        <label class="form-label">@lang('Validity')</label>
                        <select name="package_status" id="package_status" class="form-control js-example-basic-single">
                            <option selected disabled>@lang('Select Validity')</option>
                            <option value="active" {{ request()->package_status == 'active' ? 'selected' : '' }}>@lang('Active')</option>
                            <option value="expired" {{ request()->package_status == 'expired' ? 'selected' : '' }}>@lang('Expired')</option>
                        </select>
                    </div>
                    <div class="input-box">
                        <label class="form-label">@lang('Status')</label>
                        <select name="status" class="form-control js-example-basic-single">
                            <option selected disabled>@lang('Status')</option>
                            <option value="0" @if(@request()->status == '0') selected @endif>@lang('Pending')</option>
                            <option value="1" @if(@request()->status == '1') selected @endif>@lang('Approved')</option>
                            <option value="2" @if(@request()->status == '2') selected @endif>@lang('Cancelled')</option>
                        </select>
                    </div>
                    <div class="btn-area">
                        <button type="submit" class="cmn-btn w-100">@lang('Filter')</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection

@push('script')
    <script src="{{ asset('assets/global/js/bootstrap-datepicker.js') }}"></script>
    <script src="{{ asset('assets/global/js/select2.min.js') }}"></script>
    <script>
        'use strict'

        $(document).on('click', '.cancelSubscriptionBtn', function () {
            let route = $(this).data('route');
            $('.cancelRoute').attr('action', route);
        });

        $(document).ready(function () {
            $(".datepicker").datepicker({
                autoclose: true,
                clearBtn: true
            });

            $('.notiflix-confirm').on('click', function () {
                var route = $(this).data('route');
                $('.deleteRoute').attr('action', route)
            })
        });
    </script>

    <script>
        "use strict";
        (function ($) {
            $(document).on('click', '.renewPackage', function () {
                var planModal = new bootstrap.Modal(document.getElementById('renewPackageModal'))
                planModal.show()

                let dataRoute = $(this).data('route');
                $('.renewPackageForm').attr('action', dataRoute);

                let plan_name = $(this).data('plan');
                let symbol = "{{trans(basicControl()->currency_symbol)}}";
                let price = $(this).data('price');
                let listing = $(this).data('listing');

                let plan_expire_time = $(this).data('expiretime');
                let plan_expire_type = $(this).data('expiretype');
                let packageValidity = plan_expire_time + ' ' + plan_expire_type;

                $('.plan-name').text(plan_name);
                $('.plan-price').text(`${symbol}${price}`);

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
