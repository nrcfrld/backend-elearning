<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\CourseMentorController;
use App\Services\RestfulService;

class CourseController extends Controller
{
    public static $model = Course::class;

    public function uploadThumbnail(Course $uuid, Request $request)
    {
        $request->validate([
            'image' => 'required|image'
        ]);

        $filename = $request->image->store('image', 'public');

        $uuid->update([
            'thumbnail' => $filename
        ]);

        return $this->response->noContent()->setStatusCode(204);
    }

    private function storeImage($image)
    {
        $randomString = Str::random(40);
        Storage::put("public/images/$randomString.jpg", $image, 'public');
        return "images/$randomString.jpg";
    }


    private function decodeBase64toImage($base64_string)
    {
        if (strpos($base64_string, 'base64') !== false) {
            $data = explode(',', $base64_string);
            return base64_decode($data[1]);
        } else {
            return $base64_string;
        }
    }

    public function post(Request $request)
    {
        $this->authorizeUserAction('create');

        $model = new static::$model;
        $data = $request->input();

        if($request->has('thumbnail') && $request->thumbnail){
            if($request->thumbnail){
                $data['thumbnail'] = $this->storeImage($this->decodeBase64toImage($request->thumbnail));
            }
        }

        $this->restfulService->validateResource($model, $data);

        $resource = $this->restfulService->persistResource(new $model($data));

        if($request->has('mentors') && $request->mentors){
            $mentors = json_decode($request->mentors);
            foreach($mentors as $mentor){
                (new CourseMentorController(new RestfulService))->post(new Request([
                    'user_id' => $mentor->id,
                    'course_id' => $resource->id
                ]));
            }
        }

        // Retrieve full model
        $resource = $model::with($model::getItemWith())->where($model->getKeyName(), '=', $resource->getKey())->first();

        if ($this->shouldTransform()) {
            $response = $this->response->item($resource, $this->getTransformer())->setStatusCode(201);
        } else {
            $response = $resource;
        }

        return $response;
    }
}
