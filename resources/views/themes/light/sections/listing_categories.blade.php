
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
            <div class="category-directory-grid">
                @forelse($listing_categories['popularCategories'] as $category)
                    @php
                        $categoryName = \Illuminate\Support\Str::lower(optional($category->details)->name ?? '');

                        $categoryIconMap = [
                            [['hospital', 'clinic', 'dental', 'medical', 'health', 'ayurvedic'], 'fas fa-hospital'],
                            [['hotel', 'resort', 'homestay', 'restaurant'], 'fas fa-hotel'],
                            [['food', 'catering', 'bakery'], 'fas fa-utensils'],
                            [['website', 'software', 'computer', 'technology', 'web design'], 'fas fa-laptop-code'],
                            [['digital marketing', 'advertising', 'promotion'], 'fas fa-bullhorn'],
                            [['education', 'school', 'college', 'training', 'tuition'], 'fas fa-graduation-cap'],
                            [['builder', 'construction', 'architect', 'contractor'], 'fas fa-hard-hat'],
                            [['real estate', 'property'], 'fas fa-building'],
                            [['travel', 'tour', 'taxi', 'transport'], 'fas fa-route'],
                            [['event', 'wedding', 'convention', 'auditorium'], 'fas fa-calendar-star'],
                            [['beauty', 'salon', 'spa'], 'fas fa-spa'],
                            [['electrical', 'electronic', 'appliance'], 'fas fa-plug'],
                            [['repair', 'maintenance'], 'fas fa-tools'],
                            [['solar', 'energy'], 'fas fa-solar-panel'],
                            [['courier', 'delivery', 'logistics'], 'fas fa-shipping-fast'],
                            [['interior', 'furniture', 'home decor'], 'fas fa-couch'],
                            [['music', 'dance'], 'fas fa-music'],
                            [['photo', 'video'], 'fas fa-camera'],
                            [['finance', 'bank', 'insurance'], 'fas fa-landmark'],
                            [['automobile', 'car', 'bike'], 'fas fa-car'],
                            [['modular kitchen', 'kitchen'], 'fas fa-utensils'],
                            [['supermarket', 'grocery', 'store'], 'fas fa-shopping-cart'],
                            [['pharmacy', 'medicine'], 'fas fa-pills'],
                            [['theme park', 'amusement'], 'fas fa-ticket-alt'],
                            [['water purifier', 'water'], 'fas fa-tint'],
                            [['spice'], 'fas fa-pepper-hot'],
                            [['commercial', 'commerce'], 'fas fa-briefcase'],
                            [['service'], 'fas fa-concierge-bell'],
                        ];

                        $categoryIcon = $category->icon ?: 'fas fa-folder-open';

                        if (\Illuminate\Support\Str::contains($categoryIcon, 'folder')) {
                            foreach ($categoryIconMap as [$keywords, $mappedIcon]) {
                                if (\Illuminate\Support\Str::contains($categoryName, $keywords)) {
                                    $categoryIcon = $mappedIcon;
                                    break;
                                }
                            }
                        }
                    @endphp
                    <a class="category-directory-item"
                       href="{{ route('listings', \Illuminate\Support\Str::slug(optional($category->details)->name)) }}">
                        <div class="category-box">
                            <div class="icon-box">
                                <i class="{{ $categoryIcon }}"></i>
                            </div>
                            <h5>@lang(optional($category->details)->name)</h5>
                            <span>{{ $category->getCategoryCount() }} @lang('Listings')</span>
                        </div>
                    </a>
                @empty
                @endforelse

                <a class="category-directory-item category-view-all"
                   href="{{ route('listings') }}">
                    <div class="category-box">
                        <div class="view-all-arrow">
                            <i class="far fa-arrow-up"></i>
                        </div>
                        <strong>@lang('View All')</strong>
                        <span>@lang('Categories')</span>
                    </div>
                </a>
            </div>
        </div>
    </section>

@endif
