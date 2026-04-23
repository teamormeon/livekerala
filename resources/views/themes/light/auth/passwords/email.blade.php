@extends(template().'layouts.app')
@section('title')
    @lang('Reset Password')
@endsection
@section('banner_heading')
   @lang('Recover Password')
@endsection

@section('content')
    <section class="login-section">
        <div class="overlay h-100">
        <div class="container-fluid h-100">
            <div class="row h-100">
                <div class="col-lg-6 col-md-6 offset-3 col-12">
                    @if (session('status'))
                        <div class="alert alert-success alert-dismissible fade show w-100" role="alert">
                            {{ trans(session('status')) }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    <div class="form-wrapper d-flex align-items-center h-100">
                    <form action="{{ route('password.email') }}" method="post">
                        @csrf
                        <div class="row g-4">
                            <div class="col-12">
                                <h4>@lang('Recover password')</h4>
                            </div>
                            <div class="input-box col-12">
                                <input
                                type="email"
                                autocomplete="off"
                                name="email"
                                class="form-control"
                                value="{{old('email')}}"
                                placeholder="@lang('Enter Your Email address')"
                                />
                            </div>
                            @error('email')
                                <span class="text-danger mt-1">{{ trans($message) }}</span>
                            @enderror
                        </div>
                        <button class="btn-custom w-100 mt-4">@lang('submit')</button>
                    </form>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </section>
@endsection

