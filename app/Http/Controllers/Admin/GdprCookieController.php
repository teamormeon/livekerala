<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Traits\Upload;
use Illuminate\Http\Request;

class GdprCookieController extends Controller
{
    use Upload;

    public function gdprCookie()
    {
        $basicControl = basicControl();
        return view('admin.control_panel.gdpr_cookie', compact('basicControl'));
    }

    public function gdprCookieUpdate(Request $request)
    {
        $request->validate([
            'cookie_title' => 'required|string',
            'cookie_sub_title' => 'required|string',
            'cookie_description' => 'required|string',
            'cookie_status' => 'required|in:0,1',
            'cookie_image' => 'sometimes|required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        $basicControl = basicControl();
        if ($request->hasFile('cookie_image')) {
            try {
                $image = $this->fileUpload($request->cookie_image, config('filelocation.cookie.path'), null, null,'webp', 99, $basicControl->cookie_image, $basicControl->cookie_image_driver);
                if ($image) {
                    $cookie_image = $image['path'];
                    $cookie_image_driver = $image['driver'];
                }
            } catch (\Exception $exp) {
                return back()->with('error', 'Cookie image could not be uploaded.');
            }
        }
        $basicControl->cookie_title = $request->input('cookie_title');
        $basicControl->cookie_sub_title = $request->input('cookie_sub_title');
        $basicControl->cookie_description = $request->input('cookie_description');
        $basicControl->cookie_status = $request->input('cookie_status');
        $basicControl->cookie_image = $cookie_image ?? $basicControl->cookie_image;
        $basicControl->cookie_image_driver = $cookie_image_driver ?? $basicControl->cookie_image_driver;
        $basicControl->save();

        return back()->with('success', 'Cookie Information updated.');

    }
}
