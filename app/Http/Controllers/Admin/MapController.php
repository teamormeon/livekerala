<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class MapController extends Controller
{
    public function mapConfig()
    {
        $basicControl = basicControl();
        return view('admin.control_panel.map_config', compact('basicControl'));
    }

    public function mapConfigUpdate(Request $request, $mapType = null)
    {
        $basicControl = basicControl();
        if ($mapType !== null) {
            $basicControl->is_google_map = ($mapType == 'google') ? 1 : 0;
        } else {
            $basicControl->google_map_app_key = $request->google_map_app_key;
            $basicControl->google_map_id = $request->google_map_id;
        }
        $basicControl->save();
        Artisan::call('optimize:clear');
        return back()->with('success', 'Updated Successfully.');
    }
}
