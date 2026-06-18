<?php

namespace Database\Seeders;

use App\Models\Page;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
//            ['name' => 'Category', 'slug' => 'category', 'template_name' => 'light', 'type' => 2],
//            ['name' => 'Blogs', 'slug' => 'blogs', 'template_name' => 'light', 'type' => 1],
//            ['name' => 'Blog Details', 'slug' => 'blog-details', 'template_name' => 'light', 'type' => 1],
//            ['name' => 'Pricing', 'slug' => 'pricing', 'template_name' => 'light', 'type' => 2],
//            ['name' => 'Pricing Payment', 'slug' => 'pricing-payment', 'template_name' => 'light', 'type' => 1],
//            ['name' => 'Profile', 'slug' => 'profile', 'template_name' => 'light', 'type' => 1],
//            ['name' => 'Listings', 'slug' => 'listings', 'template_name' => 'light', 'type' => 2],
//            ['name' => 'Listing Details', 'slug' => 'listing-details', 'template_name' => 'light', 'type' => 1],
//            ['name' => 'Cookie Policy', 'slug' => 'cookie-policy', 'template_name' => 'light', 'type' => 1],


//            ['name' => 'Privacy Policy', 'slug' => 'privacy-policy', 'template_name' => 'directory', 'type' => 0],
//            ['name' => 'Terms and Conditions', 'slug' => 'terms-and-conditions', 'template_name' => 'directory', 'type' => 0],

//            ['name' => 'login', 'slug' => 'login', 'template_name' => 'light', 'type' => 1],
//            ['name' => 'register', 'slug' => 'register', 'template_name' => 'light', 'type' => 1],
//            ['name' => 'sms verification', 'slug' => 'sms-verification', 'template_name' => 'light', 'type' => 1],
//            ['name' => 'email verification', 'slug' => 'email-verification', 'template_name' => 'light', 'type' => 1],
//            ['name' => '2FA verification', 'slug' => '2FA-verification', 'template_name' => 'light', 'type' => 1],
//            ['name' => 'Reset Password', 'slug' => 'reset-password', 'template_name' => 'light', 'type' => 1],
        ];

        foreach ($pages as $page) {
            Page::updateOrCreate(
                ['name' => $page['name']],
                [
                    'slug' => $page['slug'],
                    'template_name' => $page['template_name'],
                    'type' => $page['type'],
                ],
                [
                    'created_at' => \Illuminate\Support\Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]
            );
        }

    }
}
