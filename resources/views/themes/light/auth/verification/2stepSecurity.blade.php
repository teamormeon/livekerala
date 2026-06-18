@extends(template().'layouts.app')
@section('title',trans('twoFA Verification'))
@section('banner_heading')
    @lang('Two Factor Authentication Verification')
@endsection

@section('content')

    <section class="login-section">
        <div class="overlay">
            <div class="container ">
                <div class="row justify-content-center">
                    <div class="col-lg-6">
                        <div class="form-wrapper d-flex align-items-center">
                            <form action="{{route('user.twoFA-Verify')}}"  method="post">
                                @csrf
                                <div class="row g-4">
                                    <div class="col-12">
                                        <h4>@lang('2FA Code')</h4>
                                    </div>
                                    <div class="input-box col-12">
                                        <input type="text" class="form-control"  placeholder="@lang('Code')" name="code" value="{{old('code')}}" autocomplete="off"/>
                                    </div>
                                    @error('code')<span class="text-danger  mt-1">{{ $message }}</span>@enderror
                                    @error('error')<span class="text-danger  mt-1">{{ $message }}</span>@enderror

                                    <button class="btn-custom w-100">@lang('sign in')</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>



@endsection
