
@if($news_letter)
    <section class="newsletter-section" id="subscribe">
        <div class="overlay">
            <div class="container">
                <div class="row">
                    <div class="col text-center">
                        <h3>@lang($news_letter['single']['title'])</h3>
                        <p>
                            @lang($news_letter['single']['description'])
                        </p>
                        <form action="{{route('subscribe')}}" method="post">
                            @csrf
                            <div class="input-group mt-5">
                                <input type="email" name="email" class="form-control" placeholder="@lang('Enter Email Address')" aria-label="Subscribe Newsletter" aria-describedby="basic-addon"/>
                                <button type="submit" class="btn-custom">@lang('subscribe')</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endif
