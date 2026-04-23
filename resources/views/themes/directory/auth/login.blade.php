@extends(template().'layouts.app')
@section('title',trans('Sign In'))
@section('banner_heading')
    @lang('Sign In')
@endsection
@section('content')
    <section class="login-signup-page">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xxl-5 col-xl-6 col-lg-7 col-md-9">
                    <div class="login-signup-form">
                        <form action="{{ route('login') }}" method="post">
                            @csrf
                            <div class="section-header">
                                <h3>@lang('Welcome back!')</h3>
                                <div class="description">@lang('Hey Enter your details to get sign in to your account')
                                </div>
                            </div>



                            <div class="row g-4">
                                <div class="col-12">
                                    <input
                                        type="text"
                                        name="username"
                                        value="{{ old('username', config('demo.IS_DEMO') ? (request()->username ?? 'demouser') : '') }}"
                                        class="form-control"
                                        autocomplete="off"
                                        placeholder="@lang('Email Or Username')"
                                    />
                                    @error('username')<span
                                        class="text-danger float-left">@lang($message)</span>@enderror
                                    @error('email')<span class="text-danger float-left">@lang($message)</span>@enderror
                                </div>
                                <div class="col-12">
                                    <div class="password-box">
                                        <input
                                            type="password"
                                            name="password"
                                            value="{{ old('password', config('demo.IS_DEMO') ? (request()->password ?? 'demouser') : '') }}"
                                            class="form-control"
                                            placeholder="@lang('Password')"
                                            autocomplete="off"
                                            id="password-input"/>
                                        <i class="password-icon fa-regular fa-eye" id="toggle-password"></i>
                                    </div>
                                    @error('password')
                                    <span class="text-danger mt-1">@lang($message)</span>
                                    @enderror
                                </div>
                                @if(basicControl()->google_recaptcha == 1 && basicControl()->google_recaptcha_login == 1)
                                    <div class="mt-4">
                                        <div class="g-recaptcha @error('g-recaptcha-response') is-invalid @enderror"
                                             data-sitekey="{{ env('GOOGLE_RECAPTCHA_SITE_KEY') }}"></div>
                                        @error('g-recaptcha-response')
                                        <span class="invalid-feedback d-block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                @endif
                                @if(basicControl()->manual_recaptcha === 1 && basicControl()->manual_recaptcha_login === 1)
                                    <div class="d-flex">
                                        <div class="w-100 me-3">
                                            <input type="text" tabindex="2"
                                                   class="form-control form-control-lg @error('captcha') is-invalid @enderror"
                                                   name="captcha" id="captcha" autocomplete="off"
                                                   placeholder="Enter Captcha" required>
                                            @error('captcha')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="input-group input-group-merge captchaDiv"
                                             data-hs-validation-validate-class>
                                            <img src="{{route('captcha').'?rand='. rand()}}" id='captcha_image'>
                                            <a class="input-group-append input-group-text"
                                               href='javascript: refreshCaptcha();'>
                                                <i class="bi-arrow-repeat text-dark"></i>
                                            </a>
                                        </div>
                                    </div>
                                @endif
                                <div class="col-12">
                                    <div class="form-check d-flex justify-content-between flex-wrap gap-2">
                                        <div class="check">
                                            <input
                                                class="form-check-input"
                                                type="checkbox"
                                                name="remember"
                                                {{ old('remember') ? 'checked' : '' }}
                                                id="flexCheckDefault"
                                            />
                                            <label
                                                class="form-check-label"
                                                for="flexCheckDefault"
                                            >
                                                @lang('Remember me')
                                            </label>
                                        </div>
                                        <div class="forgot highlight">
                                            <a href="{{ route('password.request') }}">@lang('Forgot password?')</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button class="cmn-btn mt-30 w-100">@lang('Log in')</button>


                            @if(config('socialite.google_status') || config('socialite.facebook_status') || config('socialite.github_status'))

                                <hr class="divider">
                                <div class="cmn-btn-group">
                                    <div class="row g-2">
                                        @if(config('socialite.google_status'))
                                            <div class="col-sm-4">
                                                <a href="{{ route('socialiteLogin','google') }}"
                                                   class="btn cmn-btn3 w-100 social-btn">
                                                    <i class="fab fa-google"></i>@lang('Google')
                                                </a>
                                            </div>
                                        @endif
                                        @if(config('socialite.facebook_status'))
                                            <div class="col-sm-4">
                                                <a href="{{ route('socialiteLogin','facebook') }}"
                                                   class="btn cmn-btn3 w-100 social-btn">
                                                    <i class="fab fa-facebook"></i>@lang('Facebook')
                                                </a>
                                            </div>
                                        @endif
                                        @if(config('socialite.github_status'))
                                            <div class="col-sm-4">
                                                <a href="{{ route('socialiteLogin','github') }}"
                                                   class="btn cmn-btn3 w-100 social-btn">
                                                    <i class="fab fa-github"></i>@lang('Github')
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            <div class="pt-20 text-center">
                                @lang("Don't have an account?")
                                <p class="mb-0 highlight mt-1"><a
                                        href="{{ route('register') }}">@lang('Create account')</a></p>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('extra-js')
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
@endpush

@push('script')
    <script>
        document.getElementById("toggle-password").addEventListener("click", function () {
            const passwordInput = document.getElementById("password-input");
            const icon = this;
            if (passwordInput.type == "password") {
                passwordInput.type = "text";
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");
            } else {
                passwordInput.type = "password";
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");
            }
        });

        function refreshCaptcha() {
            let img = document.images['captcha_image'];
            img.src = img.src.substring(
                0, img.src.lastIndexOf("?")
            ) + "?rand=" + Math.random() * 1000;
        }
    </script>
@endpush




