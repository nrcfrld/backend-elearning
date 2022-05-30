<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\UserLesson;
use App\Transformers\BaseTransformer;
use Illuminate\Http\Request;

class UserLessonController extends Controller
{
    public static $model = UserLesson::class;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Lesson $lesson, Request $request)
    {
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\UserLesson  $userLesson
     * @return \Illuminate\Http\Response
     */
    public function show(UserLesson $userLesson)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\UserLesson  $userLesson
     * @return \Illuminate\Http\Response
     */
    public function edit(UserLesson $userLesson)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\UserLesson  $userLesson
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Lesson $lesson)
    {
        $userLesson = UserLesson::updateOrCreate(
            ['user_id' => auth()->user()->id, 'lesson_id' => $lesson->id],
            ['status' => $request->status]
        );

        return $this->response->item($lesson, new BaseTransformer);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\UserLesson  $userLesson
     * @return \Illuminate\Http\Response
     */
    public function destroy(UserLesson $userLesson)
    {
        //
    }
}
