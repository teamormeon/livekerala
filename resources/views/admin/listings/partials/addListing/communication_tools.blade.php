<div id="tab7" class="card add-listing-form">
{{--    @if($single_package_infos->is_whatsapp == 1)--}}
        <div class="card-body">
            <div class="row g-4">
                <div class="col-xl-12">
                    <h3 class="mb-3">@lang('Whatsapp Chat Control')</h3>
                    <div class="form">
                        <div class="basic-form p-4">
                            <div class="row g-3">
                                <div class="input-box col-md-6">
                                    <input
                                        class="form-control @error('whatsapp_number') is-invalid @enderror"
                                        type="text" name="whatsapp_number"
                                        value="{{ old('whatsapp_number') }}" placeholder="@lang('whatsapp number')"/>
                                    <div class="invalid-feedback">
                                        @error('whatsapp_number') @lang($message) @enderror
                                    </div>
                                </div>

                                <div class="input-box col-md-6">
                                    <input
                                        class="form-control @error('replies_text') is-invalid @enderror"
                                        type="text" name="replies_text"
                                        value="{{ old('replies_text') }}" placeholder="@lang('Typically replies within a day')"/>
                                    <div class="invalid-feedback">
                                        @error('replies_text') @lang($message) @enderror
                                    </div>
                                </div>
                                <div class="col-md-12 bg-white">
                                    <textarea class="form-control @error('body_text') is-invalid @enderror"
                                              name="body_text" id="body_text" rows="10"
                                              placeholder="@lang('body_text')">{{ old('body_text') }}</textarea>
                                    <div class="invalid-feedback">
                                        @error('body_text') @lang($message) @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
{{--    @endif--}}
</div>



