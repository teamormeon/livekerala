
@if(isset($testimonial))
    <!-- testimonial section -->
    <section class="testimonial-section">
        <div class="container">
            <div class="row g-4 gx-xxl-5 align-items-center">
                <div class="col-md-4">
                    <div class="section-subtitle">@lang($testimonial['single']['heading'])</div>
                    <h3 class="section-title">@lang($testimonial['single']['sub_heading'])</h3>
                    <p class="cmn-para-text">@lang($testimonial['single']['description'])</p>
                </div>
                <div class="col-md-8">
                    <div class="owl-carousel owl-theme testimonial-carousel">
                        @if(isset($testimonial['multiple']))
                            @foreach($testimonial['multiple'] as $data)
                                <div class="item">
                                    <div class="testimonial-box">
                                        <ul class="reviews">
                                            <li>
                                                @for($i = 1; $i <= 5; $i++)
                                                    @if($i <= $data['rating'])
                                                        <i class="fas fa-star"></i>
                                                    @else
                                                        <i class="far fa-star"></i>
                                                    @endif
                                                @endfor
                                            </li>
                                        </ul>
                                        <div class="quote-area">
                                            <p>@lang($data['description'])</p>
                                        </div>
                                        <div class="profile-box">
                                            <div class="profile-thumbs">
                                                <img src="{{ getFile($data['media']->image->driver, $data['media']->image->path) }}" alt="image" />
                                            </div>
                                            <div class="profile-title">
                                                <h6 class="mb-0">@lang($data['name'])</h6>
                                                <p class="mb-0">@lang($data['address'])</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
@endif
