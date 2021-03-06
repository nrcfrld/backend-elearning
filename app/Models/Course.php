<?php

namespace App\Models;

use App\Enums\CourseLevel;
use BenSampo\Enum\Rules\EnumValue;
use Carbon\Carbon;
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
    public static $itemWith = ['category', 'mentors', 'mainMentor'];

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
    protected $fillable = ['name', 'category_id', 'type', 'level', 'descriptions', 'price', 'max_participant', 'trailer_url', 'thumbnail', 'tags', 'is_featured'];

    /**
     * @var array The attributes that should be hidden for arrays and API output
     */
    protected $hidden = [];

    protected $appends = ['total_enrolled', 'total_lessons', 'minutes', 'is_completed'];

    public function getThumbnailAttribute($thumbnail)
    {
        if ($thumbnail) {
            if (Storage::disk('public')->exists($thumbnail)) {
                return Storage::disk('public')->url($thumbnail);
            }
        }

        return asset('/img/example-image.jpg');
    }

    public function getTotalEnrolledAttribute()
    {
        return $this->users()->count();
    }

    public function getTotalLessonsAttribute()
    {
        $total = 0;
        $chapters = $this->chapters()->withCount('lessons')->get();

        foreach ($chapters as $chapter) {
            $total += $chapter->lessons_count;
        }

        return $total;
    }

    public function getMinutesAttribute()
    {
        $total = 0;
        foreach ($this->chapters as $chapter) {
            $total += $chapter->lessons->sum('minutes');
        }
        return $total;
    }

    public function getTagsAttribute($tags)
    {
        if ($tags) {
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

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function mentors()
    {
        return $this->belongsToMany(User::class, 'course_mentor', 'course_id', 'user_id');
    }

    public function users()
    {
        return $this->hasMany(UserCourse::class);
    }

    public function chapters()
    {
        return $this->hasMany(Chapter::class);
    }

    public function mainMentor()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function getIsCompletedAttribute()
    {
        foreach ($this->chapters as $chapter) {
            if (!$chapter->is_completed) {
                return false;
            }
        }

        return true;
    }
}
