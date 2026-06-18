@extends('user_panel.layouts.user')
@section('title',trans('All WishList'))

@push('style')
    <link rel="stylesheet" href="{{ asset('assets/global/css/bootstrap-datepicker.css') }}"/>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="main row">
            <div class="col-12">
                <div
                    class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="mb-0">@lang('My WishList')</h3>
                </div>

                <!-- table -->
                <div class="table-parent table-responsive">
                    <div class="table-heading py-3 d-flex justify-content-between align-items-center">
                        <h4>@lang('Wishlist')</h4>
                        <button type="button" class="cmn-btn customButton" data-bs-toggle="offcanvas"
                                data-bs-target="#offcanvasExample" aria-controls="offcanvasExample">@lang('Filter')<i
                                class="fal fa-filter"></i>
                        </button>
                    </div>
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th scope="col">@lang('Category')</th>
                            <th scope="col">@lang('Listing')</th>
                            <th scope="col">@lang('Added At')</th>
                            <th scope="col" class="text-end">@lang('Action')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse ($favourite_listings as $key => $listing)
                            <tr>
                                <td data-label="Category">
                                    {{ optional($listing->get_listing)->getCategoriesName() }}
                                </td>

                                <td data-label="Listing">
                                    <a href="{{ route('listing.details',optional($listing->get_listing)->slug) }}"
                                       class="color-change-listing text-info"
                                       target="_blank">@lang(Str::limit(optional($listing->get_listing)->title, 50))</a>
                                </td>

                                <td data-label="Added At">{{ dateTime($listing->created_at) }}</td>
                                <td class="action" data-label="Action">
                                    <div class="d-flex justify-content-end">
                                        <button data-bs-toggle="modal" data-bs-target="#delete-modal"
                                                class="btn2 btn notiflix-confirm"
                                                data-route="{{ route('user.wish.list.delete', $listing->id) }}">
                                            <i class="far fa-trash custom-delete-fa"></i>
                                        </button>
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
                {{ $favourite_listings->appends($_GET)->links('user_panel.partials.pagination') }}
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
                        <button type="button" class="btn btn-secondary"
                                data-bs-dismiss="modal">@lang('Close')</button>
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

    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasExample"
         aria-labelledby="offcanvasExampleLabel">
        <div class="offcanvas-header">
            <h4 class="offcanvas-title" id="offcanvasExampleLabel">@lang('Filter Wishlist')</h4>
            <button type="button" class="cmn-btn-close" data-bs-dismiss="offcanvas" aria-label="Close">
                <i class="fal fa-arrow-right"></i>
            </button>
        </div>
        <div class="offcanvas-body">
            <form action="" method="get" enctype="multipart/form-data">
                <div class="row g-4">
                    <div class="input-box">
                        <label for="search" class="form-label">@lang('Search')</label>
                        <input type="text" name="name" value="{{ old('name',request()->name) }}"
                               class="form-control" placeholder="@lang('Search')"/>
                    </div>
                    <div class="input-box">
                        <label class="form-label">@lang('From Date')</label>
                        <input type="text" class="form-control datepicker from_date" name="from_date"
                               autofocus="off" readonly placeholder="@lang('From Date')"
                               value="{{ old('from_date',request()->from_date) }}">
                    </div>
                    <div class="input-box">
                        <label class="form-label">@lang('To Date')</label>
                        <input type="text" class="form-control datepicker to_date" name="to_date" autofocus="off"
                               readonly placeholder="@lang('To Date')"
                               value="{{ old('to_date',request()->to_date) }}" disabled="true">
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
