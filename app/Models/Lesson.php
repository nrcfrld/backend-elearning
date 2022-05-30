<?php

namespace App\Models;

use App\Enums\UserLessonStatus;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lesson extends BaseModel
{
    use HasFactory, Sluggable;

    /**
     * @var string UUID key of the resource
     */
    public $primaryKey = 'id';

    /**
     * @var null|array What relations should one model of this entity be returned with, from a relevant controller
     */
    public static $itemWith = [];

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
    protected $fillable = ['name', 'descriptions', 'video_url', 'free_access', 'chapter_id', 'minutes'];

    /**
     * @var array The attributes that should be hidden for arrays and API output
     */
    protected $hidden = [];

    protected $appends = ['youtube_id', 'is_completed'];


    public function getYoutubeIdAttribute()
    {
        if ($this->video_url) {
            $exploded = explode("/", $this->video_url);
            return end($exploded);
        }
    }

    /**
     * Return the validation rules for this model
     *
     * @return array Rules
     */
    public function getValidationRules()
    {
        return [];
    }

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }

    public function chapter()
    {
        return $this->belongsTo(Chapter::class);
    }

    public function getIsCompletedAttribute()
    {
        if (auth()->user()) {
            $userLesson = UserLesson::where([
                ['lesson_id', $this->id],
                ['created_by', auth()->user()->id]
            ])->first();

            if ($userLesson && $userLesson->status === UserLessonStatus::DONE) {
                return true;
            }

            return false;
        } else {
            return false;
        }
    }
}
