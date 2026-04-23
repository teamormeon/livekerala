<!-- Cookie Alert -->

<div id="cookieAlert" class="cookie-content d-none">
    <div class="content">
        <div class="d-flex align-items-center">
            <img class="cookie-img" src="{{ getFile(basicControl()->cookie_image_driver,basicControl()->cookie_image ) }}" alt="image">
            <h5 class="ps-2 mb-0 title cookie-title">@lang(basicControl()->cookie_title)</h5>
        </div>
        <p>
            @lang(Str::limit(basicControl()->cookie_description, 262))
            <a href="{{ route('cookie-policy') }}" class="text--base">@lang('Cookie Policy')</a>
        </p>
        <div class="cookie-btns">
            <a href="javascript:void(0)" class="close-btn" id="cookie-deny">@lang('Decline')</a>
            <a href="javascript:void(0)" class="cmn--btn btn-sm btn--success btn-custom" id="cookie-accept">@lang('Accept')</a>
        </div>
    </div>
</div>

@push('script')
    <script>
        'use strict'
        if (localStorage.getItem('cookie-value') == 1 || sessionStorage.getItem('cookie-value') == 1) {
            $('.cookie-content').remove();
        } else {
            $('.cookie-content').removeClass('d-none');
        }

        $('#cookie-accept').on("click", function () {
            localStorage.setItem('cookie-value', 1);
            sessionStorage.removeItem('cookie-value');
            $('.cookie-content').remove();
        });

        $('#cookie-deny').on("click", function () {
            sessionStorage.setItem('cookie-value', 1);
            localStorage.removeItem('cookie-value');
            $('.cookie-content').remove();
        });
    </script>
@endpush
