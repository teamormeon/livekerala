<div id="tab7" class="card add-listing-form">
    <div class="card-body">
        @if($single_package_infos->is_messenger == 1)
            <div class="row gy-4">
                <div class="col-xl-6">
                    <h3 class="mb-3">@lang('FB Messenger Control')</h3>
                    <div class="form">
                        <div class="basic-form p-2">
                            <div class="row g-3">
                                <div class="input-box col-md-6">
                                    <input
                                        class="form-control @error('fb_app_id') is-invalid @enderror"
                                        type="text" name="fb_app_id"
                                        value="{{ old('fb_app_id',  $single_listing_infos->fb_app_id ) }}"
                                        placeholder="@lang('App Id')"/>
                                    <div class="invalid-feedback">
                                        @error('fb_app_id') @lang($message) @enderror
                                    </div>
                                </div>

                                <div class="input-box col-md-6">
                                    <input
                                        class="form-control @error('fb_page_id') is-invalid @enderror"
                                        type="text" name="fb_page_id"
                                        value="{{ old('fb_page_id',  $single_listing_infos->fb_page_id ) }}"
                                        placeholder="@lang('Page Id')"/>
                                    <div class="invalid-feedback">
                                        @error('fb_page_id') @lang($message) @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-6">
                    <h3 class="custom__opacity">@lang('test')</h3>
                    <div class="card card-primary shadow">
                        <div
                            class="card-header bg-primary text-white py-3 d-flex flex-row align-items-center justify-content-between">
                            <h5 class="m-0 font-weight-bold text-white">@lang('Instructions')</h5>
                            <div class="col-md-6 d-flex justify-content-end">
                                <a href="https://www.youtube.com/watch?v=MQszEDuWFeQ" target="_blank"
                                   class="btn btn-dark btn-sm text-white float-right " type="button">
                                    <span class="btn-label"><i class="fab fa-youtube"></i></span>
                                    @lang('How to set up it?')
                                </a>
                            </div>
                        </div>

                        <div class="card-body">
                            Step One: Visit The Facebook Developers Page. To start with, navigate your browser to the Facebook Developers page. ...
                            Step Three: Add Products In Your App. Now you have to add “Facebook Login” product in your app. ...
                            Step Four: Set Up Your Product. ...
                            Step Five: Make Your App Live.
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if($single_package_infos->is_whatsapp == 1)
            <div class="row gy-4">
                <div class="col-xl-12">
                    <h3 class="mb-0 mt-3">@lang('Whatsapp Chat Control')</h3>
                    <div class="form">
                        <div class="basic-form p-2">
                            <div class="row g-3">
                                <div class="input-box col-md-6">
                                    <input
                                        class="form-control @error('whatsapp_number') is-invalid @enderror"
                                        type="text" name="whatsapp_number"
                                        value="{{ old('whatsapp_number',  $single_listing_infos->whatsapp_number ) }}" placeholder="@lang('whatsapp number')"/>
                                    <div class="invalid-feedback">
                                        @error('whatsapp_number') @lang($message) @enderror
                                    </div>
                                </div>

                                <div class="input-box col-md-6">
                                    <input
                                        class="form-control @error('replies_text') is-invalid @enderror"
                                        type="text" name="replies_text"
                                        value="{{ old('replies_text', $single_listing_infos->replies_text ) }}" placeholder="@lang('Typically replies within a day')"/>
                                    <div class="invalid-feedback">
                                        @error('replies_text') @lang($message) @enderror
                                    </div>
                                </div>

                                <div class="input-box col-md-12">
                                    <textarea class="form-control summernote @error('body_text') is-invalid @enderror" name="body_text" id="body_text" rows="15" value="{{ old('body_text', $single_listing_infos->body_text ) }}">{{ old('body_text', $single_listing_infos->body_text) }}</textarea>

                                    <div class="invalid-feedback">
                                        @error('body_text') @lang($message) @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>



