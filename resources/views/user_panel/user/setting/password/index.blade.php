@extends(template().'layouts.user')
@section('title')
    @lang('Settings')
@endsection
@section('content')

    <div class="container-fluid">
        <div class="main row">
            <div class="col-lg-8 col-md-6 col-12 m-auto">
                <div
                    class="d-flex justify-content-between align-items-center mb-3"
                >
                    <h3 class="mb-0">@lang('Change Password')</h3>
                </div>
                <div class="search-bar my-search-bar">
                    <form action="{{route('user.updatePassword')}}" method="post">
                        @csrf
                        <div class="row g-4 mt-1">
                            <div class="input-box col-md-12">
                                <label for="">@lang('Current Password')</label>
                                <input
                                    type="password"
                                    name="current_password" autocomplete="off"
                                    class="form-control"/>
                                @if($errors->has('current_password'))
                                    <div class="error text-danger">@lang($errors->first('current_password')) </div>
                                @endif
                            </div>

                            <div class="input-box col-md-12">
                                <label for="">@lang('New Password')</label>
                                <input
                                    type="password"
                                    name="password"
                                    autocomplete="off"
                                    class="form-control"/>
                                @if($errors->has('password'))
                                    <div class="error text-danger">@lang($errors->first('password')) </div>
                                @endif
                            </div>
                            <div class="input-box col-md-12">
                                <label for="">@lang('Confirm Password')</label>
                                <input
                                    type="password"
                                    name="password_confirmation"
                                    autocomplete="off"
                                    class="form-control"/>
                                @if($errors->has('password_confirmation'))
                                    <div class="error text-danger">@lang($errors->first('password_confirmation')) </div>
                                @endif
                            </div>

                            <div class="col-12">
                                <button class="btn-custom" type="submit">@lang('Update Password')</button>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
@endsection
