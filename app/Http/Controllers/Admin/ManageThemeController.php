<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BasicControl;
use App\Models\PageDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class ManageThemeController extends Controller
{
    public function manageTheme()
    {
        $themes = array_diff(array_keys(config('contents')), ['message', 'content_media']);
        return view('admin.manageTheme.index', compact('themes'));
    }

    public function manageThemeSelect($val)
    {
        try {
            $themes = array_diff(array_keys(config('contents')), ['message', 'content_media']);
            if (!in_array($val, $themes)) {
                return response()->json('failed');
            }
            $basicControl = BasicControl::firstOrFail();
            $basicControl->theme = $val;
            $basicControl->update();

            session()->forget('active_theme');
            Artisan::call('optimize:clear');
            return response()->json('success');
        }catch (\Exception $exception){
            return response()->json('failed');
        }
    }
}
