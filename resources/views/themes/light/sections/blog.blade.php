<!-- blog section -->
@if (!empty($blog['popularBlogs']) && count($blog['popularBlogs']) > 0)
    <section class="blog-section">
        <div class="container">
            @if(isset($blog['single']))
                <div class="row">
                    <div class="col-12">
                        <div class="header-text text-center mb-5">
                            <h5>@lang($blog['single']['title'])</h5>
                            <h3>@lang($blog['single']['sub_title'])</h3>