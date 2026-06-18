<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\ContentDetails;
use App\Models\ListingCategory;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->theme = template();
    }

    public function index()
    {
        $selectedTheme = getTheme();
        $pageSeo = Page::where('template_name', $selectedTheme)->where('slug', 'category')->first();
        $pageSeo['breadcrumb_image'] = $pageSeo?->breadcrumb_status == 1 ?  getFile($pageSeo->breadcrumb_image_driver, $pageSeo->breadcrumb_image) : null;

        $categories = ListingCategory::select('id','icon')->with(['details:id,listing_category_id,language_id,name'])->where('status',1)->latest()->get();
        $categorySingle = DB::table('content_details')
            ->join('contents', 'contents.id', '=', 'content_details.content_id')
            ->where('contents.theme', $selectedTheme)
            ->where('contents.name', 'listing_categories')
            ->where('contents.type', 'single')
            ->first();
        return view(template(). 'frontend.category.index', compact('pageSeo', 'categories','categorySingle'));
    }

    public function categorySearch(Request $request)
    {
        $character = $request->character;
        if ($character != null) {
            $data['categories'] = ListingCategory::with('details')->whereHas('details', function ($q) use ($character) {
                $q->where('name', 'LIKE', $character . '%');
            })->withCount('get_listings')->where('status', 1)->latest()->get();
        } else {
            $data['categories'] = ListingCategory::with('details')->withCount('get_listings')->where('status', 1)->latest()->get();
        }
        $count = $data['categories']->count();
        $view = view($this->theme . 'frontend.category.partials.renderCategory', $data)->render();
        return response()->json(['data' => $view, 'count' => $count]);
    }

}
