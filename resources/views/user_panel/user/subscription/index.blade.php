@extends(template().'layouts.user')
@section('page_title',__('My Subscription'))
@section('content')
    <div class="card mt-50">
        <div class="card-body">
            <div class="cmn-table">
                <div class="table-responsive overflow-visible">
                    <table class="table align-middle table-striped">
                        <thead>
                        <tr>
                            <th scope="col">@lang('Plan Name')</th>
                            <th scope="col">@lang('Plan Type')</th>
                            <th scope="col">@lang('Paid By')</th>
                            <th scope="col">@lang('Subscribed On')</th>
                            <th scope="col">@lang('Expired On')</th>
                            <th scope="col">@lang('Subscription Type')</th>
                            <th scope="col">@lang('Status')</th>
                            <th scope="col">@lang('Action')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(count($subscriptions) > 0)
                            @foreach($subscriptions as $key => $value)

                                <tr>
                                    <td data-label="@lang('Plan Name')">
                                        {{$value->plan_name ?? 'Unknown'}}
                                    </td>
                                    <td data-label="@lang('Plan Type')">
                                        <span>{{ucfirst($value->frequency) ?? '-'}}</span>
                                    </td>
                                    <td data-label="@lang('Paid By')">{{ ($value->gateway) ? ucfirst(optional($value->gateway)->name) : '-' }}</td>
                                    <td data-label="@lang('Subscribed On')">
                                        <span
                                            class="text-bold"> {{dateTime($value->created_at,basicControl()->date_time_format)}}</span>
                                    </td>
                                    <td data-label="@lang('Expired On')">
                                        <span
                                            class="text-danger"> {{dateTime($value->subs_expired_at,basicControl()->date_time_format)}}</span>
                                    </td>
                                    <td data-label="@lang('Subscription Type')">
                                        @if($value->api_subscription_id)
                                            <span
                                                class="badge text-bg-success">@lang('Automatic')</span>
                                        @else
                                            <span
                                                class="badge text-bg-warning">@lang('Manual')</span>
                                        @endif
                                    </td>
                                    <td data-label="@lang('Status')">
                                        @if($value->status == 1)
                                            <span
                                                class="badge text-bg-success">@lang('Running')</span>
                                        @elseif($value->status == 0)
                                            <span
                                                class="badge text-bg-danger">@lang('Cancelled')</span>
                                        @endif
                                    </td>
                                    <td data-label="@lang('Action')">
                                        <div class="cmn-btn-group">
                                            @if($value->api_subscription_id)
                                                @if($value->status)
                                                    <button type="button" data-bs-toggle="modal"
                                                            data-bs-target="#deleteModal"
                                                            data-route="{{route('user.subscription.cancel',$value->id)}}"
                                                            class="action-btn deleteBtn">
                                                        <i data-bs-toggle="tooltip" data-bs-placement="top"
                                                           data-bs-title="@lang('Cancel Subscription')"
                                                           class="fa-regular fa-window-close"></i>
                                                    </button>
                                                @else
                                                    -
                                                @endif
                                            @else
                                                <button type="button" data-bs-toggle="modal"
                                                        data-bs-target="#renewModal"
                                                        data-res="{{$value}}"
                                                        data-route="{{route('user.subscription.renew',$value->id)}}"
                                                        class="action-btn renewBtn">
                                                    <i data-bs-toggle="tooltip" data-bs-placement="top"
                                                       data-bs-title="@lang('Renew Subscription')"
                                                       class="fa-regular fa-sync-alt"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            @include('empty')
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    {{ $subscriptions->appends($_GET)->links($theme.'partials.user.pagination') }}

    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="describeModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="describeModalLabel"> @lang('Confirmation !')</h4>
                    <button type="button" class="close-btn" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fal fa-times"></i>
                    </button>
                </div>
                <form method="post" action="" class="deleteForm">
                    @csrf
                    <div class="modal-body">
                        <p>
                            @lang('Are you want to cancel this subscription')?
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="cmn-btn3"
                                data-bs-dismiss="modal">@lang('Close')</button>
                        <button type="submit" name="replayTicket"
                                class="cmn-btn">@lang("Confirm")</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="renewModal" tabindex="-1" aria-labelledby="describeModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="describeModalLabel"> @lang('Subscription Details')</h4>
                    <button type="button" class="close-btn" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fal fa-times"></i>
                    </button>
                </div>
                <form method="post" action="" class="renewForm">
                    @csrf
                    <div class="modal-body">
                        <ul id="showDetails">

                        </ul>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="cmn-btn3"
                                data-bs-dismiss="modal">@lang('Close')</button>
                        <button type="submit"
                                class="cmn-btn">@lang("Renew")</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('extra_scripts')
    <script>
        'use strict'
        $(document).on('click', '.deleteBtn', function () {
            let route = $(this).data('route');
            $('.deleteForm').attr('action', route);
        });

        $(document).on('click', '.renewBtn', function () {
            let res = $(this).data('res');
            let expiryDate = new Date(res.subs_expired_at).toDateString()
            $('.renewForm').attr('action', $(this).data('route'));

            if (res.design_and_code_editors == 1) {
                var design_and_code_editors = 'Yes'
            } else {
                var design_and_code_editors = 'No'
            }

            if (res.automation == 1) {
                var automation = 'Yes'
            } else {
                var automation = 'No'
            }

            if (res.single_send == 1) {
                var single_send = 'Yes'
            } else {
                var single_send = 'No'
            }

            if (res.segmentation == 1) {
                var segmentation = 'Yes'
            } else {
                var segmentation = 'No'
            }

            if (res.custom_field == 1) {
                var custom_field = 'Yes'
            } else {
                var custom_field = 'No'
            }

            if (res.notify_recipient == 1) {
                var notify_recipient = 'Yes'
            } else {
                var notify_recipient = 'No'
            }

            if (res.activities == 1) {
                var activities = 'Yes'
            } else {
                var activities = 'No'
            }

            if (res.api_uses == 1) {
                var api_uses = 'Yes'
            } else {
                var api_uses = 'No'
            }

            var dom = `<div class="d-flex justify-content-between mb-2">
								<li class="text-dark">@lang('Plan Name:')</li>
								<span class="text-dark">${res.plan_name}</span></div>
							<div class="d-flex justify-content-between mb-2">
								<li class="text-dark">@lang('Price:')</li>
								<span class="text-dark">${res.price} {{basicControl()->base_currency}}</span></div>
							<div class="d-flex justify-content-between mb-2">
								<li class="text-danger">@lang('Expiry Date:')</li>
								<span class="text-danger">${expiryDate}</span></div>
							<div class="d-flex justify-content-between mb-2">
								<li class="text-dark">@lang('Contacts:')</li>
								<span class="text-dark">${res.number_of_contacts}</span></div>
							<div class="d-flex justify-content-between mb-2">
								<li class="text-dark">@lang('Emails:')</li>
								<span class="text-dark">${res.number_of_emails}</span></div>
							<div class="d-flex justify-content-between mb-2">
								<li class="text-dark">@lang('Sending Server:')</li>
								<span class="text-dark">${res.sending_server}</span></div>
							<div class="d-flex justify-content-between mb-2">
								<li class="text-dark">@lang('Drag & Drop Editor:')</li>
								<span class="text-dark">${design_and_code_editors}</span></div>
							<div class="d-flex justify-content-between mb-2">
								<li class="text-dark">@lang('Make Automation:')</li>
								<span class="text-dark">${automation}</span></div>
							<div class="d-flex justify-content-between mb-2">
								<li class="text-dark">@lang('Make Single Send:')</li>
								<span class="text-dark">${single_send}</span></div>
							<div class="d-flex justify-content-between mb-2">
								<li class="text-dark">@lang('Make Segmentation:')</li>
								<span class="text-dark">${segmentation}</span></div>
							<div class="d-flex justify-content-between mb-2">
								<li class="text-dark">@lang('Add Custom Field:')</li>
								<span class="text-dark">${custom_field}</span></div>
							<div class="d-flex justify-content-between mb-2">
								<li class="text-dark">@lang('Add Notify Recipients:')</li>
								<span class="text-dark">${notify_recipient}</span></div>
							<div class="d-flex justify-content-between mb-2">
								<li class="text-dark">@lang('Activities:')</li>
								<span class="text-dark">${activities}</span></div>
							<div class="d-flex justify-content-between mb-2">
								<li class="text-dark">@lang('API Implementation:')</li>
								<span class="text-dark">${api_uses}</span></div>`


            $('#showDetails').html(dom);
        });
    </script>
@endpush
