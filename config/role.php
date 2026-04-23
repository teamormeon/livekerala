<?php

$arr = [
    'dashboard' => [
        'label' => "Dashboard",
        'access' => [
            'view' => ['admin.dashboard'],
            'add' => [],
            'edit' => [],
            'delete' => [],
        ],
    ],

    'manage_package' => [
        'label' => "Manage Package",
        'access' => [
            'view' => [
                'admin.package',
            ],
            'add' => [
                'admin.package.create'
            ],
            'edit' => [
                'admin.package.edit',
            ],
            'delete' => [
                'admin.package.delete',
            ],
        ],
    ],

    'purchase_package' => [
        'label' => "Purchase Package",
        'access' => [
            'view' => [
                'admin.purchase.package',
            ],
            'add' => [],
            'edit' => [
                'admin.purchase.package.subscription.cancel'
            ],
            'delete' => [
                'admin.purchase.package.delete.multiple'
            ],
        ],
    ],

    'listing_category' => [
        'label' => "Manage Listing Category",
        'access' => [
            'view' => [
                'admin.listing.category',
            ],
            'add' => [
                'admin.listing.category.create',
            ],
            'edit' => [
                'admin.listing.category.edit',
            ],
            'delete' => [
                'admin.listing.category.delete',
                'admin.listing.category.delete.multiple',
            ],
        ],
    ],

    'manage_listing' => [
        'label' => "Manage Listing",
        'access' => [
            'view' => [
                'admin.listings',
                'admin.listing.single.analytics',
            ],
            'add' => [],
            'edit' => [
                'admin.listing.edit',
                'admin.listing.setting',
                'admin.single.listing.approved',
                'admin.multi.listing.approved',
                'admin.single.listing.rejected',
                'admin.multi.listing.rejected',
                'admin.single.listing.active',
                'admin.single.listing.deactive',
            ],
            'delete' => [
                'admin.listing.delete',
                'admin.listing.delete.multiple',
            ],
        ],
    ],

    'listing_analytics' => [
        'label' => "Listing Analytics",
        'access' => [
            'view' => [
                'admin.listing.analytics',
            ],
            'add' => [],
            'edit' => [],
            'delete' => [
                'admin.listing.analytics.delete',
                'admin.listing.analytics.delete.multiple',
            ],
        ],
    ],

    'listing_reviews' => [
        'label' => "Listing Reviews",
        'access' => [
            'view' => [
                'admin.listing.reviews',
            ],
            'add' => [],
            'edit' => [],
            'delete' => [
                'admin.listing.reviews.delete',
                'admin.listing.reviews.delete.multiple',
            ],
        ],
    ],

    'listing_form' => [
        'label' => "Listing Form Data",
        'access' => [
            'view' => [
                'admin.listing.form.data',
            ],
            'add' => [],
            'edit' => [],
            'delete' => [
                'admin.listing.form.data.delete',
                'admin.listing.form.data.delete.multiple',
            ],
        ],
    ],

    'listing_wishlist' => [
        'label' => "Listing Wishlist",
        'access' => [
            'view' => [
                'admin.wishList',
            ],
            'add' => [],
            'edit' => [],
            'delete' => [
                'admin.wishList.delete',
                'admin.wishList.delete.multiple',
            ],
        ],
    ],

    'amenities' => [
        'label' => "Amenities",
        'access' => [
            'view' => [
                'admin.amenities',
            ],
            'add' => [
                'admin.amenities.create'
            ],
            'edit' => [
                'admin.amenities.edit',
            ],
            'delete' => [
                'admin.amenities.delete',
                'admin.amenities.delete.multiple',
            ],
        ],
    ],

    'country' => [
        'label' => "Country List",
        'access' => [
            'view' => [
                'admin.all.country',
            ],
            'add' => [
                'admin.country.add',
            ],
            'edit' => [
                'admin.country.edit',
            ],
            'delete' => [
                'admin.country.delete',
                'admin.country.delete.multiple',
            ],
        ],
    ],
    'state' => [
        'label' => "State List",
        'access' => [
            'view' => [
                'admin.country.all.state',
            ],
            'add' => [
                'admin.country.add.state',
            ],
            'edit' => [
                'admin.country.state.edit',
            ],
            'delete' => [
                'admin.country.state.delete',
                'admin.country.delete.multiple.state',
            ],
        ],
    ],

    'city' => [
        'label' => "City List",
        'access' => [
            'view' => [
                'admin.country.state.all.city',
            ],
            'add' => [
                'admin.country.state.add.city',
            ],
            'edit' => [
                'admin.country.state.city.edit',
            ],
            'delete' => [
                'admin.country.state.city.delete',
                'admin.country.delete.multiple.state.city',
            ],
        ],
    ],

    'claim_business' => [
        'label' => "Claim Business",
        'access' => [
            'view' => [
                'admin.claim.business',
            ],
            'add' => [],
            'edit' => [
                'admin.claim.business.start.chat',
                'admin.claim.business.enable.chat.status',
            ],
            'delete' => [
                'admin.claim.business.delete.multiple',
            ],
        ],
    ],

    'claim_business_conversation' => [
        'label' => "Claim Business Conversation",
        'access' => [
            'view' => [
                'admin.claim.business.conversation',
            ],
            'add' => [],
            'edit' => [
                'admin.claim.business.conversation.push.chat.new.message',
                'admin.claim.chat.stage.change',
            ],
            'delete' => [],
        ],
    ],

    'contact_message' => [
        'label' => "Contact Message",
        'access' => [
            'view' => [
                'admin.contact.message',
            ],
            'add' => [],
            'edit' => [],
            'delete' => [
                'admin.contact.message.delete.multiple',
            ],
        ],
    ],

    'subscriber' => [
        'label' => "Subscriber List",
        'access' => [
            'view' => [
                'admin.subscriber',
            ],
            'add' => [],
            'edit' => ['admin.subscriber.send.email.form'],
            'delete' => [
                'admin.subscriber.delete.multiple'
            ],
        ],
    ],

    'transaction' => [
        'label' => "Transaction",
        'access' => [
            'view' => [
                'admin.user.transaction',
            ],
            'add' => [],
            'edit' => [],
            'delete' => [],
        ],
    ],

    'payment_log' => [
        'label' => "Payment Log & Request",
        'access' => [
            'view' => [
                'admin.payment.log',
                'admin.payment.pending',
            ],
            'add' => [],
            'edit' => [
                'admin.payment.action',
            ],
            'delete' => [],
        ],
    ],


    'support_ticket' => [
        'label' => "Support Ticket",
        'access' => [
            'view' => [
                'admin.ticket',
                'admin.ticket.view',
            ],
            'add' => [],
            'edit' => ['admin.ticket.reply'],
            'delete' => [
                'admin.ticket.closed',
            ],
        ],
    ],

    'kyc_setting' =>[
        'label' => "KYC Setting",
        'access' => [
            'view' => ['admin.kyc.form.list'],
            'add' => ['admin.kyc.create'],
            'edit' => [
                'admin.kyc.edit',
            ],
            'delete' => [],
        ],
    ],

    'kyc_request' =>[
        'label' => "KYC Request",
        'access' => [
            'view' => ['admin.kyc.list'],
            'add' => [],
            'edit' => [
                'admin.kyc.action',
            ],
            'delete' => [],
        ],
    ],

    'user_management' => [
        'label' => "User Management",
        'access' => [
            'view' => [
                'admin.users',
                'admin.user.payment',
                'admin.user.payout',
                'admin.user.view.profile',

            ],
            'add' => [
                'admin.users.add'
            ],
            'edit' => [
                'admin.user.edit',
                'admin.login.as.user',
                'admin.user.email.update',
                'admin.user.username.update',
                'admin.user.update.balance',
                'admin.user.password.update',
                'admin.user.preferences.update',
                'admin.user.twoFa.update',
                'admin.user-balance-update',
                'admin.send.email',
                'admin.user.email.send',
                'admin.mail.all.user',
                'admin.email-send',
                'admin.email-send.store',
            ],
            'delete' => [
                'admin.user.delete.multiple',
                'admin.user.delete',
            ],
        ],
    ],

    'control_panel' => [
        'label' => "Control Panel",
        'access' => [
            'view' => [
                'admin.settings',
                'admin.basic.control',
                'admin.storage.index',
                'admin.maintenance.index',
                'admin.logo.settings',
                'admin.firebase.config',
                'admin.pusher.config',
                'admin.email.control',
                'admin.currency.exchange.api.config',
                'admin.email.templates',
                'admin.sms.templates',
                'admin.in.app.notification.templates',
                'admin.push.notification.templates',
                'admin.sms.controls',
                'admin.plugin.config',
                'admin.translate.api.setting',
                'admin.language.index',
                'admin.language.keywords',
                'admin.gdpr.cookie',
                'admin.map.config',
            ],
            'add' => [
                'admin.language.create',
                'admin.language.store',
                'admin.add.language.keyword',
            ],
            'edit' => [
                'admin.basic.control.update',
                'admin.basic.control.activity.update',
                'admin.currency.exchange.api.config.update',
                'admin.storage.edit',
                'admin.storage.update',
                'admin.storage.setDefault',
                'admin.maintenance.mode.update',
                'admin.logo.update',
                'admin.firebase.config.update',
                'admin.pusher.config.update',
                'admin.email.config.edit',
                'admin.email.config.update',
                'admin.email.set.default',
                'admin.email.template.default',
                'admin.email.template.edit',
                'admin.email.template.update',
                'admin.sms.template.edit',
                'admin.sms.template.update',
                'admin.in.app.notification.template.edit',
                'admin.in.app.notification.template.update',
                'admin.push.notification.template.edit',
                'admin.push.notification.template.update',
                'admin.sms.config.edit',
                'admin.sms.config.update',
                'admin.manual.sms.method.update',
                'admin.sms.set.default',
                'admin.tawk.configuration',
                'admin.tawk.configuration.update',
                'admin.fb.messenger.configuration',
                'admin.fb.messenger.configuration.update',
                'admin.google.recaptcha.configuration',
                'admin.google.recaptcha.Configuration.update',
                'admin.google.analytics.configuration',
                'admin.google.analytics.configuration.update',
                'admin.manual.recaptcha',
                'admin.manual.recaptcha.update',
                'admin.active.recaptcha',
                'admin.translate.api.config.edit',
                'admin.translate.api.setting.update',
                'admin.translate.set.default',
                'admin.language.edit',
                'admin.language.update',
                'admin.change.language.status',
                'admin.update.language.keyword',
                'admin.single.keyword.translate',
                'admin.all.keyword.translate',
                'admin.language.update.key',
                'admin.gdpr.cookie.update',
                'admin.map.config.update',
            ],
            'delete' => [
                'admin.delete.language.keyword',
                'admin.language.delete',
            ],
        ],
    ],

    'payment_settings' => [
        'label' => "Payment Setting",
        'access' => [
            'view' => [
                'admin.payment.methods',
                'admin.deposit.manual.index'
            ],
            'add' => [
                'admin.deposit.manual.create',
                'admin.deposit.manual.store',
            ],
            'edit' => [
                'admin.edit.payment.methods',
                'admin.update.payment.methods',
                'admin.deposit.manual.edit',
                'admin.deposit.manual.update',
            ],
            'delete' => [],
        ],
    ],

    'manage_role' =>[
        'label' => "Manage Role",
        'access' => [
            'view' => ['admin.role'],
            'add' => ['admin.role.create'],
            'edit' => ['admin.role.update'],
            'delete' => ['admin.role.delete'],
        ],
    ],

    'manage_staff_role' =>[
        'label' => "Manage Staff Role",
        'access' => [
            'view' => ['admin.role.staff'],
            'add' => ['admin.role.usersCreate'],
            'edit' => ['role.statusChange'],
            'delete' => [],
        ],
    ],

    'manage_theme' => [
        'label' => "Manage Theme",
        'access' => [
            'view' => [
                'admin.manage.theme',
                'admin.manage.theme.select'
            ],
            'add' => [],
            'edit' => [],
            'delete' => [],
        ],
    ],

    'page' => [
        'label' => "Page",
        'access' => [
            'view' => [
                'admin.page.index',
            ],
            'add' => ['admin.create.page'],
            'edit' => [
                'admin.edit.page',
                'admin.edit.static.page',
                'admin.page.seo'
            ],
            'delete' => ['admin.page.delete'],
        ],
    ],

    'manage_menu' => [
        'label' => "Manage Menu",
        'access' => [
            'view' => [
                'admin.manage.menu',
            ],
            'add' => ['admin.add.custom.link'],
            'edit' => [
                'admin.header.menu.item.store',
                'admin.footer.menu.item.store',
                'admin.edit.custom.link'
            ],
            'delete' => ['admin.delete.custom.link'],
        ],
    ],

    'manage_content' => [
        'label' => "Manage Content",
        'access' => [
            'view' => [
                'admin.manage.content',
            ],
            'add' => ['admin.manage.content.multiple'],
            'edit' => [
                'admin.content.store',
                'admin.content.item.edit',
            ],
            'delete' => ['admin.content.item.delete'],
        ],
    ],

    'manage_blog' => [
        'label' => "Blog",
        'access' => [
            'view' => [
                'admin.blogs.index',
                'admin.blog-category.index',
            ],
            'add' => [
                'admin.blogs.create',
                'admin.blogs.store',
                'admin.blog-category.create',
                'admin.blog-category.store',
            ],
            'edit' => [
                'admin.blogs.edit*',
                'admin.blog-category.edit',
            ],
            'delete' => [
                'admin.blogs.destroy',
                'admin.blog-category.destroy',
            ],
        ],
    ],

];

return $arr;



