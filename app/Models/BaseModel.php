<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Specialtactics\L5Api\Models\RestfulModel;

class BaseModel extends RestfulModel
{
    /*
     * Add your own base customisation here
     */

    public static function boot()
    {
       parent::boot();
       static::creating(function($model)
       {
           $user = Auth::user();
           if($user){
            if(Schema::hasColumn($model->getTable(), 'created_by')){
                $model->created_by = $user->id;
            }

            if(Schema::hasColumn($model->getTable(), 'updated_by')){
                $model->updated_by = $user->id;
            }
           }
       });
       static::updating(function($model)
       {
            $user = Auth::user();
            if($user){
                if(Schema::hasColumn($model->getTable(), 'updated_by')){
                    $model->updated_by = $user->id;
                }
            }
       });

       static::deleting(function($model)
       {
            $user = Auth::user();
            if($user){
                if(Schema::hasColumn($model->getTable(), 'deleted_by')){
                    $model->deleted_by = $user->id;
                }
            }
       });
   }
}
