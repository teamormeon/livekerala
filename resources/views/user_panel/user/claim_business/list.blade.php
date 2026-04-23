@extends('user_panel.layouts.user')
@section('title',trans('Claim Business'))

@push('style')
    <link rel="stylesheet" href="{{ asset('assets/global/css/bootstrap-datepicker.css') }}"/>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="main row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="mb-0">@lang('Claim Business')</h3>
                </div>

                <div class="switcher">
                    <a href="{{ route('user.claim.business.list', 'customer-claim') }}">
                        <button class="{{(lastUriSegment() == 'customer-claim') ? 'active' : ''}} position-relative">
                            @lang('Customer Claim')
                        </button>
                    </a>

                    <a href="{{ route('user.claim.business.list','my-claim') }}">
                        <button class="{{(lastUriSegment() == 'my-claim') ? 'active' : ''}} position-relative">
                            @lang('My Claim')
                        </button>
                    </a>
                </div>

                <!-- table -->
                <div class="table-parent table-responsive">
                    <div class="table-heading py-3 d-flex justify-content-between align-items-center">
                        <h4>@lang('Claim List')</h4>
                        <button type="button" class="cmn-btn customButton" data-bs-toggle="offcanvas"
                                data-bs-target="#offcanvasExample" aria-controls="offcanvasExample">@lang('Filter')<i
                                class="fal fa-filter"></i>
                        </button>
                    </div>
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th scope="col">@lang('Listing Title')</th>
                            @if($type == 'customer-claim')
                                <th scope="col">@lang('Claimer') </th>
                            @else
                                <th scope="col">@lang('Owner') </th>
                            @endif
                            <th scope="col">@lang('Status')</th>
                            <th scope="col">@lang('Date Time')</th>
                            <th scope="col" class="text-end">@lang('Action')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse ($claimBusiness as $item)
                            <tr>
                                <td data-label="Listing">
                                    <a href="{{ route('listing.details',optional($item->get_listing)->slug) }}"
                                       class="color-change-listing text-info" target="_blank">
                                        @lang(\Illuminate\Support\Str::limit(optional($item->get_listing)->title, 50))</a>
                                </td>

                                @if($type == 'customer-claim')
                                    <td class="company-logo" data-label="Customer">
                                        <div class="d-flex">
                                            <div>
                                                <a href="{{ route('profile', optional($item->get_client)->username) }}"
                                                   target="_blank">
                                                    <img src="{{ optional($item->get_client)->imgPath }}" alt="image">
                                                </a>
                                            </div>
                                            <div>
                                                @lang(optional($item->get_client)->fullName)
                                                <br> @lang(optional($item->get_client)->email)
                                            </div>
                                        </div>
                                    </td>
                                @else
                                    <td class="company-logo" data-label="Customer">
                                        <div class="d-flex">
                                            <div>
                                                <a href="{{ route('profile', optional($item->get_listing_owner)->username) }}"
                                                   target="_blank">
                                                    <img src="{{ optional($item->get_listing_owner)->imgPath }}"
                                                         alt="image">
                                                </a>
                                            </div>
                                            <div>
                                                @lang(optional($item->get_listing_owner)->fullName)
                                                <br> @lang(optional($item->get_listing_owner)->email)
                                            </div>
                                        </div>
                                    </td>
                                @endif

                                <td data-label="Message">
                                    <span
                                        class="badge rounded-pill bg-{{ $item->status == 0 ? 'warning' : ($item->status == 1 ? 'success' : 'danger') }}">
                                        {{ $item->status == 0 ? 'Pending' : ($item->status == 1 ? 'Approved' : 'Rejected') }}
                                    </span>
                                </td>
                                <td data-label="Time">{{ dateTime($item->created_at) }}</td>
                                <td data-label="@lang('Action')">
                                    <div class="dropdown-btns">
                                        <button type="button" class="dropdown-toggle" data-bs-toggle="dropdown"
                                                aria-expanded="false">
                                            <i class="far fa-ellipsis-v"></i>
                                        </button>
                                        @if($item->is_chat_start == 1)
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <a href="{{ route('user.claim.business.conversation', $item->uuid) }}"
                                                       class="btn currentColor dropdown-item"> <i
                                                            class="fal fa-comments me-1"></i> @lang('Conversation')</a>
                                                </li>
                                            </ul>
                                        @endif
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
                {{ $claimBusiness->appends($_GET)->links('user_panel.partials.pagination') }}
            </div>
        </div>
    </div>


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
