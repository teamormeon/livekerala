@extends('user_panel.layouts.user')
@section('title',trans('Product Enquiries'))

@push('style')
    <link rel="stylesheet" href="{{ asset('assets/global/css/bootstrap-datepicker.css') }}"/>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="main row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="mb-0">@lang('Product Enquiries')</h3>
                </div>
                <div class="switcher">
                    <a href="{{ route('user.product.queries', 'customer-enquiry') }}">
                        <button class="{{(lastUriSegment() == 'customer-enquiry') ? 'active' : ''}} position-relative">
                            @lang('Customer Enquiry')
                            @if(count($customerEnquery) > 0)
                                <sup class="text-danger custom__queiry_count"> <span
                                        class="badge bg-primary rounded-circle">{{ count($customerEnquery) }}</span></sup>
                            @endif
                        </button>
                    </a>

                    <a href="{{ route('user.product.queries','my-enquiry') }}">
                        <button class="{{(lastUriSegment() == 'my-enquiry') ? 'active' : ''}} position-relative">
                            @lang('My Enquiry')
                            @if($myReply > 0)
                                <sup class="text-danger custom__queiry_count"> <span
                                        class="badge bg-primary rounded-circle">{{ $myReply }}</span> </sup>
                            @endif
                        </button>
                    </a>
                </div>

                <!-- table -->
                <div class="table-parent table-responsive">
                    <div class="table-heading py-3 d-flex justify-content-between align-items-center">
                        <h4>@lang('Product Enquiries')</h4>
                        <button type="button" class="cmn-btn customButton" data-bs-toggle="offcanvas"
                                data-bs-target="#offcanvasExample" aria-controls="offcanvasExample">@lang('Filter')<i
                                class="fal fa-filter"></i>
                        </button>
                    </div>
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            @if($type == 'customer-enquiry')
                                <th scope="col">@lang('Customer') </th>
                            @else
                                <th scope="col">@lang('Vendor') </th>
                            @endif
                            <th scope="col">@lang('Listing')</th>
                            <th scope="col">@lang('Product')</th>
                            <th scope="col">@lang('Message')</th>
                            <th scope="col">@lang('Time')</th>
                            <th scope="col" class="text-end">@lang('Action')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse ($productQueries as $key => $query)
                            @php
                                $client_reply = \App\Models\ProductReply::where('client_id', \Illuminate\Support\Facades\Auth::user()->id)->where('product_query_id', $query->id)->where('status', 0)->count();
                            @endphp
                            <tr>
                                @if($query->get_client && $query->get_client->id == \Illuminate\Support\Facades\Auth::user()->id)
                                    <td class="company-logo" data-label="Vendor">
                                        <div class="d-flex">
                                            <div>
                                                <a href="{{ route('profile', optional($query->get_user)->username) }}"
                                                   target="_blank">
                                                    <img
                                                        src="{{ getFile(optional($query->get_user)->image_driver, optional($query->get_user)->image) }}">
                                                </a>
                                            </div>
                                            <div>
                                                @lang(optional($query->get_user)->firstname) @lang(optional($query->get_user)->lastname)
                                                <br> @lang(optional($query->get_user)->email)
                                            </div>
                                        </div>
                                    </td>
                                @else
                                    <td class="company-logo" data-label="Customer">
                                        <div class="d-flex">
                                            <div>
                                                <a href="{{ route('profile', optional($query->get_client)->username) }}"
                                                   target="_blank">
                                                    <img
                                                        src="{{ getFile(optional($query->get_client)->image_driver, optional($query->get_client)->image) }}"
                                                        alt="image">
                                                </a>
                                            </div>
                                            <div>
                                                @lang(optional($query->get_client)->firstname) @lang(optional($query->get_client)->lastname)
                                                <br> @lang(optional($query->get_client)->email)
                                            </div>
                                        </div>
                                    </td>
                                @endif

                                <td data-label="Listing">
                                    <a href="{{ route('listing.details',optional($query->get_listing)->slug) }}"
                                       class="color-change-listing text-secondary" target="_blank">
                                        @lang(\Illuminate\Support\Str::limit(optional($query->get_listing)->title, 50))</a>
                                </td>

                                <td data-label="Product">
                                    @lang(Str::limit(optional($query->get_product)->product_title, 50))
                                </td>


                                <td data-label="Message">
                                    <a href="{{ route('user.product.query.reply', $query->id) }}"
                                       class="btn btn-sm position-relative">
                                        <i class="fal fa-sms text-secondary"></i>
                                        @if(Auth::id() == $query->user_id)
                                            @php
                                                $customerEnquirySms = \App\Models\ProductQuery::where('id', $query->id)->where('customer_enquiry', 0)->count();
                                            @endphp

                                            @if($customerEnquirySms > 0 || $client_reply > 0)
                                                <span
                                                    class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger message-number-size"> @if($client_reply > 0)
                                                        {{ $client_reply }}
                                                    @else
                                                        {{ $customerEnquirySms }}
                                                    @endif</span>
                                            @endif
                                        @elseif(Auth::id() == $query->client_id)
                                            @if($client_reply > 0)
                                                <span
                                                    class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger message-number-size">{{ $client_reply }} </span>
                                            @endif
                                        @endif
                                    </a>
                                </td>

                                <td data-label="Time">{{ dateTime($query->created_at) }}</td>
                                <td data-label="@lang('Action')">
                                    <div class="dropdown-btns">
                                        <button type="button" class="dropdown-toggle" data-bs-toggle="dropdown"
                                                aria-expanded="false">
                                            <i class="far fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <a href="{{ route('user.product.query.reply', $query->id) }}"
                                                   class="btn currentColor dropdown-item"> <i
                                                        class="fal fa-reply"></i> @lang('Send reply')</a>
                                            </li>
                                            <li>
                                                <a href="javascript:void(0)" data-bs-toggle="modal"
                                                   data-bs-target="#delete-modal"
                                                   class="btn currentColor notiflix-confirm dropdown-item"
                                                   data-route="{{ route('user.product.query.delete', $query->id) }}">
                                                    <i class="far fa-trash"></i> @lang('Delete')
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <td class="text-center" colspan="100%">
                                <img class="noDataImg" src="{{ asset('assets/admin/img/oc-error.svg') }}" alt="image">
                                <p class="mt-3">@lang('No data to show')</p>
                            </td>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $productQueries->appends($_GET)->links('user_panel.partials.pagination') }}
            </div>
        </div>
    </div>

    @push('loadModal')
        <!-- Delete Modal -->
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
                        @lang('Are you sure want to delete this enquiry?')
                    </div>
                    <hr>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('Close')</button>
                        <form action="" method="post" class="deleteRoute">
                            @csrf
                            @method('delete')
                            <button type="submit"
                                    class="rounded-3 text-capitalize btn-custom addCreateListingRoute">@lang('Confirm')</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endpush

    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasExample" aria-labelledby="offcanvasExampleLabel">
        <div class="offcanvas-header">
            <h4 class="offcanvas-title" id="offcanvasExampleLabel">@lang('Filter Product Enquiries')</h4>
            <button type="button" class="cmn-btn-close" data-bs-dismiss="offcanvas" aria-label="Close">
                <i class="fal fa-arrow-right"></i>
            </button>
        </div>
        <div class="offcanvas-body">
            <form action="" method="get" enctype="multipart/form-data">
                <div class="row g-4">
                    <div class="input-box">
                        <label for="search" class="form-label">@lang('Search')</label>
                        <input type="text" name="name" value="{{ old('name',request()->name) }}" class="form-control"
                               placeholder="@lang('Search Here')"/>
                    </div>
                    <div class="input-box">
                        <label class="form-label">@lang('From Date')</label>
                        <input type="text" class="form-control datepicker from_date" name="from_date" autofocus="off"
                               readonly placeholder="@lang('From Date')"
                               value="{{ old('from_date',request()->from_date) }}">
                    </div>
                    <div class="input-box">
                        <label class="form-label">@lang('To Date')</label>
                        <input type="text" class="form-control datepicker to_date" name="to_date" autofocus="off"
                               readonly placeholder="@lang('To Date')" value="{{ old('to_date',request()->to_date) }}"
                               disabled="true">
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
    <script>
        'use strict'
        $(document).ready(function () {
            $(".datepicker").datepicker({
                autoclose: true,
                clearBtn: true
            });

            $('.from_date').on('change', function () {
                $('.to_date').removeAttr('disabled');
            });

            $('.notiflix-confirm').on('click', function () {
                var route = $(this).data('route');
                $('.deleteRoute').attr('action', route)
            })
        });
    </script>
@endpush
