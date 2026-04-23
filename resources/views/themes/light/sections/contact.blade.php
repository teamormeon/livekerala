
@if(isset($contact))
    <div class="contact-section">
        <div class="container">
            <div class="row gy-5 g-lg-5 align-items-top">
                <div class="col-lg-6">
                    <form class="contact-form" action="{{ route('contact.send') }}" method="post">
                        @csrf
                        <div class="header-text">
                            <h5>@lang('Contact Us')</h5>
                            <h3>@lang($contact['single']['left_heading'])</h3>
                            <p>
                                @lang($contact['single']['left_details'])
                            </p>
                        </div>
                        <div class="row g-3">
                            <div class="input-box col-md-6">
                                <input class="form-control" name="name" value="{{ old('name') }}" type="text"
                                       placeholder="@lang('Full name')"/>
                                @error('name')
                                <span class="text-danger">{{$message}}</span>
                                @enderror
                            </div>

                            <div class="input-box col-md-6">
                                <input class="form-control" name="email" value="{{ old('email') }}" type="email"
                                       placeholder="@lang('Email address')"/>
                                @error('email')
                                <span class="text-danger">{{$message}}</span>
                                @enderror
                            </div>

                            <div class="input-box col-12">
                                <input class="form-control" type="text" name="subject" value="{{ old('subject') }}"
                                       placeholder="@lang('Subject')">
                                @error('subject')
                                <span class="text-danger">{{$message}}</span>
                                @enderror
                            </div>

                            <div class="input-box col-12">
                                <textarea class="form-control" cols="30" rows="5" name="message"
                                          placeholder="@lang('Message')">{{old('message')}}</textarea>
                                @error('message')
                                <span class="text-danger">{{$message}}</span>
                                @enderror
                            </div>

                            <div class="input-box col-12">
                                <button class="btn-custom">@lang('submit')</button>
                            </div>

                        </div>
                    </form>
                </div>
                <div class="col-lg-1"></div>
                <div class="col-lg-5">
                    <div class="text-box">
                        <div class="header-text"></div>
                        <div class="header-text">
                            <h3>@lang($contact['single']['right_heading'])</h3>
                            <p>
                                @lang($contact['single']['right_details'])
                            </p>
                        </div>
                        <div class="row">
                            <div class="info-box col-md-12">
                                <div class="icon-box">
                                    <i class="fal fa-map-marker-alt"></i>
                                </div>
                                <div class="text">
                                    <h5>@lang('Address')</h5>
                                    <p>
                                        @lang($contact['single']['address'])
                                    </p>
                                </div>
                            </div>
                            <div class="info-box col-md-12">
                                <div class="icon-box">
                                    <i class="fal fa-envelope"></i>
                                </div>
                                <div class="text">
                                    <h5>@lang('Email')</h5>
                                    <p>@lang($contact['single']['email'])</p>
                                </div>
                            </div>
                            <div class="info-box col-md-12">
                                <div class="icon-box">
                                    <i class="fal fa-phone-alt"></i>
                                </div>
                                <div class="text">
                                    <h5>@lang('Phone')</h5>
                                    <p>@lang($contact['single']['phone'])</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
