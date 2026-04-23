@if(isset($hero))
    <div class="hero-section"
         style="background-image: url({{ getFile($hero['single']['media']->image->driver, $hero['single']['media']->image->path) }})">
        <div class="bg-overlay"></div>
        <div class="container">
            <div class="row g-3 g-lg-5 justify-content-between align-items-center">
                <div class="col-lg-10 mx-auto">
                    <div class="hero-content">
                        <h1 class="hero-title">@lang($hero['single']['title'])</h1>
                        <p class="hero-description">@lang($hero['single']['sub_title'])</p>
                    </div>
                </div>
            </div>

            <div class="multiple-search-section">
                <div class="row">
                    <div class="col-xl-10 mx-auto">
                        <div class="multiple-search-box-wrapper">
                            <form class="multiple-search-box" action="{{ route('listings') }}" method="get">
                                <div class="multiple-search-box-inner">
                                    <div class="input-box" id="input-box">
                                        <div class="icon">
                                            <i class="fa-regular fa-magnifying-glass"></i>
                                        </div>
                                        <input type="text" name="name" value="{{ old('name', request()->name) }}"
                                               id="search-input" class="form-control soValue search-input"
                                               placeholder="What are you looking for">
                                    </div>
                                    <div class="input-box" id="input-box2">
                                        <div class="icon">
                                            <i class="fa-sharp fa-regular fa-grid-2"></i>
                                        </div>
                                        <input type="text" id="search-input2" class="form-control"
                                               placeholder="Enter Category">
                                        <input type="hidden" name="category_id[]" id="search-input2-value">
                                        <div id="search-result2" class="search-result">
                                            @foreach($hero['all_categories'] as $category)
                                                @if($category!=null)
                                                <div class="search-item" data-id="{{ $category->id }}">
                                                    <div class="icon-area">
                                                        <i class="{{ $category->icon ?? 'fa-light fa-list' }}"></i>
                                                    </div>
                                                    <div class="text-area">
                                                        <div
                                                            class="title">@lang(optional($category->details)->name)</div>
                                                    </div>
                                                </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="input-box" id="input-box3">
                                        <div class="icon">
                                            <i class="fa-regular fa-globe"></i>
                                        </div>
                                        <input type="text" id="search-input3" class="form-control"
                                               placeholder="Enter Country">
                                        <input type="hidden" name="location" id="search-input3-value">
                                        <div id="search-result3" class="search-result">
                                            @foreach($hero['all_places'] as $place)
                                                @if($place!=null)
                                                <div class="search-item" data-id="{{ $place->id }}">
                                                    <div class="icon-area">
                                                        <i class="fa-light fa-location-dot"></i>
                                                    </div>
                                                    <div class="text-area">
                                                        <div class="title">@lang($place->name)</div>
                                                    </div>
                                                </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>

                                    <div class="input-box" id="input-box4">
                                        <div class="icon">
                                            <i class="fa-regular fa-city"></i>
                                        </div>
                                        <input type="text" id="search-input4" class="form-control"
                                               placeholder="Enter City">
                                        <input type="hidden" name="city" id="search-input4-value">
                                        <div id="search-result4" class="search-result">
                                            @foreach($hero['uniqueCities'] as $city)
                                                @if($city!=null)
                                                    <div class="search-item" data-id="{{ $city->id }}">
                                                        <div class="icon-area">
                                                            <i class="fa-light fa-location-dot"></i>
                                                        </div>
                                                        <div class="text-area">
                                                            <div class="title">@lang($city->name)</div>
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                    <button type="submit" class="multiple-search-btn">
                                        <span><i class="fa-regular fa-magnifying-glass"></i></span><span
                                            class="">@lang('search')</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="featured-item-continer">
                <p class="mb-0">@lang('Or browse the highlights'):</p>
                @foreach($hero['highlights_categories'] as $item)
                    <a href="{{ route('listings',$item->id) }}" class="item">
                        <div class="icon-area">
                            <i class="{{ $item->icon }}"></i>
                        </div>
                        <div class="content-area">
                            @lang(optional($item->details)->name)
                        </div>
                    </a>
                @endforeach
            </div>
        </div>

    </div>
@endif

<script src="{{asset('assets/global/js/jquery.min.js') }}"></script>

