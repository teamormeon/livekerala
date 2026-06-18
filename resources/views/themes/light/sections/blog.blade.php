<!-- blog section -->
@if (count($blog['popularBlogs']) > 0)
    <section class="blog-section">
        <div class="container">
            @if(isset($blog['single']))
                <div class="row">
                    <div class="col-12">
                        <div class="header-text text-center mb-5">
                            <h5>@lang($blog['single']['title'])</h5>
                            <h3>@lang($blog['single']['sub_title'])</h3>
                            <p class="mx-auto">
                                @lang($blog['single']['description'])
                            </p>
                        </div>
                    </div>
                </div>
            @endif
            <div class="row gy-3 g-md-5">
                @foreach ($blog['popularBlogs'] as $item)
                    <div class="col-lg-4 col-md-6">
                        <div class="blog-box">
                            <div class="img-box">
                                <img class="img-fluid" src="{{ getFile($item->blog_image_driver, $item->blog_image) }}" alt="image"/>
                            </div>
                            <div class="text-box">
                                <span class="category">@lang(optional($item->category)->name)</span>
                                <a href="{{route('blog.details',optional($item->details)->slug)}}" class="title"
                                >{{ Str::limit(optional($item->details)->title, 80) }}
                                </a>
                                <div class="date-author">
                                    <span class="author"> @lang(optional($item->details)->author) </span>
                                    <span class="float-end">{{ dateTime($item->created_at) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="row text-center mt-5">
                <div class="col">
                    <a href="{{ route('blogs') }}" class="btn-custom">
                        @lang('Explore more')
                        <i class="fal fa-angle-double-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>
@endif
