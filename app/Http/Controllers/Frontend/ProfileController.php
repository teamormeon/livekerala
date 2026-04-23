<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Follower;
use App\Models\Listing;
use App\Models\Page;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->theme = template();
    }

    public function profile($user_name = null)
    {
        $user_information = User::with(['get_listing', 'get_social_links_user', 'follower.get_follwer_user', 'following.get_following_user'])
            ->withCount('totalViews')
            ->where('username', $user_name)
            ->firstOrFail();

        if (Auth::check()) {
            $data['check_follower'] = Follower::where('user_id', $user_information->id)->where('following_id', Auth::user()->id)->get();
        } else {
            $data['check_follower'] = Follower::where('user_id', $user_information->id)->get();
        }

        $listing_id = session()->get('listing_id');
        if ($listing_id) {
            session()->forget('listing_id');
            $data['latest_listings'] = Listing::with('get_user', 'get_reviews')
                ->where('id', '!=', $listing_id)
                ->where('user_id', $user_information->id)
                ->limit(3)
                ->withCount('getFavourite')
                ->where('status', 1)
                ->where('is_active', 1)
                ->latest()->paginate(20);
        } else {
            $data['latest_listings'] = Listing::with('get_user', 'get_reviews')
                ->where('user_id', $user_information->id)
                ->limit(3)->withCount('getFavourite')
                ->where('status', 1)
                ->where('is_active', 1)
                ->latest()->paginate(20);
        }

        $pageSeo = Page::where('slug', 'profile')->first();
        $pageSeo['breadcrumb_image'] = $pageSeo?->breadcrumb_status == 1 ?  getFile($pageSeo->breadcrumb_image_driver, $pageSeo->breadcrumb_image) : null;
        return view(template() . 'frontend.profile.index', $data, compact('user_information','pageSeo'));
    }


    public function profileFollow(Request $request, $userId = null){
        if ($userId != auth()->id()){
            $ifExists = Follower::where('user_id', $userId)->where('following_id',auth()->id())->latest()->first();
            if (!$ifExists){
                Follower::create([
                    'user_id' => $userId,
                    'following_id' => auth()->id(),
                    'created_at' => Carbon::now(),
                ]);
                session()->flash('success', __('Follow'));
                return back();
            }
        }else{
            return back()->with('error','You can\'t Follow own Profile');
        }
    }

    public function profileUnfollow(Request $request, $userId = null)
    {
        Follower::where('user_id',$userId)->where('following_id',auth()->id())->delete();
        session()->flash('success', __('UnFollow'));
        return back();
    }


}
