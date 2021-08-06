<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends BaseModel
{
    use HasFactory, Sluggable, SoftDeletes;

    /**
     * @var string UUID key of the resource
     */
    public $primaryKey = 'id';

    /**
     * @var null|array What relations should one model of this entity be returned with, from a relevant controller
     */
    public static $itemWith = ['parent'];

    /**
     * @var null|array What relations should a collection of models of this entity be returned with, from a relevant controller
     * If left null, then $itemWith will be used
     */
    public static $collectionWith = null;

    /**
     * @var null|BaseTransformer The transformer to use for this model, if overriding the default
     */
    public static $transformer = null;

    /**
     * @var array The attributes that are mass assignable.
     */
    protected $fillable = ['name', 'descriptions', 'slug', 'parent_id', 'image'];

    /**
     * @var array The attributes that should be hidden for arrays and API output
     */
    protected $hidden = [];

    protected $appends = ['courses_total'];

    /**
     * Return the validation rules for this model
     *
     * @return array Rules
     */
    public function getValidationRules()
    {
        return [
            'name'  => 'required|min:3',
            'descriptions' => 'required',
        ];
    }

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }

    public function parent(){
        return $this->belongsTo(Category::class, 'parent_id', 'id');
    }

    public function courses(){
        return $this->hasMany(Course::class);
    }

    public function getImageAttribute($image)
    {
        if($image){
            if(Storage::disk('public')->exists($image)){
                return Storage::disk('public')->url($image);
            }
        }

        return "https://ui-avatars.com/api/?name={$this->name}&color=7F9CF5&background=EBF4FF";
    }

    public function getCoursesTotalAttribute()
    {
        return $this->courses()->count();
    }
}
