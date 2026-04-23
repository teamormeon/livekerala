<!-- Navbar Vertical -->
<aside
    class="js-navbar-vertical-aside navbar navbar-vertical-aside navbar-vertical navbar-vertical-fixed navbar-expand-xl navbar-vertical-aside-initialized
    {{in_array(session()->get('themeMode'), [null, 'auto'] )?  'navbar-dark bg-dark ' : 'navbar-light bg-white'}}">
    <div class="navbar-vertical-container">
        <div class="navbar-vertical-footer-offset">
            <!-- Logo -->
            <a class="navbar-brand" href="{{ route('admin.dashboard') }}" aria-label="{{ $basicControl->site_title }}">
                <img class="navbar-brand-logo navbar-brand-logo-auto"
                     src="{{ getFile(session()->get('themeMode') == 'auto'?$basicControl->admin_dark_mode_logo_driver : $basicControl->admin_logo_driver, session()->get('themeMode') == 'auto'?$basicControl->admin_dark_mode_logo:$basicControl->admin_logo, true) }}"
                     alt="{{ $basicControl->site_title }} Logo"
                     data-hs-theme-appearance="default">

                <img class="navbar-brand-logo"
                     src="{{ getFile($basicControl->admin_dark_mode_logo_driver, $basicControl->admin_dark_mode_logo, true) }}"
                     alt="{{ $basicControl->site_title }} Logo"
                     data-hs-theme-appearance="dark">

                <img class="navbar-brand-logo-mini"
                     src="{{ getFile($basicControl->favicon_driver, $basicControl->favicon, true) }}"
                     alt="{{ $basicControl->site_title }} Logo"
                     data-hs-theme-appearance="default">
                <img class="navbar-brand-logo-mini"
                     src="{{ getFile($basicControl->favicon_driver, $basicControl->favicon, true) }}"
                     alt="Logo"
                     data-hs-theme-appearance="dark">
            </a>
            <!-- End Logo -->

            <!-- Navbar Vertical Toggle -->
            <button type="button" class="js-navbar-vertical-aside-toggle-invoker navbar-aside-toggler">
                <i class="bi-arrow-bar-left navbar-toggler-short-align"
                   data-bs-template='<div class="tooltip d-none d-md-block" role="tooltip"><div class="arrow"></div><div class="tooltip-inner"></div></div>'
                   data-bs-toggle="tooltip"
                   data-bs-placement="right"
                   title="Collapse">
                </i>
                <i
                    class="bi-arrow-bar-right navbar-toggler-full-align"
                    data-bs-template='<div class="tooltip d-none d-md-block" role="tooltip"><div class="arrow"></div><div class="tooltip-inner"></div></div>'
                    data-bs-toggle="tooltip"
                    data-bs-placement="right"
                    title="Expand"
                ></i>
            </button>
            <!-- End Navbar Vertical Toggle -->


            <!-- Content -->
            <div class="navbar-vertical-content">
                <div id="navbarVerticalMenu" class="nav nav-pills nav-vertical card-navbar-nav">
                    @if(adminAccessRoute(config('role.dashboard.access.view')))
                        <div class="nav-item">
                            <a class="nav-link {{ menuActive(['admin.dashboard']) }}"
                               href="{{ route('admin.dashboard') }}">
                                <i class="bi-house-door nav-icon"></i>
                                <span class="nav-link-title">@lang("Dashboard")</span>
                            </a>
                        </div>
                    @endif


                    @if(adminAccessRoute(config('role.manage_package.access.view')) || adminAccessRoute(config('role.purchase_package.access.view')))
                        <span class="dropdown-header mt-3">@lang('Manage Package')</span>
                        <small class="bi-three-dots nav-subtitle-replacer"></small>
                        @if(adminAccessRoute(config('role.manage_package.access.view')))
                            <div class="nav-item">
                                <a class="nav-link {{ menuActive(['admin.package','admin.package.create','admin.package.edit']) }}"
                                   href="{{ route('admin.package') }}" data-placement="left">
                                    <i class="fa-light fa-list nav-icon"></i>
                                    <span class="nav-link-title">@lang("Package List")</span>
                                </a>
                            </div>
                        @endif

                        @if(adminAccessRoute(config('role.purchase_package.access.view')))
                            <div class="nav-item">
                                <a class="nav-link {{ menuActive(['admin.purchase.package']) }}"
                                   href="{{ route('admin.purchase.package') }}" data-placement="left">
                                    <i class="fa-light fa-list nav-icon"></i>
                                    <span class="nav-link-title">@lang("Purchase History")</span>
                                </a>
                            </div>
                        @endif
                    @endif

                    @if(adminAccessRoute(config('role.listing_category.access.view')) || adminAccessRoute(config('role.manage_listing.access.view'))
                     || adminAccessRoute(config('role.listing_wishlist.access.view')) || adminAccessRoute(config('role.listing_analytics.access.view'))
                     || adminAccessRoute(config('role.manage_listing.access.edit')))
                        <span class="dropdown-header mt-3">@lang('Manage Listing')</span>
                        <small class="bi-three-dots nav-subtitle-replacer"></small>

                        @if(adminAccessRoute(config('role.listing_category.access.view')))
                            <div class="nav-item">
                                <a class="nav-link {{ menuActive(['admin.listing.category','admin.listing.category.create','admin.listing.category.edit']) }}"
                                   href="{{ route('admin.listing.category') }}" data-placement="left">
                                    <i class="fa fa-crosshairs nav-icon"></i>
                                    <span class="nav-link-title">@lang("Category")</span>
                                </a>
                            </div>
                        @endif

                        @if(adminAccessRoute(config('role.manage_listing.access.view')))
                            <div class="nav-item">
                                <a class="nav-link {{ menuActive(['admin.listings', 'admin.listing.create', 'admin.listing.edit','admin.listing.reviews','admin.listing.single.analytics','admin.listing.form.data']) }}"
                                   href="{{ route('admin.listings') }}" data-placement="left">
                                    <i class="bi bi-list-ol nav-icon"></i>
                                    <span class="nav-link-title">@lang("User Listings")</span>
                                </a>
                            </div>
                        @endif


                        @if(adminAccessRoute(config('role.listing_wishlist.access.view')))
                            <div class="nav-item">
                                <a class="nav-link {{ menuActive(['admin.wishList']) }}"
                                   href="{{ route('admin.wishList') }}" data-placement="left">
                                    <i class="bi bi-suit-heart nav-icon"></i>
                                    <span class="nav-link-title">@lang("Wishlist")</span>
                                </a>
                            </div>
                        @endif

                        @if(adminAccessRoute(config('role.listing_analytics.access.view')))
                            <div class="nav-item">
                                <a class="nav-link {{ menuActive(['admin.listing.analytics']) }}"
                                   href="{{ route('admin.listing.analytics') }}" data-placement="left">
                                    <i class="bi bi-graph-up-arrow nav-icon"></i>
                                    <span class="nav-link-title">@lang("Analytics")</span>
                                </a>
                            </div>
                        @endif

                        @if(adminAccessRoute(config('role.manage_listing.access.edit')))
                            <div class="nav-item">
                                <a class="nav-link {{ menuActive(['admin.listing.setting']) }}"
                                   href="{{ route('admin.listing.setting') }}" data-placement="left">
                                    <i class="bi bi-gear nav-icon"></i>
                                    <span class="nav-link-title">@lang("Listing Settings")</span>
                                </a>
                            </div>
                        @endif
                    @endif

                    @if(adminAccessRoute(config('role.amenities.access.view')))
                        <span class="dropdown-header mt-2">@lang('Manage Amenities')</span>
                        <small class="bi-three-dots nav-subtitle-replacer"></small>
                        <div class="nav-item">
                            <a class="nav-link {{ menuActive(['admin.amenities', 'admin.amenities.create', 'admin.amenities.edit']) }}"
                               href="{{ route('admin.amenities') }}" data-placement="left">
                                <i class="bi bi-check-circle nav-icon"></i>
                                <span class="nav-link-title">@lang("Amenities")</span>
                            </a>
                        </div>
                    @endif
                    @if(adminAccessRoute(config('role.country.access.view')))
                        <span class="dropdown-header mt-2">@lang('Manage Place')</span>
                        <small class="bi-three-dots nav-subtitle-replacer"></small>
                        <div class="nav-item">
                            <a class="nav-link {{ menuActive(['admin.all.country','admin.country.add','admin.country.edit','admin.country.all.state','admin.country.add.state','admin.country.state.edit','admin.country.state.all.city','admin.country.state.add.city','admin.country.state.city.edit']) }}"
                               href="{{ route('admin.all.country') }}"><i
                                    class="fas fa-flag nav-icon"></i><span class="nav-link-title">@lang("Countries")</span></a>
                        </div>
                    @endif

                    @if(adminAccessRoute(config('role.claim_business.access.view')))
                        <span class="dropdown-header mt-2">@lang('Claim Business')</span>
                        <small class="bi-three-dots nav-subtitle-replacer"></small>
                        <div class="nav-item">
                            <a class="nav-link {{ menuActive(['admin.claim.business', 'admin.claim.business.conversation']) }}"
                               href="{{ route('admin.claim.business') }}" data-placement="left">
                                <i class="fa fa-gavel nav-icon"></i>
                                <span class="nav-link-title">@lang("Claim List")</span>
                            </a>
                        </div>
                    @endif

                    @if(adminAccessRoute(config('role.contact_message.access.view')))
                        <span class="dropdown-header mt-2">@lang('Listing Message')</span>
                        <small class="bi-three-dots nav-subtitle-replacer"></small>
                        <div class="nav-item">
                            <a class="nav-link {{ menuActive(['admin.contact.message']) }}"
                               href="{{ route('admin.contact.message') }}" data-placement="left">
                                <i class="bi bi-chat-dots nav-icon"></i>
                                <span class="nav-link-title">@lang("Messages")</span>
                            </a>
                        </div>
                    @endif

                    @if(adminAccessRoute(config('role.subscriber.access.view')))
                        <span class="dropdown-header mt-2">@lang('Subscriber')</span>
                        <small class="bi-three-dots nav-subtitle-replacer"></small>
                        <div class="nav-item">
                            <a class="nav-link {{ menuActive(['admin.subscriber','admin.subscriber.send.email.form']) }}"
                               href="{{ route('admin.subscriber') }}" data-placement="left">
                                <i class="fas fa-users nav-icon"></i>
                                <span class="nav-link-title">@lang("Subscriber List")</span>
                            </a>
                        </div>
                    @endif

                    @if(adminAccessRoute(config('role.transaction.access.view')))
                        <span class="dropdown-header mt-3">@lang('Transactions')</span>
                        <small class="bi-three-dots nav-subtitle-replacer"></small>
                        <div class="nav-item">
                            <a class="nav-link {{ menuActive(['admin.transaction']) }}"
                               href="{{ route('admin.transaction') }}" data-placement="left">
                                <i class="bi bi-send nav-icon"></i>
                                <span class="nav-link-title">@lang("Transaction")</span>
                            </a>
                        </div>
                    @endif

                    @if(adminAccessRoute(config('role.payment_log.access.view')))
                        <div class="nav-item">
                            <a class="nav-link {{ menuActive(['admin.payment.log']) }}"
                               href="{{ route('admin.payment.log') }}" data-placement="left">
                                <i class="bi bi-credit-card-2-front nav-icon"></i>
                                <span class="nav-link-title">@lang("Payment Log")</span>
                            </a>
                        </div>
                        <div class="nav-item">
                            <a class="nav-link {{ menuActive(['admin.payment.pending']) }}"
                               href="{{ route('admin.payment.pending') }}" data-placement="left">
                                <i class="bi bi-cash nav-icon"></i>
                                <div class="d-flex justify-content-between gap-3">
                                    <span class="nav-link-title">@lang("Payment Request")</span>
                                    @if($sidebarCounts->deposit_pending > 0)
                                        <span
                                            class="badge bg-primary rounded-pill ">{{ $sidebarCounts->deposit_pending }}</span>
                                    @endif
                                </div>
                            </a>
                        </div>
                    @endif

                    @if(adminAccessRoute(config('role.support_ticket.access.view')))
                        <span class="dropdown-header mt-3"> @lang("Ticket Panel")</span>
                        <small class="bi-three-dots nav-subtitle-replacer"></small>
                        <div class="nav-item">
                            <a class="nav-link dropdown-toggle {{ menuActive(['admin.ticket', 'admin.ticket.search', 'admin.ticket.view'], 3) }}"
                               href="#navbarVerticalTicketMenu"
                               role="button"
                               data-bs-toggle="collapse"
                               data-bs-target="#navbarVerticalTicketMenu"
                               aria-expanded="false"
                               aria-controls="navbarVerticalTicketMenu">
                                <i class="fa-light fa-headset nav-icon"></i>
                                <span class="nav-link-title">@lang("Support Ticket")</span>
                            </a>
                            <div id="navbarVerticalTicketMenu"
                                 class="nav-collapse collapse {{ menuActive(['admin.ticket','admin.ticket.search', 'admin.ticket.view'], 2) }}"
                                 data-bs-parent="#navbarVerticalTicketMenu">
                                <a class="nav-link {{ request()->is('admin/tickets/all') ? 'active' : '' }}"
                                   href="{{ route('admin.ticket', 'all') }}">@lang("All Tickets")
                                </a>
                                <a class="nav-link {{ request()->is('admin/tickets/answered') ? 'active' : '' }}"
                                   href="{{ route('admin.ticket', 'answered') }}">@lang("Answered Ticket")</a>
                                <a class="nav-link {{ request()->is('admin/tickets/replied') ? 'active' : '' }}"
                                   href="{{ route('admin.ticket', 'replied') }}">@lang("Replied Ticket")</a>
                                <a class="nav-link {{ request()->is('admin/tickets/closed') ? 'active' : '' }}"
                                   href="{{ route('admin.ticket', 'closed') }}">@lang("Closed Ticket")</a>
                            </div>
                        </div>
                    @endif


                    @if(adminAccessRoute(config('role.kyc_setting.access.view')) || adminAccessRoute(config('role.kyc_request.access.view')))
                        <span class="dropdown-header mt-3"> @lang('Kyc Management')</span>
                        <small class="bi-three-dots nav-subtitle-replacer"></small>

                        @if(adminAccessRoute(config('role.kyc_setting.access.view')))
                            <div class="nav-item">
                                <a class="nav-link {{ menuActive(['admin.kyc.form.list','admin.kyc.edit','admin.kyc.create']) }}"
                                   href="{{ route('admin.kyc.form.list') }}" data-placement="left">
                                    <i class="bi-stickies nav-icon"></i>
                                    <span class="nav-link-title">@lang('KYC Setting')</span>
                                </a>
                            </div>
                        @endif
                        @if(adminAccessRoute(config('role.kyc_request.access.view')))
                            <div class="nav-item" {{ menuActive(['admin.kyc.list*','admin.kyc.view'], 3) }}>
                                <a class="nav-link dropdown-toggle collapsed" href="#navbarVerticalKycRequestMenu"
                                   role="button"
                                   data-bs-toggle="collapse" data-bs-target="#navbarVerticalKycRequestMenu"
                                   aria-expanded="false"
                                   aria-controls="navbarVerticalKycRequestMenu">
                                    <i class="bi bi-person-lines-fill nav-icon"></i>
                                    <span class="nav-link-title">@lang("KYC Request")</span>
                                </a>
                                <div id="navbarVerticalKycRequestMenu"
                                     class="nav-collapse collapse {{ menuActive(['admin.kyc.list*','admin.kyc.view'], 2) }}"
                                     data-bs-parent="#navbarVerticalKycRequestMenu">

                                    <a class="nav-link d-flex justify-content-between {{ Request::is('admin/kyc/pending') ? 'active' : '' }}"
                                       href="{{ route('admin.kyc.list', 'pending') }}">
                                        @lang('Pending KYC')
                                        @if($sidebarCounts->kyc_pending > 0)
                                            <span
                                                class="badge bg-primary rounded-pill ">{{ $sidebarCounts->kyc_pending }}</span>
                                        @endif
                                    </a>
                                    <a class="nav-link d-flex justify-content-between {{ Request::is('admin/kyc/approve') ? 'active' : '' }}"
                                       href="{{ route('admin.kyc.list', 'approve') }}">
                                        @lang('Approved KYC')
                                        @if($sidebarCounts->kyc_verified > 0)
                                            <span
                                                class="badge bg-primary rounded-pill ">{{ $sidebarCounts->kyc_verified }}</span>
                                        @endif
                                    </a>
                                    <a class="nav-link d-flex justify-content-between {{ Request::is('admin/kyc/rejected') ? 'active' : '' }}"
                                       href="{{ route('admin.kyc.list', 'rejected') }}">
                                        @lang('Rejected KYC')
                                        @if($sidebarCounts->kyc_rejected > 0)
                                            <span
                                                class="badge bg-primary rounded-pill ">{{ $sidebarCounts->kyc_rejected }}</span>
                                        @endif
                                    </a>
                                </div>
                            </div>
                        @endif
                    @endif

                    @if(adminAccessRoute(config('role.user_management.access.view')))
                        <span class="dropdown-header mt-3"> @lang("User Panel")</span>
                        <small class="bi-three-dots nav-subtitle-replacer"></small>
                        <div class="nav-item">
                            <a class="nav-link dropdown-toggle {{ menuActive(['admin.users'], 3) }}"
                               href="#navbarVerticalUserPanelMenu"
                               role="button"
                               data-bs-toggle="collapse"
                               data-bs-target="#navbarVerticalUserPanelMenu"
                               aria-expanded="false"
                               aria-controls="navbarVerticalUserPanelMenu">
                                <i class="bi-people nav-icon"></i>
                                <span class="nav-link-title">@lang('User Management')</span>
                            </a>
                            <div id="navbarVerticalUserPanelMenu"
                                 class="nav-collapse collapse {{ menuActive(['admin.mail.all.user','admin.users','admin.users.add','admin.user.edit',
                                                                        'admin.user.view.profile','admin.user.transaction','admin.user.payment',
                                                                        'admin.user.payout','admin.user.kyc.list','admin.send.email'], 2) }}"
                                 data-bs-parent="#navbarVerticalUserPanelMenu">

                                <a class="nav-link {{ request()->is('admin/users') ? 'active' : '' }}"
                                   href="{{ route('admin.users') }}">
                                    @lang('All User')
                                </a>

                                <a href="{{ route('admin.users','active-users') }}"
                                   class="nav-link d-flex justify-content-between {{ request()->is('admin/users/active-users') ? 'active' : '' }}">
                                    @lang('Active Users')
                                    @if($sidebarCounts->active_users > 0)
                                        <span
                                            class="badge bg-primary rounded-pill ">{{ $sidebarCounts->active_users }}</span>
                                    @endif
                                </a>
                                <a href="{{ route('admin.users','blocked-users') }}"
                                   class="nav-link d-flex justify-content-between {{ request()->is('admin/users/blocked-users') ? 'active' : '' }}">
                                    @lang('Blocked Users')
                                    @if($sidebarCounts->blocked_users > 0)
                                        <span
                                            class="badge bg-primary rounded-pill ">{{ $sidebarCounts->blocked_users }}</span>
                                    @endif
                                </a>
                                <a href="{{ route('admin.users','email-unverified') }}"
                                   class="nav-link d-flex justify-content-between {{ request()->is('admin/users/email-unverified') ? 'active' : '' }}">
                                    @lang('Email Unverified')
                                    @if($sidebarCounts->email_unverified > 0)
                                        <span
                                            class="badge bg-primary rounded-pill ">{{ $sidebarCounts->email_unverified }}</span>
                                    @endif
                                </a>
                                <a href="{{ route('admin.users','sms-unverified') }}"
                                   class="nav-link d-flex justify-content-between {{ request()->is('admin/users/sms-unverified') ? 'active' : '' }}">
                                    @lang('Sms Unverified')
                                    @if($sidebarCounts->sms_unverified > 0)
                                        <span
                                            class="badge bg-primary rounded-pill ">{{ $sidebarCounts->sms_unverified }}</span>
                                    @endif
                                </a>

                                @if(adminAccessRoute(config('role.user_management.access.edit')))
                                    <a class="nav-link {{ menuActive(['admin.mail.all.user']) }}"
                                       href="{{ route("admin.mail.all.user") }}">@lang('Mail To Users')</a>
                                @endif
                            </div>
                        </div>
                    @endif

                    @if(adminAccessRoute(config('role.control_panel.access.view')) || adminAccessRoute(config('role.control_panel.access.view'))
                        || adminAccessRoute(config('role.manage_role.access.view')) || adminAccessRoute(config('role.manage_staff_role.access.view')))
                        <span class="dropdown-header mt-3"> @lang('SETTINGS PANEL')</span>
                        <small class="bi-three-dots nav-subtitle-replacer"></small>

                        @if(adminAccessRoute(config('role.control_panel.access.view')))
                            <div class="nav-item">
                                <a class="nav-link {{ menuActive(controlPanelRoutes()) }}"
                                   href="{{ route('admin.settings') }}" data-placement="left">
                                    <i class="bi bi-gear nav-icon"></i>
                                    <span class="nav-link-title">@lang('Control Panel')</span>
                                </a>
                            </div>
                        @endif

                        @if(adminAccessRoute(config('role.payment_settings.access.view')))
                            <div
                                class="nav-item {{ menuActive(['admin.payment.methods', 'admin.edit.payment.methods', 'admin.deposit.manual.index', 'admin.deposit.manual.create', 'admin.deposit.manual.edit'], 3) }}">
                                <a class="nav-link dropdown-toggle"
                                   href="#navbarVerticalGatewayMenu"
                                   role="button"
                                   data-bs-toggle="collapse"
                                   data-bs-target="#navbarVerticalGatewayMenu"
                                   aria-expanded="false"
                                   aria-controls="navbarVerticalGatewayMenu">
                                    <i class="bi-briefcase nav-icon"></i>
                                    <span class="nav-link-title">@lang('Payment Setting')</span>
                                </a>
                                <div id="navbarVerticalGatewayMenu"
                                     class="nav-collapse collapse {{ menuActive(['admin.payment.methods', 'admin.edit.payment.methods', 'admin.deposit.manual.index', 'admin.deposit.manual.create', 'admin.deposit.manual.edit'], 2) }}"
                                     data-bs-parent="#navbarVerticalGatewayMenu">

                                    <a class="nav-link {{ menuActive(['admin.payment.methods', 'admin.edit.payment.methods',]) }}"
                                       href="{{ route('admin.payment.methods') }}">@lang('Payment Gateway')</a>

                                    <a class="nav-link {{ menuActive([ 'admin.deposit.manual.index', 'admin.deposit.manual.create', 'admin.deposit.manual.edit']) }}"
                                       href="{{ route('admin.deposit.manual.index') }}">@lang('Manual Gateway')</a>
                                </div>
                            </div>
                        @endif

                        @if(adminAccessRoute(config('role.manage_role.access.view')))
                            <div class="nav-item">
                                <a class="nav-link {{ menuActive(['admin.role']) }}"
                                   href="{{ route('admin.role') }}" data-placement="left">
                                    <i class="bi-people nav-icon"></i>
                                    <span class="nav-link-title">@lang("Role List")</span>
                                </a>
                            </div>
                        @endif

                        @if(adminAccessRoute(config('role.manage_staff_role.access.view')))
                            <div class="nav-item">
                                <a class="nav-link {{ menuActive(['admin.role.staff','admin.staff.create','admin.edit.staff']) }}"
                                   href="{{ route('admin.role.staff') }}" data-placement="left">
                                    <i class="bi-people nav-icon"></i>
                                    <span class="nav-link-title">@lang("Manage Staff")</span>
                                </a>
                            </div>
                        @endif
                    @endif

                    <span class="dropdown-header mt-3">@lang("Themes Settings")</span>
                    <small class="bi-three-dots nav-subtitle-replacer"></small>
                    <div id="navbarVerticalThemeMenu">
                        @if(adminAccessRoute(config('role.manage_theme.access.view')))
                            <div class="nav-item">
                                <a class="nav-link {{ menuActive(['admin.manage.theme']) }}"
                                   href="{{ route('admin.manage.theme') }}"
                                   data-placement="left">
                                    <i class="fa-light fa-image nav-icon"></i>
                                    <span class="nav-link-title">@lang('Manage Theme')</span>
                                </a>
                            </div>
                        @endif

                        @if(adminAccessRoute(config('role.page.access.view')))
                            <div class="nav-item">
                                <a class="nav-link {{ menuActive(['admin.page.index','admin.create.page','admin.edit.page']) }}"
                                   href="{{ route('admin.page.index', basicControl()->theme) }}"
                                   data-placement="left">
                                    <i class="fa-light fa-list nav-icon"></i>
                                    <span class="nav-link-title">@lang('Pages')</span>
                                </a>
                            </div>
                        @endif

                        @if(adminAccessRoute(config('role.manage_menu.access.view')))
                            <div class="nav-item">
                                <a class="nav-link {{ menuActive(['admin.manage.menu']) }}"
                                   href="{{ route('admin.manage.menu') }}" data-placement="left">
                                    <i class="bi-folder2-open nav-icon"></i>
                                    <span class="nav-link-title">@lang('Manage Menu')</span>
                                </a>
                            </div>
                        @endif
                    </div>

                    @if(adminAccessRoute(config('role.manage_content.access.view')))
                        @php
                            $segments = request()->segments();
                            $last  = end($segments);
                        @endphp
                        <div class="nav-item">
                            <a class="nav-link dropdown-toggle {{ menuActive(['admin.manage.content', 'admin.manage.content.multiple', 'admin.content.item.edit*'], 3) }}"
                               href="#navbarVerticalContentsMenu"
                               role="button" data-bs-toggle="collapse"
                               data-bs-target="#navbarVerticalContentsMenu" aria-expanded="false"
                               aria-controls="navbarVerticalContentsMenu">
                                <i class="fa-light fa-pen nav-icon"></i>
                                <span class="nav-link-title">@lang('Manage Content')</span>
                            </a>
                            <div id="navbarVerticalContentsMenu"
                                 class="content-manage nav-collapse collapse {{ menuActive(['admin.manage.content', 'admin.manage.content.multiple', 'admin.content.item.edit*'], 2) }}"
                                 data-bs-parent="#navbarVerticalContentsMenu">

                                @foreach(array_diff(array_keys(config('contents')), ['message', 'content_media']) as $keyValue)
                                    @if($keyValue == basicControl()->theme)
                                        @foreach(config('contents')[$keyValue] as $name => $content)
                                            @php
                                                $contentImage = config('contents.' .$keyValue. '.' . $name . '.contentPreview');
                                            @endphp
                                            <div class="contentAll d-flex justify-content-between">
                                                <a class="nav-link contentTitle {{ ($last == $name) ? 'active' : '' }}"
                                                   href="{{ route('admin.manage.content', $name) }}">@lang(stringToTitle($name))
                                                </a>
                                                <button class="btn btn-white btn-sm sidebarContentImage contentImage"
                                                        data-theme="{{ ucwords($keyValue) }}"
                                                        data-image="{{ json_encode($contentImage) }}"
                                                        data-bs-toggle="tooltip"
                                                        title="{{ ucwords(str_replace('_', ' ', $name)) }} Preview">
                                                    <i class="fa-regular fa-eye"></i>
                                                </button>
                                            </div>
                                        @endforeach
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif
                    @if(adminAccessRoute(config('role.manage_blog.access.view')))
                        <div class="nav-item">
                            <a class="nav-link dropdown-toggle {{ menuActive(['admin.blog-category.index', 'admin.blog-category.create','admin.blog-category.edit', 'admin.blogs.index', 'admin.blogs.create','admin.blogs.edit*'], 3) }}"
                               href="#navbarVerticalBlogMenu"
                               role="button" data-bs-toggle="collapse"
                               data-bs-target="#navbarVerticalBlogMenu" aria-expanded="false"
                               aria-controls="navbarVerticalBlogMenu">
                                <i class="fa-light fa-newspaper nav-icon"></i>
                                <span class="nav-link-title">@lang('Manage Blog')</span>
                            </a>
                            <div id="navbarVerticalBlogMenu"
                                 class="nav-collapse collapse
                                 {{ menuActive(['admin.blog-category.index', 'admin.blog-category.create','admin.blog-category.edit', 'admin.blogs.index', 'admin.blogs.create','admin.blogs.edit*'], 2) }}"
                                 data-bs-parent="#navbarVerticalBlogMenu">
                                <a class="nav-link {{ menuActive(['admin.blog-category.index', 'admin.blog-category.create','admin.blog-category.edit']) }}"
                                   href="{{ route('admin.blog-category.index') }}">@lang('Blog Category')</a>

                                <a class="nav-link {{ menuActive(['admin.blogs.index', 'admin.blogs.create','admin.blogs.edit*']) }}"
                                   href="{{ route('admin.blogs.index') }}">@lang('Blog')</a>
                            </div>
                        </div>
                    @endif

                    <div class="nav-item">
                        <a class="nav-link"
                           href="{{ route('clear') }}" data-placement="left">
                            <i class="bi bi-radioactive nav-icon"></i>
                            <span class="nav-link-title">@lang('Clear Cache')</span>
                        </a>
                    </div>

                    @foreach(collect(config('generalsettings.settings')) as $key => $setting)
                        <div class="nav-item d-none">
                            <a class="nav-link  {{ isMenuActive($setting['route']) }}"
                               href="{{ getRoute($setting['route'], $setting['route_segment'] ?? null) }}">
                                <i class="{{$setting['icon']}} nav-icon"></i>
                                <span class="nav-link-title">{{ __(getTitle($key.' '.'Settings')) }}</span>
                            </a>
                        </div>
                    @endforeach
                </div>

                <div class="navbar-vertical-footer">
                    <ul class="navbar-vertical-footer-list">
                        <li class="navbar-vertical-footer-list-item">
                            <span class="dropdown-header">@lang('Version 4.2')</span>
                        </li>
                        <li class="navbar-vertical-footer-list-item">
                            <div class="dropdown dropup">
                                <button type="button" class="btn btn-ghost-secondary btn-icon rounded-circle"
                                        id="selectThemeDropdown" data-bs-toggle="dropdown" aria-expanded="false"
                                        data-bs-dropdown-animation></button>
                                <div class="dropdown-menu navbar-dropdown-menu navbar-dropdown-menu-borderless"
                                     aria-labelledby="selectThemeDropdown">
                                    <a class="dropdown-item" href="javascript:void(0)" data-icon="bi-moon-stars"
                                       data-value="auto">
                                        <i class="bi-moon-stars me-2"></i>
                                        <span class="text-truncate"
                                              title="Auto (system default)">@lang("Default")</span>
                                    </a>
                                    <a class="dropdown-item" href="javascript:void(0)" data-icon="bi-brightness-high"
                                       data-value="default">
                                        <i class="bi-brightness-high me-2"></i>
                                        <span class="text-truncate"
                                              title="Default (light mode)">@lang("Light Mode")</span>
                                    </a>
                                    <a class="dropdown-item active" href="javascript:void(0)" data-icon="bi-moon"
                                       data-value="dark">
                                        <i class="bi-moon me-2"></i>
                                        <span class="text-truncate" title="Dark">@lang("Dark Mode")</span>
                                    </a>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</aside>

@push('script')
    <script>
        'use strict';
        $('.sidebarContentImage').on('click', function () {
            const baseUrl = "{{ asset('') }}";
            var themeName = $(this).data('theme');
            var imageData = $(this).data('image');

            let items = Object.keys(imageData).map(function (key) {
                return {
                    src: baseUrl + imageData[key],
                    type: 'image',
                    title: themeName + ' Theme > ' + key + ' Section'
                };
            });
            console.log(items)
            $.magnificPopup.open({
                items: items,
                gallery: {
                    enabled: true
                },
                type: 'image',
                image: {
                    titleSrc: function (item) {
                        console.log(item)
                        return `<div class="mfp-title-overlay"><h5>${item.title || 'Image Title'}</h5></div>`;
                    }
                }
            });
        });
    </script>
@endpush




