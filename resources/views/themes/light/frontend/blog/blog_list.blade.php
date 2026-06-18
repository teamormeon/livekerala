@extends(template().'layouts.app')
@section('title', trans('Blogs'))


@section('content')
    <!-- BLOG -->
    @if (count($blogs) > 0)
        <section class="blog-section blog-page">
            <div class="container">
                <div class="row g-lg-5">
                    <div class="col-lg-8">
                        @forelse ($blogs as $blog)
                            <div class="blog-box">
                                <div class="img-box">
                                    <img class="img-fluid w-100" src="{{ getFile($blog->blog_image_driver, $blog->blog_image) }}" alt="blog"/>
                                    <span
                                        class="category"> @lang(optional($blog->category)->name)</span>
                                </div>
                                <div class="text-box">
                                    <div class="date-author mb-3">
                                         <span class="author">
                                            <i class="fad fa-pencil"></i>@lang(optional($blog->details)->author)
                                         </span>
                                        <span class="float-end">
                                            <i class="fad fa-calendar-alt" aria-hidden="true"></i>{{ dateTime($blog->created_at, 'M d, Y') }}
                                         </span>
                                    </div>

                                    <a href="{{ route('blog.details', ['slug' => optional($blog->details)->slug]) }}"
                                       class="title">{{Str::limit(optional($blog->details)->title, 100) }}
                                    </a>

                                    <p>
                                        {{ Str::limit(strip_tags(optional($blog->details)->description),500)}}
                                    </p>

                                    <a href="{{ route('blog.details', ['slug' => optional($blog->details)->slug]) }}"
                                       class="btn-custom">@lang('Read more')</a>
                                </div>
                            </div>
                        @empty
                        @endforelse
                    </div>

                    <div class="col-lg-4">
                        <div class="right-bar">
                            <div class="side-box">
                                <form action="{{ route('blogs') }}" method="get">
                                    <h4>@lang('Search')</h4>
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="search" id="search"
                                               placeholder="@lang('search')"  value="{{ request('search') }}"/>
                                        <button type="submit"><i class="fal fa-search"></i></button>
                                    </div>
                                </form>
                            </div>

                            @if(count($blogCategories) > 0)
                                <div class="side-box">
                                    <h4>@lang('Categories')</h4>
                                    <ul class="links">
                                        @foreach ($blogCategories as $category)
                                            @if($category->blogs_count > 0)
                                                <li class="d-flex justify-content-between">
                                                    <a href="{{ route('blogs', ['category'=>$category->slug]) }}">@lang($category->name)</a>
                                                    <a href="javascript:void(0)">({{ $category->blogs_count }})</a>
                                                </li>
                                            @endif
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <div class="side-box">
                                <h4>@lang('Recent Blogs')</h4>
                                @foreach ($blogs as $blog)
                                    <div class="blog-box">
                                        <div class="img-box">
                                            <img class="img-fluid" src="{{ getFile($blog->blog_image_driver, $blog->blog_image) }}" alt="blog"/>
                                            <span class="category">@lang(optional($blog->category)->name)</span>
                                        </div>
                                        <div class="text-box">
                                            <a href="{{ route('blog.details', ['slug' => optional($blog->details)->slug]) }}"
                                               class="title">{{ Str::limit(optional($blog->details)->title, 40) }}</a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-12 d-flex justify-content-center">
                        <nav aria-label="Page navigation example mt-3">
                            {{ $blogs->appends($_GET)->links(template().'partials.pagination') }}
                        </nav>
                    </div>
                </div>
            </div>
        </section>
    @else
        <div class="custom-not-found2">
            <img src="{{ asset(template(true).'img/no_data_found.png') }}" alt="image"
                 class="img-fluid">
        </div>
    @endif
@endsection



