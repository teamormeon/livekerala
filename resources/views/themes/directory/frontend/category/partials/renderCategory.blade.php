@foreach ($categories as $category)
    <div class="col-xl-3 col-lg-4 col-md-4 col-sm-6">
        <div class="category-box">
            <a href="{{ route('listings',$category->id) }}" class="icon-box">
                <i class="{{ $category->icon }}"></i>
            </a>
            <div class="content-area">
                <h5 class="title"><a href="{{ route('listings',$category->id) }}">@lang(optional($category->details)->name)</a></h5>
                <p class="mb-0">{{ $category->getCategoryCount() }} @lang('Listings')</p>
            </div>
            <div class="bg-icon">
                <i class="fa-regular fa-dumbbell"></i>
            </div>
        </div>
    </div>
@endforeach
