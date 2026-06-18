@extends(template() . 'layouts.app')
@section('title',trans('Blog-Details'))
@section('content')
    <section class="blog-details-section">
        @if(!empty($blogDetails))
            <div class="container">
                <div class="row g-4 g-sm-5">
                    <div class="col-lg-7 order-2 order-lg-1">
                        <div class="blog-box-large">
                            <div class="thumbs-area">
                                <img src="{{ getFile(optional($blogDetails->blog)->blog_image_driver,optional($blogDetails->blog)->blog_image) }}" alt="image">
                            </div>
                            <div class="content-area mt-20">
                                <ul class="meta">
                                    <li class="item">
                                        <a href="javascript:void(0)"><span class="icon"><i class="fa-regular fa-user"></i></span>
                                            <span>@lang($blogDetails->author ?? '')</span></a>
                                    </li>

                                    <li class="item">
                                        <span class="icon"><i class="fa-regular fa-calendar-days"></i></span>
                                        <span>{{ dateTime($blogDetails->created_at) }}</span>
                                    </li>
                                </ul>
                                <h4 class="blog-title">@lang($blogDetails->title ?? '')</h4>

                                <div class="para-text">
                                    <p>@lang($blogDetails->description ?? '')</p>

                                </div>
                            </div>
                        </div>
                        <div class="social-share-box">
                            <h4 class="title">@lang('social share') :</h4>
                            <div id="shareBlock"></div>
                        </div>
                    </div>
                    <div class="col-lg-5 order-1 order-lg-2">
                        <div class="blog-sidebar">
                            <div class="sidebar-widget-area">
                                <div class="widget-title">
                                    <h4>@lang('Search')</h4>
                                </div>
                                <form action="{{ route('blogs') }}" method="get">
                                    <div class="search-box">
                                        <input type="text" class="form-control" name="search" id="search" value="{{ request('search') }}" placeholder="@lang('Search here')">
                                        <button type="submit" class="search-btn"><i class="far fa-search"></i></button>
                                    </div>
                                </form>
                            </div>

                            @if(count($blogCategories) > 0)
                                <div class="sidebar-widget-area">
                                    <div class="sidebar-categories-area">
                                        <div class="categories-header">
                                            <div class="widget-title">
                                                <h4>@lang('Categories')</h4>
                                            </div>
                                        </div>
                                        <ul class="categories-list">
                                            @foreach ($blogCategories as $category)
                                                @if($category->blogs_count > 0)
                                                    <li>
                                                        <a href="{{ route('blogs', ['category'=>$category->slug]) }}">
                                                            <span>@lang($category->name)</span> <span class="highlight">({{$category->blogs_count}})</span>
                                                        </a>
                                                    </li>
                                                @endif
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            @endif

                            @if (count($relatedBlogs) > 0)
                                <div class="sidebar-widget-area">
                                    <div class="widget-title">
                                        <h4>@lang('Recent Post')</h4>
                                    </div>
                                    @foreach ($relatedBlogs as $blog)
                                        <a href="{{ route('blog.details', ['slug' => optional($blog->details)->slug]) }}" class="sidebar-widget-item">
                                            <div class="image-area">
                                                <img src="{{ getFile($blog->blog_image_driver, $blog->blog_image) }}" alt="image">
                                            </div>
                                            <div class="content-area">
                                                <div class="title">@lang(optional($blog->details)->title)</div>
                                                <div class="widget-date">
                                                    <i class="fa-regular fa-calendar-days"></i> {{ dateTime($blog->created_at) }}
                                                </div>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </section>
@endsection


@push('extra-js')
    <script src="{{ asset(template(true).'js/socialSharing.js') }}"></script>
@endpush
