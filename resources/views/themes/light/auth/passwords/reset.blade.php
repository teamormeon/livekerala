@extends(template().'layouts.app')
@section('title','Reset Password')
@section('content')
    <section class="login-section">
        <div class="overlay">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-6">
                        @if(session('status'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ trans(session('status')) }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif


                        @error('token')
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ trans($message) }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        @enderror


                        <div class="form-wrapper d-flex align-items-center">
                            <form action="{{route('password.update')}}" method="post">
                                @csrf
                                <div class="row g-4">
                                    <div class="col-12">
                                        <h4>@lang('Reset Password')</h4>
                                    </div>


                                    <input type="hidden" name="token" value="{{ $token }}">
                                    <input type="hidden" name="email" value="{{ $email }}">


                                    <div class="input-box col-12">
                                        <input type="password" name="password" class="form-control"
                                               placeholder="@lang('New Password')" autocomplete="off"/>
                                    </div>
                                    @error('password')
                                    <span class="text-danger mt-1">@lang($message)</span>
                                    @enderror


                                    <div class="input-box col-12">
                                        <input type="password" name="password_confirmation" class="form-control" placeholder="@lang('Confirm Password')" autocomplete="off" />
                                    </div>

                                </div>
                                <button type="submit" class="btn-custom w-100 mt-3">@lang('sign in')</button>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
