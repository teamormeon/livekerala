@extends(template().'layouts.app')
@section('title','Sign Up')
@section('banner_heading')
    @lang('Register')
@endsection
@section('content')
    <section class="login-signup-page">

        <div class="container">
            <div class="login-signup-page-inner">
                <div class="row align-items-center justify-content-center">
                    <div class="col-xl-6 col-lg-8 col-md-10">
                        <div class="login-signup-form">
                            <form action="{{ route('register') }}" method="post">
                                @csrf
                                <div class="section-header">
                                    <h3>@lang('Welcome back!')</h3>
                                    <div class="description">@lang('Hey Enter your details to get sign in to your account')t</div>
                                </div>
                                <div class="row g-4">
                                    <div class="input-box col-12">
                                        <input
                                            type="text"
                                            name="firstname"
                                            value="{{old('firstname')}}"
                                            class="form-control"
                                            placeholder="@lang('First name')"
                                        />
                                    </div>
                                    @error('firstname')
                                    <span class="text-danger mt-1">@lang($message)</span>
                                    @enderror
                                    <div class="input-box col-12">
                                        <input
                                            type="text"
                                            name="lastname"
                                            value="{{old('lastname')}}"
                                            class="form-control"
                                            placeholder="@lang('Last name')"
                                        />
                                    </div>
                                    @error('lastname')
                                    <span class="text-danger mt-1">@lang($message)</span>
                                    @enderror
                                    <div class="input-box col-12">
                                        <input
                                            type="text"
                                            name="username"
                                            value="{{old('username')}}"
                                            class="form-control"
                                            placeholder="@lang('Username')"
                                        />
                                    </div>
                                    @error('username')
                                    <span class="text-danger mt-1">@lang($message)</span>
                                    @enderror
                                    <div class="input-box col-12">
                                        <input
                                            type="text"
                                            name="email"
                                            value="{{old('email')}}"
                                            class="form-control"
                                            placeholder="@lang('Email Address')"
                                        />
                                    </div>
                                    @error('email')
                                    <span class="text-danger mt-1">@lang($message)</span>
                                    @enderror
                                    <div class="input-box col-12">
                                        <div class="row">
                                            <div class="col-4">
                                                <select name="phone_code" class="form-control country_code dialCode-change">
                                                    @foreach(config('country') as $value)
                                                        <option value="{{$value['phone_code']}}"
                                                                data-name="{{$value['name']}}"
                                                                data-code="{{$value['code']}}"
                                                            {{ old('phone_code') == $value['phone_code'] ? 'selected' : ''}}
                                                        > {{$value['name']}} ({{$value['phone_code']}})
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-8">
                                                <input type="text" name="phone" class="form-control dialcode-set" value="{{old('phone')}}" placeholder="@lang('Phone Number')">
                                            </div>

                                            <input type="hidden" name="country_name" value="" class="text-dark country_name">
                                            <input type="hidden" name="country_code" value="" class="text-dark country_code">
                                        </div>
                                    </div>
                                    @error('phone')
                                    <span class="text-danger mt-1">@lang($message)</span>
                                    @enderror
                                    <div class="input-box col-12">
                                        <input
                                            type="password"
                                            name="password"
                                            class="form-control"
                                            placeholder="@lang('Password')"
                                        />
                                    </div>
                                    @error('password')
                                    <span class="text-danger mt-1">@lang($message)</span>
                                    @enderror
                                    <div class="input-box col-12">
                                        <input
                                            type="password"
                                            name="password_confirmation"
                                            class="form-control"
                                            placeholder="@lang('Confirm Password')"
                                        />
                                    </div>

                                    @if(basicControl()->google_recaptcha == 1 && basicControl()->google_recaptcha_login == 1)
                                        <div class="mt-4">
                                            <div class="g-recaptcha @error('g-recaptcha-response') is-invalid @enderror" data-sitekey="{{ env('GOOGLE_RECAPTCHA_SITE_KEY') }}"></div>
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

                                            <div class="input-group input-group-merge captchaDiv" data-hs-validation-validate-class>
                                                <img src="{{route('captcha').'?rand='. rand()}}" id='captcha_image'>
                                                <a class="input-group-append input-group-text"
                                                   href='javascript: refreshCaptcha();'>
                                                    <i class="bi-arrow-repeat text-dark"></i>
                                                </a>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <button type="submit" class="btn cmn-btn mt-30 w-100">@lang('sign up')</button>
                                <div class="pt-20 text-center">
                                    @lang('Already have an account?')
                                    <p class="mb-0 highlight mt-1"><a href="{{ route('login') }}">@lang('Login Here')</a></p>
                                </div>
                            </form>
                        </div>
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
        "use strict";

        $(document).ready(function() {
            $('select[name="phone_code"]').change(updateCountryFields);
            updateCountryFields();
        });

        function updateCountryFields() {
            var selectedOption = $('select[name="phone_code"] option:selected');
            var countryName = selectedOption.data('name');
            var countryCode = selectedOption.data('code');

            $('input[name="country_name"]').val(countryName);
            $('input[name="country_code"]').val(countryCode);
        }

        function refreshCaptcha() {
            let img = document.images['captcha_image'];
            img.src = img.src.substring(
                0, img.src.lastIndexOf("?")
            ) + "?rand=" + Math.random() * 1000;
        }
    </script>
@endpush
