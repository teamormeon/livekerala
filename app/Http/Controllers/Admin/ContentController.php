<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Content;
use App\Models\ContentDetails;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Validator;
use App\Traits\Upload;


class ContentController extends Controller
{
    use Upload;

    public function index($content)
    {
        $currentTheme = basicControl()->theme;
        if (!array_key_exists($content, config('contents.'. $currentTheme))) {
            abort(404);
        }

        $data['singleContent'] = config('contents.' .$currentTheme. '.' . $content . '.single');
        $data['multipleContents'] = config('contents.' .$currentTheme. '.'  . $content . '.multiple');
        $data['languages'] = Language::orderBy('default_status', 'desc')->get();
        $defaultLanguage = $data['languages']->where('default_status', true)->first();

        $data['singleContentData'] = ContentDetails::with('content')
            ->whereHas('content', function ($query) use ($content, $currentTheme) {
                $query->where('name', $content);
                $query->where('theme', $currentTheme);
                $query->where('type', 'single');
            })->get()->groupBy('language_id');


        $data['multipleContentData'] = ContentDetails::with('content')
            ->whereHas('content', function ($query) use ($content, $currentTheme) {
                $query->where('name', $content);
                $query->where('theme', $currentTheme);
                $query->where('type', 'multiple');
            })->where('language_id', $defaultLanguage->id)
            ->get();
        $data['contentPreview'] = config('contents.' .$currentTheme. '.' . $content . '.contentPreview');
        return view('admin.frontend_management.content.index', $data, compact('content'));

    }

    public function store(Request $request, $content, $language)
    {

        $currentTheme = basicControl()->theme;
        if (!array_key_exists($content, config('contents.'. $currentTheme))) {
            abort(404);
        }

        $inputData = $request->except('_token', '_method');
        $validate = Validator::make($inputData, config('contents.' .$currentTheme. '.' . $content . '.single.validation'), config('contents.message'));

        if ($validate->fails()) {
            $validate->errors()->add('errActive', $language);
            return back()->withInput()->withErrors($validate);
        }

        $singleContent = Content::updateOrCreate(
            ['name' => $content, 'theme' => $currentTheme, 'type' => 'single'],
            ['name' => $content, 'theme' => $currentTheme, 'type' => 'single']
        );

        $mediaData = (array) ($singleContent->media ?? []);
        foreach (config('contents.content_media') as $key => $media) {
            $oldData = $singleContent->media->{$key} ?? null;
            if ($request->hasFile($key)) {
                try {
                    $size = config('contents.' .$currentTheme. '.' . $content . '.single.size.image');
                    $image = $this->fileUpload(
                        $request->$key,
                        config('filelocation.contents.path'),
                        null,
                        $size,
                        'webp',
                        80,
                        $oldData->path ?? null,
                        $oldData->driver ?? 'local'
                    );
                    $mediaData[$key] = $image;
                } catch (\Exception $exp) {
                    return back()->with('error', 'Image could not be uploaded.');
                }
            } elseif ($request->has($key)) {
                $mediaData[$key] = $inputData[$key][$language];
            } elseif (isset($oldData)) {
                $mediaData[$key] = $oldData;
            }
        }

        if (!empty($mediaData)) {
            $singleContent->media = $mediaData;
            $singleContent->save();
        }

        $description = [];
        $field_name = array_diff_key(config('contents.' .$currentTheme. '.' . $content . '.single.field_name'), config("contents.content_media"));
        foreach ($field_name as $name => $type) {
            $description[$name] = $inputData[$name][$language] ?? null;
        }

        $contentDetails = ContentDetails::updateOrCreate(
            ['content_id' => $singleContent->id, 'language_id' => $language],
            ['content_id' => $singleContent->id, 'language_id' => $language, 'description' => $description ?: null]
        );

        if (!$contentDetails) {
            return back()->with('error', 'Something went wrong. Please try again.');
        }

        $this->clearFrontendCaches();
        return back()->with('success', 'Content created successfully.');
    }

    public function manageContentMultiple($content)
    {
        $currentTheme = basicControl()->theme;
        if (!array_key_exists($content, config('contents.'. $currentTheme))) {
            abort(404);
        }

        $data['languages'] = Language::orderBy('default_status', 'desc')->get();
        $data['multipleContent'] = config('contents.' .$currentTheme. '.' . $content . '.multiple');

        return view('admin.frontend_management.content.create', $data, compact('content'));
    }

