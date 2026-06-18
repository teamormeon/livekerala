@extends('user_panel.layouts.user')
@section('title',__('Support Ticket'))
@push('style')
    <link rel="stylesheet" href="{{ asset('assets/global/css/bootstrap-datepicker.css') }}"/>
@endpush
@section('content')
    <!-- main -->
    <div class="container-fluid">
        <div class="main row">
            <div class="col-12">
                <div
                    class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="mb-0">@lang('My Tickets')</h3>
                </div>

                <!-- table -->
                <div class="table-parent table-responsive listing-table-parent">
                    <div class="table-heading py-3 d-flex justify-content-between align-items-center">
                        <h4>@lang('Latest Listings')</h4>
                        <div class="d-flex align-items-center">
                            <a href="{{route('user.ticket.create')}}"
                               class="cmn-btn customButton notiflix-confirm me-2"> <i class="fal fa-plus"
                                                                                                     aria-hidden="true"></i> @lang('Create Ticket')
                            </a>
                            <button type="button" class="cmn-btn customButton" data-bs-toggle="offcanvas"
                                    data-bs-target="#offcanvasExample" aria-controls="offcanvasExample">@lang('Filter')
                                <i class="fal fa-filter"></i>
                            </button>
                        </div>
                    </div>
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th scope="col">@lang('Ticket')</th>
                            <th scope="col">@lang('Subject')</th>
                            <th scope="col">@lang('Status')</th>
                            <th scope="col">@lang('Last Reply')</th>
                            <th scope="col" class="text-end">@lang('Action')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($tickets as $key => $ticket)
                            <tr>
                                <td data-label="Ticket">
                                     <span
                                         class="font-weight-bold"> [{{ trans('Ticket#').$ticket->ticket }}]
                                     </span>
                                </td>

                                <td data-label="Subject">
                                    <span
                                        class="font-weight-bold"> {{ $ticket->subject }}
                                     </span>
                                </td>

                                <td data-label="Status">
                                    @if($ticket->status == 0)
                                        <span
                                            class="badge rounded-pill bg-warning">@lang('Open')</span>
                                    @elseif($ticket->status == 1)
                                        <span
                                            class="badge rounded-pill bg-success">@lang('Answered')</span>
                                    @elseif($ticket->status == 2)
                                        <span
                                            class="badge rounded-pill bg-info">@lang('Replied')</span>
                                    @elseif($ticket->status == 3)
                                        <span class="badge rounded-pill bg-danger">@lang('Closed')</span>
                                    @endif
                                </td>

                                <td data-label="Last Reply">
                                    {{diffForHumans($ticket->last_reply) }}
                                </td>

                                <td class="action" data-label="Action">
                                    <div class="d-flex justify-content-end">
                                        <a href="{{ route('user.ticket.view', $ticket->ticket) }}"
                                           class="btn2 btn" title="@lang('Details')"> <i class="fas fa-eye"></i> </a>
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
                {{ $tickets->appends($_GET)->links('user_panel.partials.pagination') }}
            </div>
        </div>
    </div>

    @push('loadModal')
        <div
            class="modal fade"
            id="delete-modal"
            tabindex="-1"
            aria-labelledby="editModalLabel"
            aria-hidden="true"
        >
            <div class="modal-dialog modal-dialog-top modal-md">
                <div class="modal-content">
                    <div class="modal-header modal-primary modal-header-custom">
                        <h4 class="modal-title" id="editModalLabel">@lang('Delete Confirmation')</h4>
                        <button
                            type="button"
                            class="close-btn"
                            data-bs-dismiss="modal"
                            aria-label="Close"
                        >
                            <i class="fal fa-times"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        @lang('Are you sure delete?')
                    </div>
                    <hr>
                    <div class="modal-footer">
                        <button
                            type="button"
                            class="btn-custom btn2"
                            data-bs-dismiss="modal"
                        >
                            @lang('No')
                        </button>
                        <form action="" method="post" class="deleteRoute">
                            @csrf
                            @method('delete')
                            <button type="submit" class="btn btn-custom btn-custom-listing-modal">@lang('Yes')</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endpush

    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasExample" aria-labelledby="offcanvasExampleLabel">
        <div class="offcanvas-header">
            <h4 class="offcanvas-title" id="offcanvasExampleLabel">@lang('Filter Ticket')</h4>
            <button type="button" class="cmn-btn-close" data-bs-dismiss="offcanvas" aria-label="Close">
                <i class="fal fa-arrow-right"></i>
            </button>
        </div>
        <div class="offcanvas-body">
            <form action="" method="get" enctype="multipart/form-data">
                <div class="row g-4">
                    <div class="input-box">
                        <label for="search" class="form-label">@lang('Ticket')</label>
                        <input type="text" name="ticket" class="form-control" placeholder="@lang('Ticket No')"
                               value="{{ old('ticket',request()->ticket) }}"/>
                    </div>
                    <div class="input-box">
                        <label for="search" class="form-label">@lang('Date')</label>
                        <input type="text" class="form-control datepicker" name="date_time" autofocus="off" readonly
                               placeholder="@lang('Choose Date')" value="{{ old('date_time',request()->date_time) }}">
                    </div>
                    <div class="input-box">
                        <label class="form-label">@lang('Status')</label>
                        <select name="status" class="form-control js-example-basic-single">
                            <option value="">@lang('All Ticket')</option>
                            <option value="0"
                                    @if(@request()->status == '0') selected @endif>@lang('Open Ticket')</option>
                            <option value="1"
                                    @if(@request()->status == '1') selected @endif>@lang('Answered Ticket')</option>
                            <option value="2"
                                    @if(@request()->status == '2') selected @endif>@lang('Replied Ticket')</option>
                            <option value="3"
                                    @if(@request()->status == '3') selected @endif>@lang('Closed Ticket')</option>
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
    <script>
        'use strict'
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
@endpush
