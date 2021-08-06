<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{

    public static $model = Category::class;

    public function queryIndex(&$query){
        if(request()->has('parents')){
            $query->whereNull('parent_id');
        }

        if(request()->has('children')){
            $query->whereNotNull('parent_id');
        }
    }

    public function post(Request $request)
    {
        $this->authorizeUserAction('create');

        $model = new static::$model;
        $data = $request->input();

        if($request->has('image') && $request->image){
            if($request->image){
                $data['image'] = $this->storeImage($this->decodeBase64toImage($request->image), $this->getExtensionBase64($request->image));
            }
        }

        $this->restfulService->validateResource($model, $data);

        $resource = $this->restfulService->persistResource(new $model($data));

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
