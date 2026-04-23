
@if(isset($faq))
    <section class="faq-section">
        <img class="shape2" src="{{ asset(template(true).'img/background/net-shape.png') }}" alt="shape">
        <img class="shape3" src="{{ asset(template(true).'img/background/net-left.png') }}" alt="shape">
        <div class="container">
            <div class="row">
                <div class="section-header text-center">
                    <div class="section-subtitle">@lang($faq['single']['title'])</div>
                    <h3 class="section-title mx-auto">@lang($faq['single']['sub_title'])</h3>
                    <p class="cmn-para-text mx-auto">@lang($faq['single']['description'])</p>
                </div>
            </div>
            <div class="row g-2">
                @foreach($faq['multiple'] as $key => $item)
                    <div class="col-md-6">
                        <div class="faq-content">
                            <div class="accordion" id="accordionExample">
                                <div class="accordion-item mb-0">
                                    <h2 class="accordion-header" id="heading{{$key}}">
                                        <button class="accordion-button {{ $key > 1 ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#collapse{{$key}}" aria-expanded="{{ $key < 2 ? 'true' : 'false' }}"
                                                aria-controls="collapse{{$key}}">
                                            @lang($item['question'])
                                            <span class="icon ms-auto"></span>
                                        </button>
                                    </h2>
                                    <div id="collapse{{$key}}" class="accordion-collapse collapse {{ $key < 2 ? 'show' : '' }}"
                                         aria-labelledby="heading{{$key}}">
                                        <div class="accordion-body">
                                            <div class="table-responsive">
                                                <p>
                                                    @lang($item['answer'])
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endif
