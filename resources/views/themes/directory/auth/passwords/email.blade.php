@extends(template().'layouts.app')
@section('title')
    @lang('Reset Password')
@endsection
@section('banner_heading')
   @lang('Recover Password')
@endsection

@section('content')
    <section class="login-signup-page">
        <div class="container">
            <div class="login-signup-page-inner">
                <div class="row align-items-center justify-content-center">
                    <div class="col-xl-6 col-lg-8 col-md-10">
                        <div class="login-signup-form">
                            @if (session('status'))
                                <div class="alert alert-success alert-dismissible fade show w-100" role="alert">
                                    {{ trans(session('status')) }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif
                            <form action="{{ route('password.email') }}" method="post">
                                @csrf
                                <div class="section-header">
                                    <h3>@lang('Recover password')</h3>
                                    <div class="description">@lang('Enter your email to recover the password')</div>
                                </div>

                                <div class="row g-4">
                                    <div class="col-12">
                                        <input
                                            type="email"
                                            autocomplete="off"
                                            name="email"
                                            class="form-control"
                                            value="{{old('email')}}"
                                            placeholder="@lang('Enter Your Email address')"
                                        />
                                        @error('email')
                                        <span class="text-danger mt-1">{{ trans($message) }}</span>
                                        @enderror
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

