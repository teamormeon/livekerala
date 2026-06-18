
@if($terms_and_conditions)
    <section class="blog-section blog-page">
        <div class="container">
            <div class="row g-lg-5">
                <div class="col-lg-12">
                    <div class="blog-box">
                        <div class="text-box">
                            <h4>@lang($terms_and_conditions['single']['title'])</h4>
                            <p>@lang($terms_and_conditions['single']['description'])</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endif
