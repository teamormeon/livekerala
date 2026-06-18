<?php

return [
    'light' => [
        'hero' => [
            'single' => [
                'field_name' => [
                    'title' => 'text',
                    'sub_title' => 'text',
                    'image'=>'file',
                ],
                'validation' => [
                    'title.*' => 'required|string|max:200',
                    'sub_title.*' => 'required|string|max:500',
                    'image.*' => 'required|image|mimes:jpg,jpeg,png'
                ],
                'size'=>[
                    'image' => '1920x878',
                ],
            ],
            'contentPreview' => [
                'Hero'=>'assets/global/images/sections/light/hero.png',
            ],
        ],

        'about' => [
            'single' => [
                'field_name' => [
                    'heading' => 'text',
                    'title' => 'text',
                    'description' => 'textarea',
                    'image' => 'file',
                ],
                'validation' => [
                    'heading.*' => 'required|max:100',
                    'title.*' => 'required|max:100',
                    'description.*' => 'required|max:1500',
                    'image.*' => 'nullable|max:3072|image|mimes:jpg,jpeg,png',
                ],
                'size'=>[
                    'image' => '512x656',
                ]
            ],
            'contentPreview' => [
                'About'=>'assets/global/images/sections/light/about.png',
            ],
        ],

        'testimonial' => [
            'single' => [
                'field_name' => [
                    'heading' => 'text',
                    'sub_heading' => 'text',
                ],
                'validation' => [
                    'heading.*' => 'required|max:100',
                    'sub_heading.*' => 'required|max:150',
                ],
            ],
            'multiple' => [
                'field_name' => [
                    'name' => 'text',
                    'designation' => 'text',
                    'description' => 'textarea',
                    'image' => 'file',
                ],
                'validation' => [
                    'name.*' => 'required|max:100',
                    'designation.*' => 'required|max:100',
                    'description.*' => 'required|max:500',
                    'image.*' => 'required|max:3072|image|mimes:jpg,jpeg,png',
                ],
                'size'=>[
                    'image' => '80x80',
                ]
            ],
            'contentPreview' => [
                'Testimonial'=>'assets/global/images/sections/light/testimonial.png',
            ],
        ],

        'how_it_work' => [
            'single' => [
                'field_name' => [
                    'heading' => 'text',
                    'sub_heading' => 'text',
                ],
                'validation' => [
                    'heading.*' => 'required|max:50',
                    'sub_heading.*' => 'required|max:100',
                ],
            ],
            'multiple' => [
                'field_name' => [
                    'title' => 'text',
                    'description' => 'textarea',
                    'image' => 'file',
                ],
                'validation' => [
                    'title.*' => 'required|max:50',
                    'description.*' => 'required|max:250',
                    'image.*' => 'required|max:3072|image|mimes:jpg,jpeg,png',
                ],
                'size'=>[
                    'image' => '64x64',
                ]
            ],
            'contentPreview' => [
                'How it Work'=>'assets/global/images/sections/light/how_it_work.png',
            ],
        ],

        'pricing' => [
            'single' => [
                'field_name' => [
                    'title' => 'text',
                    'description' => 'textarea',
                ],
                'validation' => [
                    'title.*' => 'required|max:150',
                    'description.*' => 'required|max:1500',
                ],
            ],
            'contentPreview' => [
                'Pricing'=>'assets/global/images/sections/light/pricing.png',
            ],
        ],

        'contact' => [
            'single' => [
                'field_name' => [
                    'left_heading' => 'text',
                    'left_details' => 'text',
                    'right_heading' => 'text',
                    'right_details' => 'text',
                    'phone' => 'text',
                    'email' => 'text',
                    'address' => 'text',
                    'footer_description' => 'text',
                ],
                'validation' => [
                    'left_heading.*' => 'required',
                    'left_details.*' => 'required',
                    'right_heading.*' => 'required',
                    'right_details.*' => 'required',
                    'phone.*' => 'required',
                    'email.*' => 'required',
                    'address.*' => 'required',
                    'footer_description.*' => 'required',
                ],
            ],
            'multiple' => [
                'field_name' => [
                    'social_icon' => 'text',
                    'social_link' => 'text',
                ],
                'validation' => [
                    'social_icon.*' => 'required',
                    'social_link.*' => 'required|max:100',
                ],
            ],
            'contentPreview' => [
                'Contact'=>'assets/global/images/sections/light/contact.png',
            ],
        ],

        'faq' => [
            'multiple' => [
                'field_name' => [
                    'question' => 'text',
                    'answer' => 'textarea'
                ],
                'validation' => [
                    'question.*' => 'required|max:300',
                    'answer.*' => 'required|max:1500'
                ]
            ],
            'contentPreview' => [
                'FAQ'=>'assets/global/images/sections/light/faq.png',
            ],
        ],

        'privacy_policy' => [
            'single' => [
                'field_name' => [
                    'title' => 'text',
                    'description' => 'textarea',
                ],
                'validation' => [
                    'title.*' => 'required|max:150',
                    'description.*' => 'required|max:5000',
                ],
            ],
            'contentPreview' => [
                'Privacy Policy'=>'assets/global/images/sections/light/privacy_policy.png',
            ],
        ],

        'terms_and_conditions' => [
            'single' => [
                'field_name' => [
                    'title' => 'text',
                    'description' => 'textarea',
                ],
                'validation' => [
                    'title.*' => 'required|max:150',
                    'description.*' => 'required|max:5000',
                ],
            ],
            'contentPreview' => [
                'Terms And Condition'=>'assets/global/images/sections/light/terms_and_conditions.png',
            ],
        ],

        'news_letter' => [
            'single' => [
                'field_name' => [
                    'title' => 'text',
                    'description' => 'textarea',
                ],
                'validation' => [
                    'title.*' => 'required|max:150',
                    'description.*' => 'required|max:2000',
                ],
            ],
            'contentPreview' => [
                'News Letter'=>'assets/global/images/sections/light/news_letter.png',
            ],
        ],

        'blog' => [
            'single' => [
                'field_name' => [
                    'title' => 'text',
                    'sub_title' => 'text',
                    'description' => 'textarea',
                ],
                'validation' => [
                    'title.*' => 'required|max:150',
                    'sub_title.*' => 'required|max:300',
                    'description.*' => 'required|max:1000',
                ],
            ],
            'contentPreview' => [
                'Blog'=>'assets/global/images/sections/light/blog.png',
            ],
        ],

        'listing' => [
            'single' => [
                'field_name' => [
                    'title' => 'text',
                    'sub_title' => 'text',
                    'description' => 'textarea',
                ],
                'validation' => [
                    'title.*' => 'required|max:150',
                    'sub_title.*' => 'required|max:300',
                    'description.*' => 'required|max:1000',
                ],
            ],
            'contentPreview' => [
                'Listing'=>'assets/global/images/sections/light/listing.png',
            ],
        ],

        'listing_categories' => [
            'single' => [
                'field_name' => [
                    'title' => 'text',
                    'sub_title' => 'text',
                    'description' => 'textarea',
                ],
                'validation' => [
                    'title.*' => 'required|max:150',
                    'sub_title.*' => 'required|max:300',
                    'description.*' => 'required|max:1000',
                ],
            ],
            'contentPreview' => [
                'Listing Category'=>'assets/global/images/sections/light/listing_categories.png',
            ],
        ],
    ],

    'directory' => [
        'hero' => [
            'single' => [
                'field_name' => [
                    'title' => 'text',
                    'sub_title' => 'text',
                    'image'=>'file',
                ],
                'validation' => [
                    'title.*' => 'required|string|max:200',
                    'sub_title.*' => 'required|string|max:500',
                    'image.*' => 'required|image|mimes:jpg,jpeg,png'
                ],
                'size'=>[
                    'image' => '1280x853',
                ],
            ],
            'contentPreview' => [
                'Hero'=>'assets/global/images/sections/directory/hero.png',
            ],
        ],

        'about' => [
            'single' => [
                'field_name' => [
                    'heading' => 'text',
                    'title' => 'text',
                    'description' => 'textarea',
                    'image' => 'file',
                ],
                'validation' => [
                    'heading.*' => 'required|max:100',
                    'title.*' => 'required|max:100',
                    'description.*' => 'required|max:1500',
                    'image.*' => 'nullable|max:3072|image|mimes:jpg,jpeg,png',
                ],
                'size'=>[
                    'image' => '624x535',
                ]
            ],
            'contentPreview' => [
                'About'=>'assets/global/images/sections/directory/about.png',
            ],
        ],

        'listing_categories' => [
            'single' => [
                'field_name' => [
                    'title' => 'text',
                    'sub_title' => 'text',
                    'description' => 'textarea',
                ],
                'validation' => [
                    'title.*' => 'required|max:150',
                    'sub_title.*' => 'required|max:300',
                    'description.*' => 'required|max:1000',
                ],
            ],
            'contentPreview' => [
                'Listing Category'=>'assets/global/images/sections/directory/listing_categories.png',
            ],
        ],

        'listing' => [
            'single' => [
                'field_name' => [
                    'title' => 'text',
                    'sub_title' => 'text',
                    'description' => 'textarea',
                ],
                'validation' => [
                    'title.*' => 'required|max:150',
                    'sub_title.*' => 'required|max:300',
                    'description.*' => 'required|max:1000',
                ],
            ],
            'contentPreview' => [
                'Listing'=>'assets/global/images/sections/directory/listing.png',
            ],
        ],

        'pricing' => [
            'single' => [
                'field_name' => [
                    'title' => 'text',
                    'sub_title' => 'text',
                    'description' => 'textarea',
                ],
                'validation' => [
                    'title.*' => 'required|max:150',
                    'sub_title.*' => 'required|max:300',
                    'description.*' => 'required|max:1500',
                ],
            ],
            'contentPreview' => [
                'Pricing'=>'assets/global/images/sections/directory/pricing.png',
            ],
        ],

        'how_it_work' => [
            'single' => [
                'field_name' => [
                    'heading' => 'text',
                    'sub_heading' => 'text',
                    'description' => 'textarea',
                ],
                'validation' => [
                    'heading.*' => 'required|max:50',
                    'sub_heading.*' => 'required|max:100',
                    'description.*' => 'required|max:150',
                ],
            ],
            'multiple' => [
                'field_name' => [
                    'title' => 'text',
                    'fontawesome_icon_class' => 'text',
                    'description' => 'textarea',
                ],
                'validation' => [
                    'title.*' => 'required|max:50',
                    'fontawesome_icon_class.*' => 'required',
                    'description.*' => 'required|max:250',
                ],
                'size'=>[
                    'image' => '64x64',
                ]
            ],
            'contentPreview' => [
                'How it Work'=>'assets/global/images/sections/directory/how_it_work.png',
            ],
        ],

        'testimonial' => [
            'single' => [
                'field_name' => [
                    'heading' => 'text',
                    'sub_heading' => 'text',
                    'description' => 'textarea',
                ],
                'validation' => [
                    'heading.*' => 'required|max:100',
                    'sub_heading.*' => 'required|max:150',
                    'description.*' => 'required|max:300',
                ],
            ],
            'multiple' => [
                'field_name' => [
                    'name' => 'text',
                    'address' => 'text',
                    'rating' => 'text',
                    'description' => 'textarea',
                    'image' => 'file',
                ],
                'validation' => [
                    'name.*' => 'required|max:100',
                    'address.*' => 'required|max:100',
                    'rating.*' => 'required|numeric|between:1,5',
                    'description.*' => 'required|max:500',
                    'image.*' => 'required|max:3072|image|mimes:jpg,jpeg,png',
                ],
                'size'=>[
                    'image' => '500x480',
                ]
            ],
            'contentPreview' => [
                'Testimonial'=>'assets/global/images/sections/directory/testimonial.png',
            ],
        ],

        'faq' => [
            'single' => [
                'field_name' => [
                    'title' => 'text',
                    'sub_title' => 'text',
                    'description' => 'textarea',
                ],
                'validation' => [
                    'title.*' => 'required|max:150',
                    'sub_title.*' => 'required|max:300',
                    'description.*' => 'required|max:1000',
                ],
            ],
            'multiple' => [
                'field_name' => [
                    'question' => 'text',
                    'answer' => 'textarea'
                ],
                'validation' => [
                    'question.*' => 'required|max:300',
                    'answer.*' => 'required|max:1500'
                ]
            ],
            'contentPreview' => [
                'FAQ'=>'assets/global/images/sections/directory/faq.png',
            ],
        ],

        'privacy_policy' => [
            'single' => [
                'field_name' => [
                    'title' => 'text',
                    'description' => 'textarea',
                ],
                'validation' => [
                    'title.*' => 'required|max:150',
                    'description.*' => 'required|max:5000',
                ],
            ],
            'contentPreview' => [
                'Privacy Policy'=>'assets/global/images/sections/light/privacy_policy.png',
            ],
        ],

        'terms_and_conditions' => [
            'single' => [
                'field_name' => [
                    'title' => 'text',
                    'description' => 'textarea',
                ],
                'validation' => [
                    'title.*' => 'required|max:150',
                    'description.*' => 'required|max:5000',
                ],
            ],
            'contentPreview' => [
                'Terms And Condition'=>'assets/global/images/sections/light/terms_and_conditions.png',
            ],
        ],

        'blog' => [
            'single' => [
                'field_name' => [
                    'title' => 'text',
                    'sub_title' => 'text',
                    'description' => 'textarea',
                ],
                'validation' => [
                    'title.*' => 'required|max:150',
                    'sub_title.*' => 'required|max:300',
                    'description.*' => 'required|max:1000',
                ],
            ],
            'contentPreview' => [
                'Blog'=>'assets/global/images/sections/directory/blog.png',
            ],
        ],

        'news_letter' => [
            'single' => [
                'field_name' => [
                    'title' => 'text',
                    'description' => 'text',
                ],
                'validation' => [
                    'title.*' => 'required|max:150',
                    'description.*' => 'required|max:1000',
                ],
            ],
            'contentPreview' => [
                'News Letter'=>'assets/global/images/sections/directory/news_letter.png',
            ],
        ],

        'contact' => [
            'single' => [
                'field_name' => [
                    'title' => 'text',
                    'description' => 'textarea',
                    'phone' => 'text',
                    'email' => 'text',
                    'address' => 'text',
                    'footer_description' => 'text',
                    'image' => 'file',
                ],
                'validation' => [
                    'title.*' => 'required|max:150',
                    'description.*' => 'required|max:400',
                    'phone.*' => 'required',
                    'email.*' => 'required',
                    'address.*' => 'required',
                    'footer_description.*' => 'required',
                    'image.*' => 'required|image|mimes:jpg,jpeg,png',
                ],
                'size'=>[
                    'image' => '300x267',
                ],
            ],
            'multiple' => [
                'field_name' => [
                    'fontawesome_social_icon_class' => 'text',
                    'social_link' => 'text',
                ],
                'validation' => [
                    'social_icon.*' => 'required',
                    'social_link.*' => 'required|max:100',
                ],
            ],
            'contentPreview' => [
                'Contact'=>'assets/global/images/sections/directory/contact.png',
            ],
        ],
    ],


    'message' => [
        'required' => 'This field is required.',
        'min' => 'This field must be at least :min characters.',
        'max' => 'This field may not be greater than :max characters.',
        'image' => 'This field must be image.',
        'mimes' => 'This image must be a file of type: jpg, jpeg, png.',
        'integer' => 'This field must be an integer value',
    ],

    'content_media' => [
        'image' => 'file',
        'thumb_image' => 'file',
        'my_link' => 'url',
        'icon' => 'icon',
        'count_number' => 'number',
        'start_date' => 'date'
    ]
];

