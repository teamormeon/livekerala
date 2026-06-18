@extends(template().'layouts.app')
@section('title',trans('twoFA Verification'))
@section('banner_heading')
    @lang('Two Factor Authentication Verification')
@endsection

@section('content')
    <section class="login-signup-page">
        <div class="container">
            <div class="login-signup-page-inner">
                <div class="row align-items-center justify-content-center">
                    <div class="col-xl-6 col-lg-8 col-md-10">
                        <div class="login-signup-form">
                            <form action="{{route('user.twoFA-Verify')}}" method="post">
                                @csrf
                                <div class="section-header">
                                    <h3>@lang('2FA Verification')</h3>
                                    <div class="description">@lang('2FA verification code that send to you')</div>
                                </div>

                                <div class="row g-4">
                                    <div class="col-12">
                                        <input
                                            type="text"
                                            class="form-control"
                                            autofocus="off"
                                            placeholder="@lang('Code')"
                                            name="code" value="{{old('code')}}"
                                            autocomplete="off" />
                                        @error('code')<span class="text-danger  mt-1">{{ $message }}</span>@enderror
                                        @error('error')<span class="text-danger  mt-1">{{ $message }}</span>@enderror
                                    </div>
                                </div>
                                <button type="submit" class="btn cmn-btn mt-30 w-100">@lang('Submit')</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
