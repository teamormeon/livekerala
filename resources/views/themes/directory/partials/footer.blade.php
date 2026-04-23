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

<section class="footer-section">
    <!-- Newsletter section start -->
    <div class="newsletter-section mb-50">
        <div class="container">
            <div class="newsletter-section-inner">
                <img class="shape" src="{{ asset(template(true).'img/background/bg-newsletter.png') }}" alt="image">
                <div class="row align-items-center g-4 g-sm-5">
                    <div class="col-lg-6 col-md-5">
                        <div class="content-area">
                            <p class="mb-0">@lang($news_letter['description']->title)</p>
                            <h1 class="subscribe-normal-text">@lang($news_letter['description']->description)</h1>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-7">
                        <form class="newsletter-form" action="{{route('subscribe')}}" method="post">
                            @csrf
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="@lang('Enter your mail')">
                            <button type="submit" class="subscribe-btn">@lang('subscribe')</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Newsletter section end -->
    <div class="container">
        <div class="row gy-4 gy-sm-5">
            <div class="col-lg-4 col-sm-6">
                <div class="footer-widget">
                    <div class="widget-logo">
                        <a href="{{ url('/') }}">
                            <img class="logo" src="{{ getFile(basicControl()->admin_dark_mode_logo_driver,basicControl()->admin_dark_mode_logo) }}" alt="logo">
                        </a>
                    </div>
                    @if($contactSingle)
                        <p>@lang(strip_tags(optional($contactSingle->description)->footer_description))</p>
                    @endif
                    @if($contactMultiple)
                        <ul class="social-box mt-30">
                            @foreach($contactMultiple as $data)
                                <li><a href="{{ optional($data->description)->social_link }}" target="_blank"><i
                                            class="{{ optional($data->description)->fontawesome_social_icon_class }}"></i></a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
            <div class="col-lg-2 col-sm-6">
                <div class="footer-widget">
                    <h5 class="widget-title">@lang('Quick Links')</h5>
                    <ul>
                        @if(getFooterMenuData('useful_link') != null)
                            @foreach(getFooterMenuData('useful_link') as $list)
                                {!! $list !!}
                            @endforeach
                        @endif
                    </ul>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 pt-sm-0 pt-3 ps-lg-5">
                <div class="footer-widget">
                    <h5 class="widget-title">@lang('Support Links')</h5>
                    <ul>
                        @if(getFooterMenuData('support_link') != null)
                            @foreach(getFooterMenuData('support_link') as $list)
                                {!! $list !!}
                            @endforeach
                        @endif
                    </ul>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 pt-sm-0 pt-3">
                <div class="footer-widget">
                    <h5 class="widget-title">@lang('Contact Us')</h5>
                    <p class="contact-item"><i
                            class="fa-regular fa-location-dot"></i> @lang(optional($contactSingle->description)->address)
                    </p>
                    <p class="contact-item"><i
                            class="fa-regular fa-envelope"></i> @lang(optional($contactSingle->description)->email)</p>
                    <p class="contact-item"><i
                            class="fa-regular fa-phone"></i> @lang(optional($contactSingle->description)->phone)</p>
                </div>
            </div>
        </div>
        <hr class="cmn-hr">
        <!-- Copyright-area-start -->
        <div class="copyright-area">
            <div class="row gy-4">
                <div class="col-sm-6">
                    <p>@lang('Copyright') &copy; {{date('Y')}}
                        <a class="highlight" href="{{ url('/') }}">@lang(basicControl()->site_title)</a> @lang('All Rights Reserved')
                    </p>
                </div>
                <div class="col-sm-6">
                    <div class="language">
                        @foreach ($languages as $key => $lang)
                            <a href="{{route('language',$lang->short_name)}}" class="language {{ session()->get('lang') == $lang->short_name ? 'highlight' : '' }}">@lang($lang->name)</a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <!-- Copyright-area-end -->
    </div>
</section>


<div class="modal fade" id="addListingmodal" tabindex="-1" aria-labelledby="addListingmodal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="editModalLabel">@lang('Create Listing')</h4>
                <button type="button" class="cmn-btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fa-light fa-xmark" aria-hidden="true"></i>
                </button>
            </div>
            <form>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <div id="formModal2">
                                <label class="form-label">@lang('Package')</label>
                                <select class="modal-select2 form-control" name="package" id="package">
                                    <option selected disabled>@lang('Select Package')</option>
                                    @foreach($my_packages as $key => $package)
                                        @if(($package->no_of_listing > 0 || $package->no_of_listing == null) && ($package->expire_date == null ||  \Carbon\Carbon::now() <= \Carbon\Carbon::parse($package->expire_date)) && ($package->status == 1))
                                            <option value="{{ $package->id }}" data-listing="{{ $package->no_of_listing }}"
                                                    data-route="{{ route('user.addListing', $package->id) }}"
                                                    class="total_listing{{$package->id}}">@lang(optional(optional($package->get_package)->details)->title)
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                                <div class="invalid-feedback">
                                    @error('category_id') @lang($message) @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-12" id="noOfListing">
                            <label for="firstname" class="form-label">@lang('No. of available listing')</label>
                            <input type="text" class="form-control total_no_of_listing_field" readonly>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="cmn-btn3" data-bs-dismiss="modal">@lang('Close')</button>
                    <a href="javascript:void(0)"  class="cmn-btn addCreateListingRoute">@lang('Create')</a>
                </div>
            </form>
        </div>
    </div>
</div>

@push('script')
    <script>
        $(document).ready(function () {
            $('#package').on('change', function () {
                $('#noOfListing').removeClass('d-none');
                let package_id = $(this).val();
                let no_of_listing = $('.total_listing' + package_id).data('listing');
                if (no_of_listing) {
                    $('.total_no_of_listing_field').val(no_of_listing);
                } else {
                    $('.total_no_of_listing_field').val('Unlimited');
                }

                let route = $('.total_listing' + package_id).data('route');
                $('.addCreateListingRoute').attr('href', route)
            });
        });
    </script>
@endpush
