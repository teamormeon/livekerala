<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\BlogDetails;
use App\Models\Content;
use App\Models\ContentDetails;
use App\Models\Page;
use App\Models\PageDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BlogController extends Controller
{
    public function __construct()
    {
        $this->theme = template();
    }

    public function blogs(Request $request)
    {
        $pageSeo = Page::where('template_name', getTheme())->where('slug', 'blogs')->first();
        $pageSeo['breadcrumb_image'] = $pageSeo?->breadcrumb_status == 1 ?  getFile($pageSeo->breadcrumb_image_driver, $pageSeo->breadcrumb_image) : null;

        $data['blogs'] = Blog::select('id','category_id','blog_image','blog_image_driver','status','created_at')
            ->with(['category:id,name,slug', 'details'])
            ->where('status', 1)
            ->orderBy('id', 'desc')
            ->when(isset($request->search), function ($query) use ($request) {
                $query->whereHas('details', function ($query2) use ($request) {
                    $query2->where('title', 'LIKE', '%'.$request->search.'%');
                });
            })
            ->when(isset($request->category), function ($query) use ($request) {
                $query->whereHas('category', function ($query2) use ($request) {
                    $query2->where('slug', $request->category);
                });
            })
            ->latest()->paginate(3);

        $data['blogSingleContent'] = ContentDetails::with('content')->whereHas('content', function ($query) use ($request) {
            $query->where('theme', getTheme())
                ->where('name','blog')
                ->where('type', 'single');
        })->first();

        $data['blogCategories'] = DB::table('blog_categories')
            ->leftJoin('blogs', 'blog_categories.id', '=', 'blogs.category_id')
            ->select('blog_categories.*', DB::raw('COUNT(blogs.id) as blogs_count'))
            ->groupBy('blog_categories.id')
            ->orderBy('blog_categories.id', 'desc')
            ->get();
        return view(template().'frontend.blog.blog_list', $data, compact('pageSeo'));
    }

    public function blogDetails($slug)
    {
        $pageSeo = Page::where('template_name', getTheme())->where('slug', 'blog-details')->first();
        $pageSeo['breadcrumb_image'] = $pageSeo?->breadcrumb_status == 1 ?  getFile($pageSeo->breadcrumb_image_driver, $pageSeo->breadcrumb_image) : null;

        $blogDetails = BlogDetails::with(['blog:id,category_id,blog_image,blog_image_driver,status,created_at'])
        ->where('slug', $slug)
            ->first();

        if ($blogDetails) {
            $categoryId = $blogDetails->blog->category_id ?? null;
            if ($categoryId) {
                $relatedBlogs = Blog::select('id', 'category_id', 'blog_image', 'blog_image_driver', 'status', 'created_at')
                ->where('category_id', $categoryId)
                    ->where('id', '!=', $blogDetails->blog_id)
                    ->latest()
                    ->take(3)
                    ->get();
                $data['relatedBlogs'] = $relatedBlogs;
            }
        }

        $data['blogDetails'] = $blogDetails;
        $data['blogCategories'] = DB::table('blog_categories')
            ->leftJoin('blogs', 'blog_categories.id', '=', 'blogs.category_id')
            ->select('blog_categories.*', DB::raw('COUNT(blogs.id) as blogs_count'))
            ->groupBy('blog_categories.id')
            ->orderBy('blog_categories.id', 'desc')
            ->get();
        return view(template().'frontend.blog.blog_details', $data, compact('pageSeo'));
    }
}
