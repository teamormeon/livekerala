<?php

namespace Database\Seeders;

use App\Models\Language;
use App\Models\ListingCategory;
use App\Models\ListingCategoryDetails;
use Illuminate\Database\Seeder;

class LiveKeralaCategorySeeder extends Seeder
{
    /**
     * Seed the application's database with normalized categories from livekerala.com.
     */
    public function run(): void
    {
        $categories = [
            'Accommodation',
            'Advertising',
            'Agents',
            'Agriculture',
            'Agriculture Blogs',
            'Agriculture Marketing',
            'Agriculture Nursery',
            'Agriculture Products',
            'Allopathic Hospitals',
            'App Development',
            'Architects',
            'Arts & Finance Colleges',
            'Automobile Finance',
            'Automobile Services',
            'Automobile Shops',
            'Ayurvedic Hospitals',
            'Bakers',
            'Bank & Finance',
            'Beauty Parlour',
            'Beauty Products',
            'Builders',
            'Cab Services',
            'Caterers',
            'Chemical Industries',
            "Children's Hospitals",
            'Cinema',
            'Communication Services',
            'Computer Education',
            'Construction Materials',
            'Convention Centre',
            'Cruise Services',
            'Dental Hospitals',
            'Dermatology Hospitals',
            'Digital Marketing',
            'Doctors',
            'Electrical Manufactures',
            'Electrical Sales & Service',
            'Electronic Sales & Service',
            'Electronics Manufactures',
            'Electronics Shops',
            'Engineers',
            'ENT',
            'Eye Hospitals',
            'Food Suppliers',
            'Glass',
            'Homestay & Others',
            'Hospitals',
            'Hotels',
            'Houseboats',
            'Industries',
            'Insurance Services',
            'Instagrammer',
            'Interior Designers',
            'Internet Services',
            'Jewellery',
            'Landscapes And Gardening',
            'Lifestyle Blogs',
            'Maintenance',
            'Medical Colleges',
            'Modular Kitchen',
            'Movies',
            'Music School',
            'Online',
            'Online Directories',
            'Other Shops',
            'Other Travel Services',
            'Pet Supplements',
            'Pharmacy',
            'Photography',
            'Plastics',
            'Printers & Publishers',
            'Professional Institutions',
            'Property Managements',
            'Resorts',
            'Restaurant',
            'Rubber Industries',
            'Sales & Services',
            'Schools',
            'Service',
            'Spare Parts',
            'Spices',
            'Super Markets',
            'Tech Blogs',
            'Textiles',
            'Theme Park',
            'Travel Agents',
            'Travel Blogs',
            'TV Channels',
            'Water Purifiers',
            'Website Designing',
            'Website Hosting',
            'Wedding Planners',
            'Youtubers',
        ];

        $languageId = Language::where('default_status', 1)->value('id')
            ?? Language::orderBy('id')->value('id');

        if (!$languageId) {
            $this->command?->warn('No language found. Seed languages before importing categories.');
            return;
        }

        foreach ($categories as $name) {
            $existingDetail = ListingCategoryDetails::where('language_id', $languageId)
                ->where('name', $name)
                ->first();

            $category = $existingDetail?->category;

            if (!$category) {
                $category = ListingCategory::create([
                    'icon' => 'fas fa-folder',
                    'status' => 1,
                ]);
            } else {
                $category->update([
                    'status' => 1,
                ]);
            }

            ListingCategoryDetails::updateOrCreate(
                [
                    'listing_category_id' => $category->id,
                    'language_id' => $languageId,
                ],
                [
                    'name' => $name,
                ]
            );
        }
    }
}
