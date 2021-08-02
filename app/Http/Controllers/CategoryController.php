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
}
