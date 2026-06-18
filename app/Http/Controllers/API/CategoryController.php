<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ListingCategory;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    use ApiResponse;

    public function listingCategories($id = null)
    {
        $listingCategories = ListingCategory::with('details:id,listing_category_id,language_id,name')->where('status', 1)
            ->when(isset($id), function ($query) use ($id) {
                return $query->where('id', $id);
            })
            ->latest()->get();
        $formatedListingCategories = $listingCategories->map(function ($item) {
            return [
                'id' => $item->id,
                'icon' => $item->icon,
                'name' => html_entity_decode($item->details->name),
                'image' => $item->mobile_app_image ? getFile($item->image_driver,$item->mobile_app_image) : null,
                'language_id' => $item->details->language_id,
                'total_listing' => $item->getCategoryCount(),
                'status' => $item->status,
            ];
        });

        $info = [
            'status' => '0 = Inactive, 1 = Active',
        ];
        return response()->json($this->withSuccess($formatedListingCategories, $info));
    }
}
