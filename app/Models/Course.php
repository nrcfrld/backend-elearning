<?php

namespace App\Models;

use App\Enums\CourseLevel;
use BenSampo\Enum\Rules\EnumValue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends BaseModel
{
    use HasFactory, Sluggable, SoftDeletes;

    /**
     * @var string UUID key of the resource
     */
    public $primaryKey = 'id';

    /**
     * @var null|array What relations should one model of this entity be returned with, from a relevant controller
     */
    public static $itemWith = ['category', 'mentors'];

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
    protected $fillable = ['name', 'category_id', 'type', 'level', 'descriptions', 'price', 'max_participant', 'trailer_url', 'thumbnail', 'tags', 'minute'];

    /**
     * @var array The attributes that should be hidden for arrays and API output
     */
    protected $hidden = [];

    public function getThumbnailAttribute($thumbnail)
    {
        if($thumbnail){
            if(Storage::disk('public')->exists($thumbnail)){
                return Storage::disk('public')->url($thumbnail);
            }
        }

        return asset('/img/example-image.jpg');
    }

    public function getTagsAttribute($tags)
    {
        if($tags){
            return explode(',', $tags);
        }
        return [];
    }

    /**
     * Return the validation rules for this model
     *
     * @return array Rules
     */
    public function getValidationRules()
    {
        return [
            'name' => 'required',
            'price' => 'required',
            'type' => 'required',
            'level' => ['required', new EnumValue(CourseLevel::class)],
            'category_id' => 'required|exists:categories,id',
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

    public function category(){
        return $this->belongsTo(Category::class);
    }

    public function mentors(){
        return $this->belongsToMany(User::class, 'course_mentor', 'course_id', 'user_id');
    }
}
