@foreach ($categories as $category)
    <div class="col-lg-3 col-md-4 col-6 ">
        <a href="{{ route('listings',$category->id) }}">
            <div class="category-box d-flex justify-content-start">
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
@endforeach
