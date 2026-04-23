<section class="faq-section faq-page">
    <div class="container">
        @if(isset($faq))
            <div class="row g-4 gy-5 justify-content-center ">
                <div class="col-lg-8">
                    <div class="accordion" id="accordionExample">
                        @foreach(collect($faq['multiple'])->toArray() as  $key => $item)
                            <div class="accordion-item">
                                <h5 class="accordion-header" id="heading{{ $key }}">
                                    <button class="accordion-button {{ $key == 0 ? '' : 'collapsed' }}"
                                            type="button" data-bs-toggle="collapse"
                                            data-bs-target="#collapse{{ $key }}" aria-expanded="{{ $key == 0 ? 'true' : 'false' }}"
                                            aria-controls="collapse{{ $key }}">@lang($item['question'])
                                    </button>
                                </h5>
                                <div id="collapse{{ $key }}"
                                     class="accordion-collapse collapse {{ $key == 0 ? 'show' : '' }}"
                                     aria-labelledby="heading{{ $key }}" data-bs-parent="#accordionExample">
                                    <div class="accordion-body">
                                        @lang($item['answer'])
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>
</section>
