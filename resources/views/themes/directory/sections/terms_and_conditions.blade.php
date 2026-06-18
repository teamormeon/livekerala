
@if(isset($terms_and_conditions))
    <section class="policy-section">
        <img class="shape2" src="{{ asset(template(true).'img/background/net-shape.png') }}" alt="image">
        <img class="shape3" src="{{ asset(template(true).'img/background/net-left.png') }}" alt="image">
        <div class="container">
            <div class="row">
                <div class="policy-section-inner">
                    <h3>@lang($terms_and_conditions['single']['title'])</h3>
                    <p>@lang($terms_and_conditions['single']['description'])</p>

                </div>
            </div>
        </div>
    </section>
@endif
