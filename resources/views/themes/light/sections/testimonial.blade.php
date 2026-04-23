
@if($testimonial)
    <!-- testimonial section -->
    <section class="testimonial-section">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="header-text text-center mb-5">
                        <h3>
                            @lang($testimonial['single']['heading'])
                        </h3>
                        <p class="mx-auto">
                            @lang($testimonial['single']['sub_heading'])
                        </p>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="testimonials owl-carousel">
                        @if(isset($testimonial['multiple']))
                            @foreach($testimonial['multiple'] as $data)
                                <div class="review-box">
                                    <div class="upper">
                                        <div class="img-box">
                                            <img src="{{ getFile($data['media']->image->driver, $data['media']->image->path) }}" alt="image"/>
                                        </div>
                                        <div class="client-info">
                                            <h5>@lang($data['name'])</h5>
                                            <span>{{ $data['designation'] }}</span>
                                        </div>
                                    </div>
                                    <p class="mb-0">
                                        @lang($data['description'])
                                    </p>
                                    <i class="fad fa-quote-right quote"></i>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
@endif
