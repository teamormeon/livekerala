@php
    if (getTheme() != basicControl()->theme) {
        $data = footerData();
        $news_letter = $data['newsLetter'];
        $contactSingle = $data['contactSingle'];
        $contactMultiple = $data['contactMultiple'];
        $my_packages = $data['my_packages'];
        $languages = $data['languages'];
    }
@endphp
<!-- FOOTER -->
<footer class="footer-section">
    <div class="container">
        <div class="row">
            <div class="col-md-6 col-lg-3">
                <div class="footer-box">
                    <a class="navbar-brand" href="javascript:void(0)">
                        <img src="{{ getFile(basicControl()->logo_driver,basicControl()->logo) }}" alt="logo">
                    </a>

                    @if(isset($contactSingle))
                        <p>@lang(strip_tags(optional($contactSingle->description)->footer_description))</p>
                    @endif
                    @if(isset($contactMultiple))
                        <div class="social-links">
                            @foreach($contactMultiple as $data)
                                <a href="{{ optional($data->description)->social_link }}" target="_blank">
                                    <i class="{{ optional($data->description)->social_icon }}"></i>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
            <div class="col-md-6 col-lg-3 ps-lg-5">
                <div class="footer-box">
                    <h5>@lang('Quick Links')</h5>
                    <ul>
                        @if(getFooterMenuData('useful_link') != null)
                            @foreach(getFooterMenuData('useful_link') as $list)
                                {!! $list !!}
                            @endforeach
                        @endif
                    </ul>
                </div>
            </div>
            <div class="col-md-6 col-lg-3 ps-lg-5">
                <div class="footer-box">
                    <h5>@lang('OUR Services')</h5>
                    <ul>
                            @if(getFooterMenuData('support_link') != null)
                                @foreach(getFooterMenuData('support_link') as $list)
                                    {!! $list !!}
                                @endforeach
                            @endif
                    </ul>
                </div>
            </div>

            @if(isset($contactSingle))
                <div class="col-md-6 col-lg-3">
                    <div class="footer-box">
                        <h5>@lang(optional($contactSingle->description)->right_heading)</h5>
                        <ul>
                            <li>
                                <i class="far fa-phone-alt"></i>
                                <span>@lang(optional($contactSingle->description)->phone)</span>
                            </li>
                            <li>
                                <i class="far fa-envelope"></i>
                                <span>@lang(optional($contactSingle->description)->email)</span>
                            </li>
                            <li>
                                <i class="far fa-map-marker-alt"></i>
                                <span>@lang(optional($contactSingle->description)->address)</span>
                            </li>
                        </ul>
                    </div>
                </div>
            @endif
        </div>
        <div class="footer-bottom">
            <div class="row">
                <div class="col-md-6">
                    <p class="copyright">
                        @lang('Copyright') &copy; {{date('Y')}} <a href="{{ url('/') }}">@lang(basicControl()->site_title)</a> @lang('All Rights Reserved')
                    </p>
                </div>

                <div class="col-md-6 language">
                    @if(isset($languages))
                    @foreach ($languages as $key => $lang)
                        <a href="{{route('language',$lang->short_name)}}" class="{{ session()->get('lang') == $lang->short_name ? 'active' : '' }}">
                            <span class="flag-icon flag-icon-{{strtolower($key)}}"></span>@lang($lang->name)</a>
                    @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
</footer>
<!-- /FOOTER -->


