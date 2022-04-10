<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use App\Http\Controllers\CourseMentorController;
use App\Models\User;
use App\Services\CertificateService;
use App\Services\RestfulService;
use Carbon\Carbon;
use setasign\Fpdi\Fpdi;
use Storage;

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

    public function post(Request $request)
    {
        $this->authorizeUserAction('create');

        $model = new static::$model;
        $data = $request->input();

        if ($request->has('thumbnail') && $request->thumbnail) {
            if ($request->thumbnail) {
                $data['thumbnail'] = $this->storeImage($this->decodeBase64toImage($request->thumbnail), $this->getExtensionBase64($request->thumbnail));
            }
        }

        $this->restfulService->validateResource($model, $data);

        $resource = $this->restfulService->persistResource(new $model($data));

        if ($request->has('mentors') && $request->mentors) {
            $mentors = json_decode($request->mentors);
            foreach ($mentors as $mentor) {
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

    public function certificate(Request $request)
    {
        $course = Course::first();
        $user = User::first();
        $filename = (new CertificateService())->generate($course, $user, Carbon::now()->format('Y-m-d'));

        return Storage::download($filename);

        // return $this->response->array([[
        //     'data' => [
        //         'certificate_url' => asset(Storage::url($filename))
        //     ]
        // ]]);
    }
}