    public function manageContentMultipleStore(Request $request, $content, $language)
    {
        $currentTheme = basicControl()->theme;
        if (!array_key_exists($content, config('contents.'. $currentTheme))) {
            abort(404);
        }

        $inputData = $request->except('_token', '_method');
        $validate = Validator::make($inputData, config('contents.' .$currentTheme. '.' . $content . '.multiple.validation'), config('contents.message'));

        if ($validate->fails()) {
            $validate->errors()->add('errActive', $language);
            return back()->withInput()->withErrors($validate);
        }

        $multipleContent = Content::create(['name' => $content, 'theme' => $currentTheme, 'type' => 'multiple']);

        $mediaData = [];
        foreach (config('contents.content_media') as $key => $media) {
            $oldData = $multipleContent->media->{$key} ?? null;

            if ($request->hasFile($key)) {
                try {
                    $size = config('contents.' .$currentTheme. '.' . $content . '.multiple.size.image');
                    $image = $this->fileUpload(
                        $request->$key,
                        config('filelocation.contents.path'),
                        null,
                        $size,
                        'webp',
                        99,
                        $oldData->path ?? null,
                        $oldData->driver ?? 'local'
                    );
                    $mediaData[$key] = $image;
                } catch (\Exception $exp) {
                    return back()->with('error', 'Image could not be uploaded.');
                }
            } elseif ($request->has($key)) {
                $mediaData[$key] = $inputData[$key][$language];
            } elseif (isset($oldData)) {
                $mediaData[$key] = $oldData;
            }
        }

        if (!empty($mediaData)) {
            $multipleContent->media = $mediaData;
            $multipleContent->save();
        }

        $description = [];
        $field_name = array_diff_key(config('contents.' .$currentTheme. '.' . $content . '.multiple.field_name'), config("contents.content_media"));
        foreach ($field_name as $name => $type) {
            $description[$name] = $inputData[$name][$language] ?? null;
        }

        $contentDetails = ContentDetails::create([
            'content_id' => $multipleContent->id,
            'language_id' => $language,
            'description' => $description ?: null
        ]);

        if (!$contentDetails) {
            throw new \Exception("Something went wrong, Please try again");
        }

        $this->clearFrontendCaches();
        return back()->with('success', 'Created Successfully');
    }

    public function multipleContentItemEdit($content, $id)
    {
        $currentTheme = basicControl()->theme;
        if (!array_key_exists($content, config('contents.'. $currentTheme))) {
            abort(404);
        }
        $data['languages'] = Language::orderBy('default_status', 'desc')->get();
        $data['multipleContent'] = config('contents.' .$currentTheme. '.' . $content . '.multiple');

        $data['multipleContentData'] = ContentDetails::with('content')
            ->where('content_id', $id)
            ->whereHas('content', function ($query) use ($content, $currentTheme) {
                $query->where('name', $content);
                $query->where('theme', $currentTheme);
                $query->where('type', 'multiple');
            })
            ->get()->groupBy('language_id');
        return view('admin.frontend_management.content.edit', $data, compact('content', 'id'));
    }

    public function multipleContentItemUpdate(Request $request, $content, $id, $language)
    {
        $currentTheme = basicControl()->theme;
        if (!array_key_exists($content, config('contents.'. $currentTheme))) {
            abort(404);
        }

        $inputData = $request->except('_token', '_method');
        $validate = Validator::make($inputData, config('contents.' .$currentTheme. '.' . $content . '.multiple.validation'), config('contents.message'));

        if ($validate->fails()) {
            $validate->errors()->add('errActive', $language);
            return back()->withInput()->withErrors($validate);
        }

        $multipleContent = Content::findOrFail($id);
        $mediaData = (array) ($multipleContent->media ?? []);
        foreach (config('contents.content_media') as $key => $media) {
            $oldData = $multipleContent->media->{$key} ?? null;
            if ($request->hasFile($key)) {
                try {
                    $size = config('contents.' .$currentTheme. '.' . $content . '.multiple.size.image');
                    $image = $this->fileUpload(
                        $request->$key,
                        config('filelocation.contents.path'),
                        null,
                        $size,
                        'webp',
                        99,
                        $oldData->path ?? null,
                        $oldData->driver ?? 'local'
                    );
                    $mediaData[$key] = $image;
                } catch (\Exception $exp) {
                    return back()->with('error', 'Image could not be uploaded.');
                }
            } elseif ($request->has($key)) {
                $mediaData[$key] = $inputData[$key][$language];
            } elseif (isset($oldData)) {
                $mediaData[$key] = $oldData;
            }
        }

        if (!empty($mediaData)) {
            $multipleContent->media = $mediaData;
            $multipleContent->save();
        }

        $description = [];
        $field_name = array_diff_key(config('contents.' .$currentTheme. '.' . $content . '.multiple.field_name'), config("contents.content_media"));

        foreach ($field_name as $name => $type) {
            $description[$name] = $inputData[$name][$language] ?? null;
        }

        $contentDetails = ContentDetails::updateOrCreate(
            ['content_id' => $id, 'language_id' => $language],
            ['content_id' => $id, 'language_id' => $language, 'description' => $description ?: null]
        );

        if (!$contentDetails) {
            throw new \Exception("Something went wrong, Please try again");
        }

        $this->clearFrontendCaches();
        return back()->with('success', 'Created Successfully');
    }


    public function ContentDelete($id)
    {
        try {
            $content = Content::findOrFail($id);
            $content->delete();

            $this->fileDelete(optional(optional($content->media)->image)->driver, optional(optional($content->media)->image)->path);

            $contentDetails = ContentDetails::where('content_id', $id)->get();
            foreach ($contentDetails as $detail) {
                $detail->delete();
            }

            $this->clearFrontendCaches();
            return back()->with('success', 'Content has been deleted');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

    }

    protected function clearFrontendCaches(): void
    {
        Artisan::call('optimize:clear');
    }
}
