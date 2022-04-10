<?php

use App\Enums\UserLessonStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserLessonsTable extends Migration
{
    const TABLE_NAME = 'user_lessons';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(static::TABLE_NAME, function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->index();
            $table->uuid('lesson_id')->index();
            $table->enum('status', UserLessonStatus::getValues())->index();

            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->uuid('deleted_by')->nullable();

            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade');
            $table->foreign('updated_by')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade');
            $table->foreign('deleted_by')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_lessons');
    }
}
