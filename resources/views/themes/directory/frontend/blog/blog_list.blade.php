@extends(template().'layouts.app')
@section('title', trans('Blogs'))

@section('content')
    @if (count($blogs) > 0)
        <section class="blog-section">
            <div class="container">
                @if(isset($blogSingleContent))
                    <div class="row">
                        <div class="col-12">
                            <div class="section-header text-center mb-50">
                                <div class="section-subtitle">@lang($blogSingleContent['description']->title)</div>
                                <h3 class="section-title mx-auto">@lang($blogSingleContent['description']->sub_title)</h3>
                                <p class="cmn-para-text mx-auto">
                                    {!! $blogSingleContent['description']->description !!}
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
                <div class="row g-4">
                    @forelse ($blogs as $blog)
                        <div class="col-lg-4 col-md-6">
                            <div class="blog-box">
                                <a href="{{ route('blog.details', ['slug' => optional($blog->details)->slug]) }}">
                                    <div class="img-box">
                                        <img src="{{ getFile($blog->blog_image_driver, $blog->blog_image) }}" alt="image">
                                        <span class="category">@lang(optional($blog->category)->name)</span>
                                    </div>
                                </a>
                                <div class="content-box">
                                    <ul class="meta">
                                        <li class="item">
                                            <a href="javascript:void(0)"><span class="icon"><i class="fa-regular fa-user"></i></span>
                                                <span>@lang(optional($blog->details)->author)</span></a>
                                        </li>
                                        <li class="item">
                                            <span class="icon"><i class="fa-regular fa-calendar-days"></i></span>
                                            <span>{{ dateTime($blog->created_at) }}</span>
                                        </li>
                                    </ul>
                                    <h5 class="blog-title">
                                        <a class="border-effect" href="{{ route('blog.details', ['slug' => optional($blog->details)->slug]) }}">
                                            {{Str::limit(optional($blog->details)->title, 100) }}
                                        </a>
                                    </h5>
                                    <div class="para-text">
                                        <p>
                                            {!! Str::limit(strip_tags(optional($blog->details)->description),500) !!}
                                        </p>
                                    </div>

                                    <a href="{{ route('blog.details', ['slug' => optional($blog->details)->slug]) }}" class="blog-btn">
                                        @lang('learn more')
                                        <i class="fa-regular fa-angle-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                    @endforelse
                </div>
                <div class="col-lg-12 d-flex justify-content-center">
                    <nav aria-label="Page navigation example mt-3">
                        {{ $blogs->appends($_GET)->links(template().'partials.pagination') }}
                    </nav>
                </div>
            </div>
            <div class="shape2"></div>
        </section>
    @else
        <div class="custom-not-found">
            <img src="{{ asset(template(true).'img/error/error.png') }}" alt="image"
                 class="img-fluid">
        </div>
    @endif
@endsection



