@if(isset($how_it_work))
    <section class="how-it-works">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="header-text text-center mb-5">
                        <h5>@lang($how_it_work['single']['heading'])</h5>
                        <h3>@lang($how_it_work['single']['sub_heading'])</h3>
                    </div>
                </div>
            </div>
            <div class="row gy-5 gy-lg-0">
                @foreach($how_it_work['multiple'] as $item)
                    <div class="col-lg-4 col-md-6 mx-auto">
                        <div class="box">
                            <div class="icon-box">
                                <img src="{{ getFile($item['media']->image->driver, $item['media']->image->path) }}" alt="image" width="64"/>
                            </div>
                            <div>
                                <h5>@lang($item['title'])</h5>
                                <p>
                                    @lang($item['description'])
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endif

