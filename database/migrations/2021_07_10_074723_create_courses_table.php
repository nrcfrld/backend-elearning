<?php

use App\Enums\CourseLevel;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoursesTable extends Migration
{
    const TABLE_NAME = 'courses';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(static::TABLE_NAME, function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('slug');
            $table->text('descriptions')->nullable();
            $table->string('thumbnail')->nullable();
            $table->enum('level', CourseLevel::getValues());
            $table->enum('type', ['VIDEO', 'ONSITE']);
            $table->uuid('category_id')->foreign()->references('id')->on('categories')->onUpdate('cascade');
            $table->decimal('price', 18, 2);

            $table->string('tags')->nullable();
            $table->integer('max_participant')->nullable();
            $table->string('trailer_url')->nullable();
            $table->integer('minutes');


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
        Schema::dropIfExists('courses');
    }
}
