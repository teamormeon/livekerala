
@if(isset($contact))
    <section class="contact-section">
        <div class="container">
            <div class="contact-inner">
                <div class="row g-4 justify-content-center">
                    <div class="col-xl-4 col-md-5">
                        <div class="contact-area">
                            <div class="thumbs-area">
                                <img class="" src="{{ getFile($contact['single']['media']->image->driver, $contact['single']['media']->image->path) }}" alt="image">
                            </div>
                            <div class="contact-item-list">
                                <div class="item">
                                    <div class="icon-area">
                                        <i class="fal fa-phone-alt"></i>
                                    </div>
                                    <div class="content-area">
                                        <h6 class="mb-0">@lang('Phone'):</h6>
                                        <p class="mb-0">@lang($contact['single']['phone'])</p>
                                    </div>
                                </div>
                                <div class="item">
                                    <div class="icon-area">
                                        <i class="fal fa-envelope"></i>
                                    </div>
                                    <div class="content-area">
                                        <h6 class="mb-0">@lang('Email'):</h6>
                                        <p class="mb-0">@lang($contact['single']['email'])</p>
                                    </div>
                                </div>
                                <div class="item">
                                    <div class="icon-area">
                                        <i class="fal fa-map-marker-alt"></i>
                                    </div>
                                    <div class="content-area">
                                        <h6 class="mb-0">@lang('Address'):</h6>
                                        <p class="mb-0">@lang($contact['single']['address'])</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-7 col-md-7">
                        <div class="contact-message-area">
                            <div class="contact-header">
                                <h3 class="section-title">@lang($contact['single']['title'])</h3>
                                <p>@lang($contact['single']['description'])</p>
                            </div>
                            <form action="{{ route('contact.send') }}" method="post">
                                @csrf
                                <div class="row">
                                    <div class="mb-3 col-md-6">
                                        <input type="text" name="name" value="{{ old('name') }}" class="form-control" placeholder="@lang('Your Name')">
                                        @error('name')
                                            <span class="text-danger">{{$message}}</span>
                                        @enderror
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <input type="email" name="email" value="{{ old('email') }}" class="form-control" placeholder="@lang('E-mail Address')">
                                        @error('email')
                                            <span class="text-danger">{{$message}}</span>
                                        @enderror
                                    </div>
                                    <div class="mb-3 col-md-12">
                                        <input type="text" name="subject" value="{{ old('subject') }}" class="form-control" placeholder="@lang('Your Subject')">
                                        @error('subject')
                                            <span class="text-danger">{{$message}}</span>
                                        @enderror
                                    </div>
                                    <div class="mb  -3 col-12">
                                        <textarea class="form-control" name="message" id="exampleFormControlTextarea1" rows="5"
                                                  placeholder="@lang('Your Massage')">{{ old('message') }}</textarea>
                                        @error('message')
                                            <span class="text-danger">{{$message}}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="btn-area d-flex justify-content-end">
                                    <button type="submit" class="cmn-btn w-100">@lang('Send a massage')</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endif
