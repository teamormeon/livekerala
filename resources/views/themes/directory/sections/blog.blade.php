<!-- blog section -->
@if (count($blog['popularBlogs']) > 0)
    <section class="blog-section">
        <div class="container">
            @if(isset($blog['single']))
                <div class="row">
                    <div class="col-12">
                        <div class="section-header text-center mb-50">
                            <div class="section-subtitle">@lang($blog['single']['title'])</div>
                            <h3 class="section-title mx-auto">@lang($blog['single']['sub_title'])</h3>
                            <p class="cmn-para-text mx-auto">
                                @lang($blog['single']['description'])
                            </p>
                        </div>
                    </div>
                </div>
            @endif
            <div class="row g-4 justify-content-center">
                @foreach ($blog['popularBlogs'] as $item)
                    <div class="col-lg-4 col-md-6">
                        <div class="blog-box">
                            <a href="{{route('blog.details',optional($item->details)->slug)}}">
                                <div class="img-box">
                                    <img src="{{ getFile($item->blog_image_driver, $item->blog_image) }}" alt="image">
                                    <span class="category">@lang(optional($item->category)->name)</span>
                                </div>
                            </a>
                            <div class="content-box">

                                <ul class="meta">
                                    <li class="item">
                                        <a href="javascript:void(0)"><span class="icon"><i class="fa-regular fa-user"></i></span>
                                            <span>@lang(optional($item->details)->author)</span></a>
                                    </li>
                                    <li class="item">
                                        <span class="icon"><i class="fa-light fa-calendar-days"></i></span>
                                        <span>{{ dateTime($item->created_at) }}</span>
                                    </li>
                                </ul>
                                <h5 class="blog-title"><a class="border-effect" href="{{route('blog.details',optional($item->details)->slug)}}">
                                        @lang(optional($item->details)->title)</a>
                                </h5>
                                <div class="para-text">
                                    <p>
                                        {!! optional($item->details)->description !!}
                                    </p>
                                </div>

                                <a href="{{route('blog.details',optional($item->details)->slug)}}" class="blog-btn">
                                    @lang('learn more')
                                    <i class="fa-regular fa-angle-right"></i>
                                </a>
                            </div>

                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="shape2"></div>
    </section>
@endif
