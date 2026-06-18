@extends(template().'layouts.app')
@section('title',trans('Cookie Policy'))

@section('banner_heading')
    @lang('Cookie Policy')
@endsection

@section('content')
    <section class="category-filter-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-12">
                    <div class="shadow rounded-3 p-5">
                        <h4> @lang(basicControl()->cookie_title)</h4>
                        <h6 class="card-title"><i>@lang(basicControl()->cookie_sub_title)</i></h6>
                        <p class="card-text">@lang(basicControl()->cookie_description)</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection



