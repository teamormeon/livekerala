
@if(isset($listing_categories['popularCategories']) && $listing_categories['popularCategories']->isNotEmpty())
    <section class="category-section">
        <div class="container">
            @if(isset($listing_categories['single']))
                <div class="row">
                    <div class="col-12">
                        <div class="section-header text-center">
                            <div class="section-subtitle">@lang($listing_categories['single']['title'])</div>
                            <h3 class="mb-0">@lang($listing_categories['single']['sub_title'])</h3>
                            <p class="mx-auto">
                                @lang($listing_categories['single']['description'])
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <div class="row">
                <div class="col-12">
                    <div class="owl-carousel owl-theme category-slider">
                        @forelse($listing_categories['popularCategories'] as $category)
                            <div class="item">
                                <div class="category-box">
                                    <a href="{{ route('listings', $category->id) }}" class="icon-box">
                                        <i class="{{ $category->icon }}"></i>
                                    </a>
                                    <div class="content-area">
                                        <h5 class="title"><a href="{{ route('listings', $category->id) }}">@lang(\Illuminate\Support\Str::limit(optional($category->details)->name, 15))</a></h5>
                                        <p class="mb-0">{{ $category->getCategoryCount() }} @lang('Listings')</p>
                                    </div>
                                    <div class="bg-icon">
                                        <i class="{{ $category->icon }}"></i>
                                    </div>
                                </div>
                            </div>
                        @empty
                        @endforelse
                    </div>
                    <div class="btn-area text-center mt-30">
                        @lang('Browse All Different')
                        <a href="{{ route('category') }}" class="view-all-btn">@lang('Categories')</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endif
