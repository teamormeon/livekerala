@if(isset($how_it_work))
    <section class="how-it-works-section">
        <div class="container">
            <div class="row">
                <div class="section-header text-center mb-50">
                    <div class="section-subtitle"> @lang($how_it_work['single']['heading']) </div>
                    <h3 class="section-title mx-auto"> @lang($how_it_work['single']['sub_heading'])</h3>
                    <p class="cmn-para-text mx-auto">@lang($how_it_work['single']['description'])</p>
                </div>
            </div>
            <div class="working-steps row g-4 g-sm-5 justify-content-center">
                @foreach($how_it_work['multiple'] as $key => $item)
                    <div class="col-lg-4 col-md-6">
                        <div class="cmn-box">
                            <div class="icon-area">
                                <i class="{{ $item['fontawesome_icon_class'] }}"></i>
                                <div class="number">{{ $key + 1 }}</div>
                            </div>
                            <div class="text-area">
                                <h4 class="title">@lang($item['title']) </h4>
                                <p class="mb-0">@lang($item['description'])</p>
                            </div>
                        </div>
                    </div>
                @endforeach
                <div class="workflow-progress">
                    <div class="dot">
                        <img src="{{ asset(template(true).'img/how-it-works/dot.png') }}" alt="">
                    </div>
                    <div class="map-icon">
                        <img src="{{ asset(template(true).'img/how-it-works/location2.png') }}" alt="">
                    </div>
                </div>
            </div>

        </div>
    </section>
@endif

