@extends(template() . 'layouts.app')
@section('title',trans('Blog-Details'))
@section('content')
    <section class="blog-section blog-page">
        @if(!empty($blogDetails))
            <div class="container">
                <div class="row g-lg-5">
                    <div class="col-lg-8">
                        <div class="blog-box">
                            <div class="img-box">
                                <img class="img-fluid w-100" src="{{ getFile(optional($blogDetails->blog)->blog_image_driver,optional($blogDetails->blog)->blog_image) }}" alt="image"/>
                                <span class="category">@lang(optional(optional($blogDetails->blog)->category)->name)</span>
                            </div>

                            <div class="text-box">
                                <div class="date-author mb-3">
                               <span class="author">
                                  <i class="fad fa-pencil"></i>@lang($blogDetails->author ?? '')
                               </span>
                                    <span class="float-end">
                                    <i class="fad fa-calendar-alt" aria-hidden="true"></i>{{ dateTime($blogDetails->created_at, 'M d, Y') }}
                               </span>
                                </div>
                                <h5>
                                    @lang($blogDetails->title ?? '')
                                </h5>
                                <p>
                                    @lang($blogDetails->description ?? '')
                                </p>
                            </div>
                        </div>
                        <div class="social-share-box">
                            <h4 class="title">@lang('Share') :</h4>
                            <div id="shareBlock"></div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="right-bar">
                            <div class="side-box">
                                <form action="{{ route('blogs') }}" method="get">
                                    <h4>@lang('Search')</h4>
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="search" id="search"
                                               placeholder="@lang('search')" value="{{ request('search') }}"/>
                                        <button type="submit"><i class="fal fa-search"></i></button>
                                    </div>
                                </form>
                            </div>

                            @if(count($blogCategories) > 0)
                                <div class="side-box">
                                    <h4>@lang('Categories')</h4>
                                    <ul class="links">
                                        @foreach ($blogCategories as $category)
                                            @if($category->blogs_count)
                                                <li class="d-flex justify-content-between">
                                                    <a href="{{ route('blogs', ['category'=>$category->slug]) }}">
                                                        @lang($category->name)
                                                    </a>
                                                    <a href="javascript:void(0)">({{ $category->blogs_count }})</a>
                                                </li>
                                            @endif
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            @if (count($relatedBlogs) > 0)
                                <div class="side-box">
                                    <h4>@lang('Related Blogs')</h4>
                                    @foreach ($relatedBlogs as $blog)
                                        <div class="blog-box">
                                            <div class="img-box">
                                                <img class="img-fluid"
                                                     src="{{ getFile($blog->blog_image_driver, $blog->blog_image) }}"
                                                     alt="@lang('blog')"/>

                                                <span class="category">@lang(optional($blog->category)->name)</span>
                                            </div>
                                            <div class="text-box">
                                                <a href="{{ route('blog.details', ['slug' => optional($blog->details)->slug]) }}"
                                                   class="title">{{ Str::limit(optional($blog->details)->title, 40) }}</a>
                                            </div>
                                        </div>
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
