<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public static $model = User::class;

    public function updateAvatar(User $user ,Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:5120'
        ]);

        $filename = $request->image->store('images', 'public');
        $oldfileexists = Storage::disk('public')->exists($user->avatar);
        //Delete old avatar
        if($oldfileexists){
            Storage::disk('public')->delete($user->avatar);
        }

        $user->avatar = $filename;
        $user->save();

        return Storage::disk('public')->url($filename);
    }

    /**
     * Request to create or replace a resource
     *
     * @param Request $request
     * @param string $uuid
     * @return \Dingo\Api\Http\Response
     */
    public function put(Request $request, $uuid)
    {
        $model = static::$model::find($uuid);

        if (! $model) {
            // Doesn't exist - create
            $this->authorizeUserAction('create');

            $model = new static::$model;

            $this->restfulService->validateResource($model, $request->input());
            $resource = $this->restfulService->persistResource(new $model($request->input()));

            $resource->loadMissing($model::getItemWith());

            if ($this->shouldTransform()) {
                $response = $this->response->item($resource, $this->getTransformer())->setStatusCode(201);
            } else {
                $response = $resource;
            }
        } else {
            // Exists - replace
            $this->authorizeUserAction('update', $model);
            $data = $request->input();

            if($request->has('email') && $request->email == $model->email){
                $data = $request->except('email');
            }

            $this->restfulService->validateResourceUpdate($model, $data);
            $this->restfulService->persistResource($model->fill($data));

            if ($this->shouldTransform()) {
                $response = $this->response->item($model, $this->getTransformer())->setStatusCode(200);
            } else {
                $response = $model;
            }
        }

        return $response;
    }

    public function queryIndex(&$query)
    {
        if(request()->has('role')){
            $rolename = request('role');
            $query->whereHas('role' , function($q) use ($rolename) {
                $q->where('name', $rolename);
            });
        }
    }
}
