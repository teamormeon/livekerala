
@if(isset($news_letter))
    <div class="newsletter-section mb-50">
        <div class="container">
            <div class="newsletter-section-inner">
                <img class="shape" src="{{ asset(template(true).'img/background/bg-newsletter.png') }}" alt="image">
                <div class="row align-items-center g-4 g-sm-5">
                    <div class="col-lg-6 col-md-5">
                        <div class="content-area">
                            <p class="mb-0">@lang($news_letter['single']['title'])</p>
                            <h1 class="subscribe-normal-text">@lang($news_letter['single']['description'])</h1>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-7">
                        <form class="newsletter-form" action="{{route('subscribe')}}" method="post">
                            @csrf
                            <input type="email" name="email" class="form-control" placeholder="Enter your mail">
                            <button type="submit" class="subscribe-btn">@lang('subscribe')</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
