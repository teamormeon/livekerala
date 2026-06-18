@extends(template().'layouts.app')
@section('title')
    {{ 'Pay with '.optional($deposit->gateway)->name ?? '' }}
@endsection

@section('content')
    <section class="payment-section-div">
        <div class="col-xxl-8 col-lg-10 mx-auto">
            <h3>@lang('Pay with '.optional($deposit->gateway)->name)</h3>
            <div class="card p-4">
                <div class="card-header border-0 bg-white">
                    <h4 class="text-center">{{trans('Please follow the instruction below')}}</h4>
                    <p class="text-center mt-2 ">{{trans('Please pay')}}
                        <b class="text--base">{{ getAmount($deposit->payable_amount) }} {{ $deposit->payment_method_currency }}</b> {{trans('for successful payment')}}
                    </p>
                    <p class=" mt-2 ">
                        <?php echo optional($deposit->gateway)->note; ?>
                    </p>
                </div>
                <div class="card-body">
                    <form action="{{route('addFund.fromSubmit',$deposit->trx_id)}}" method="post"
                          enctype="multipart/form-data"
                          class="form-row  preview-form">
                        @csrf
                        @if(optional($deposit->gateway)->parameters)
                            @foreach($deposit->gateway->parameters as $k => $v)
                                @if($v->type == "text")
                                    <div class="col-md-12 mt-2">
                                        <div class="form-group  ">
                                            <label>{{trans($v->field_label)}} @if($v->validation == 'required')
                                                    <span class="text--danger">*</span>
                                                @endif </label>
                                            <input type="text" name="{{$k}}"
                                                   class="form-control bg-transparent"
                                                   @if($v->validation == "required") required @endif>
                                            @if ($errors->has($k))
                                                <span
                                                    class="text-danger">{{ trans($errors->first($k)) }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    @elseif($v->type == "number")
                                        <div class="col-md-12 mt-2">
                                            <div class="form-group  ">
                                                <label>{{trans($v->field_label)}} @if($v->validation == 'required')
                                                        <span class="text--danger">*</span>
                                                    @endif </label>
                                                <input type="text" name="{{$k}}"
                                                       class="form-control bg-transparent"
                                                       @if($v->validation == "required") required @endif>
                                                @if ($errors->has($k))
                                                    <span
                                                        class="text-danger">{{ trans($errors->first($k)) }}</span>
                                                @endif
                                            </div>
                                        </div>
                                @elseif($v->type == "textarea")
                                    <div class="col-md-12 mt-2">
                                        <div class="form-group">
                                            <label>{{trans($v->field_label)}} @if($v->validation == 'required')
                                                    <span class="text--danger">*</span>
                                                @endif </label>
                                            <textarea name="{{$k}}" class="form-control bg-transparent"
                                                      rows="3"
                                                      @if($v->validation == "required") required @endif></textarea>
                                            @if ($errors->has($k))
                                                <span
                                                    class="text-danger">{{ trans($errors->first($k)) }}</span>
                                            @endif
                                        </div>
                                    </div>
                                @elseif($v->type == "file")
                                    <div class="col-md-6 form-group mt-2">
                                        <div class="cmn-file-input">
                                            <label><i class="fal fa-camera-alt"></i> {{trans('Upload '.$v->field_label)}} @if($v->validation == 'required')
                                                    <span class="text--danger">*</span>
                                                @endif </label>
                                            <div>
                                                <input class="form-control mt-0" type="file" name="{{$k}}" id="image" accept="image/*"
                                                       @if($v->validation == "required") required @endif>

                                            </div>
                                            @error($k)
                                            <span class="text-danger">@lang($message)</span>
                                            @enderror
                                        </div>
                                        <img class="w-50 preview-image mt-2" id="image_preview_container"
                                             src="{{getFile(config('filelocation.default'))}}"
                                             alt="@lang('Upload Image')">
                                    </div>
                                @endif
                            @endforeach
                        @endif
                        <div class="col-md-12 ">
                            <div class=" form-group">
                                <button type="submit" class="btn-custom cmn-btn w-100 mt-3 py-2">@lang('Confirm Now')
                                    <span></span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('script')
    <script>
        'use strict'
        $(document).on("change",'#image',function () {
                let reader = new FileReader();
                reader.onload = (e) => {
                    $('#image_preview_container').attr('src', e.target.result);
                }
                reader.readAsDataURL(this.files[0]);
            });
    </script>
@endpush
