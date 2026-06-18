@extends(template().'layouts.app')
@section('title',trans('Email Verification'))
@section('banner_heading')
    @lang('Email Verification')
@endsection

@section('content')
    <section class="login-signup-page">
        <div class="container">
            <div class="login-signup-page-inner">
                <div class="row align-items-center justify-content-center">
                    <div class="col-xl-6 col-lg-8 col-md-10">
                        <div class="login-signup-form">
                            <form action="{{route('user.mail.verify')}}" method="post">
                                @csrf
                                <div class="section-header">
                                    <h3>@lang('Email Verification')</h3>
                                    <div class="description">@lang('Enter verification code that send to your email')</div>
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
                                <div class="mt-2">
                                    @lang("Didn't get Code? Click to")
                                    <a href="{{route('user.resend.code')}}?type=email">@lang('Resend code')</a>
                                </div>
                                @error('resend')
                                <p class="text-danger  mt-1">{{ $message }}</p>
                                @enderror
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
