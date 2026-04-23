@if($about)
    <!-- ABOUT-US -->
    <section class="about-section">
        <div class="container">
            <div class="row g-lg-5 gy-5 align-items-center">
                <div class="col-lg-5">
                    <div class="img-box">
                        <img src="{{getFile($about['single']['media']->image->driver, $about['single']['media']->image->path)}}" class="img-fluid rounded" alt="image"/>
                    </div>
                </div>

                <div class="col-lg-1 d-none d-lg-block"></div>
                <div class="col-lg-6">
                    <div class="text-box">
                        <div class="header-text">
                            <h5>@lang($about['single']['heading'])</h5>
                            <h3>@lang($about['single']['title'])</h3>
                        </div>
                        <div>
                            {!! $about['single']['description'] !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- /ABOUT-US -->
@endif


