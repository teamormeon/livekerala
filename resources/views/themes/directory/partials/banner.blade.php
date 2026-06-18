
@if(isset($pageSeo) && $pageSeo['breadcrumb_image'])
    <div class="banner-section" style="background-image: url({{ $pageSeo['breadcrumb_image'] }});">
        <div class="banner-section-inner">
            <div class="container">
                <div class="row">
                    <div class="col">
                        <div class="breadcrumb-area">
                            <h3>@lang($pageSeo['page_title'])</h3>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ url('/') }}"><i class="fa-light fa-house"></i> @lang('Home')</a></li>
                                <li class="breadcrumb-item active" aria-current="page">@lang($pageSeo['page_title'])</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
