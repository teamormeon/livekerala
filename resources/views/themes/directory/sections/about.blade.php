@if(isset($about))
    <!-- ABOUT-US -->
    <section class="about-section">
        <div class="container">
            <div class="about-section-inner">
                <div class="row g-4 g-sm-5 align-items-center">
                    <div class="col-lg-6">
                        <div class="thumbs-area">
                            <div class="about-image">
                                <img src="{{ getFile($about['single']['media']->image->driver, $about['single']['media']->image->path) }}" alt="image">
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="about-content">
                            <span class="section-subtitle">@lang($about['single']['heading'])</span>
                            <h2 class="section-title">@lang($about['single']['title'])</h2>
                            <p>
                                {!! $about['single']['description'] !!}
                            </p>

                            <div class="btn-area mt-30">
                                <a href="{{ route('page','contact') }}" class="cmn-btn">@lang('contact us')</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endif


