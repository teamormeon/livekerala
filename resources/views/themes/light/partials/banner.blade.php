
@if(isset($pageSeo) && $pageSeo['breadcrumb_image'])
    <section class="banner-section" style="background-image: url({{ $pageSeo['breadcrumb_image'] }});">
        <div class="overlay">
            <div class="container">
                <div class="row">
                    <div class="col">
                        <div class="header-text text-center">
                            <h3>@lang($pageSeo['page_title'])</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endif
