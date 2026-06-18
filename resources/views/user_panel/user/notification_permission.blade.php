@extends('user_panel.layouts.user')
@section('title',trans('Notification Settings'))
@section('content')
    <section class="transaction-history twofactor">
        <div class="container-fluid">
            <div class="row mt-2 ms-1">
                <div class="col">
                    <div class="header-text-full mt-2">
                        <h3 class="dashboard_breadcurmb_heading mb-3">@lang('Notification Settings')</h3>
                    </div>
                </div>
            </div>

            <div class="row ms-1">
                <div class="col-12 coin-box-wrapper">
                    <div class="card text-center bg-dark py-2 two-factor-authenticator">
                        <div class="card-body notificationPermission">
                            <form action="{{ route('user.notification.permission.update') }}" method="POST">
                                @csrf
                                <div class="table-responsive">
                                    <table class="table table-striped align-middle">
                                        <thead style="background: aliceblue">
                                            <tr class="text-start">
                                                <th scope="col">@lang('Notification Name')</th>
                                                <th scope="col">@lang('Email')</th>
                                                <th scope="col">@lang('SMS')</th>
                                                <th scope="col">@lang('Push')</th>
                                                <th class="text-start" scope="col">@lang('In App')</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($allTemplates as $item)
                                                <tr>
                                                    <td class="text-start" data-label="Notification Name">
                                                        {{$item->name}}
                                                    </td>
                                                    <td data-label="Email">
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox"
                                                                   role="switch" name="email_key[]"
                                                                   value="{{$item->template_key ?? ""}}"
                                                                   {{ !$item->email ? 'disabled':'' }}
                                                                   id="emailSwitch"
                                                                {{ in_array($item->template_key, optional($user->notifypermission)->template_email_key ?? []) ? 'checked' : '' }}
                                                            >
                                                        </div>
                                                    </td>
                                                    <td data-label="SMS">
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox"
                                                                   role="switch" name="sms_key[]"
                                                                   value="{{$item->template_key ?? ""}}"
                                                                   {{ !$item->sms ? 'disabled':'' }}
                                                                   id="smsSwitch"
                                                                {{ in_array($item->template_key, optional($user->notifypermission)->template_sms_key ?? []) ? 'checked' : '' }}
                                                            >
                                                        </div>
                                                    </td>
                                                    <td data-label="Push">
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox"
                                                                   role="switch"
                                                                   name="push_key[]"
                                                                   value="{{ $item->template_key ?? "" }}"
                                                                   {{ !$item->push ? 'disabled' : '' }}
                                                                   id="pushSwitch"
                                                                {{ in_array($item->template_key, optional($user->notifypermission)->template_push_key ?? []) ? 'checked' : '' }}
                                                            >
                                                        </div>
                                                    </td>
                                                    <td data-label="In App">
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox"
                                                                   role="switch"
                                                                   name="in_app_key[]"
                                                                   value="{{$item->template_key ?? ""}}"
                                                                   id="appSwitch"
                                                                {{!$item->in_app ? 'disabled':''}}
                                                                {{ in_array($item->template_key, optional($user->notifypermission)->template_in_app_key ?? []) ? 'checked' : '' }}
                                                            >
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td class="text-center text-danger" colspan="100%">@lang('No Data Found.')</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                    <hr>
                                    <button type="submit" class="btn-custom ">@lang('Save Changes')</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection



