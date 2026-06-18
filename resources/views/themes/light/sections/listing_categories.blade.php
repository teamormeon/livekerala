
@if(isset($listing_categories['popularCategories']) && $listing_categories['popularCategories']->isNotEmpty())
    <section class="category-section">
        <div class="container">
            @if(isset($listing_categories['single']))
                <div class="row">
                    <div class="col-12">
                        <div class="header-text text-center mb-5">
                            <h5>@lang($listing_categories['single']['title'])</h5>
                            <h3>@lang($listing_categories['single']['sub_title'])</h3>
                            <p class="mx-auto">
                                @lang($listing_categories['single']['description'])
                            </p>
                        </div>
                    </div>
                </div>
            @endif
            <div class="row g-3 g-lg-4">
                @forelse($listing_categories['popularCategories'] as $category)
                    <div class="col-xl-3 col-md-6 col-6">
                        <a href="{{ route('listings', $category->id) }}">
                            <div class="category-box">
                                <div class="icon-box">
                                    <i class="{{ $category->icon }}"></i>
                                </div>
                                <div>
                                    <h5>@lang(optional($category->details)->name)</h5>
                                    <span>{{ $category->getCategoryCount() }} @lang('Listings')</span>
                                </div>
                            </div>
                        </a>
                    </div>
                @empty
                @endforelse
            </div>
        </div>
    </section>

@endif
