<?php

namespace Database\Seeders;

use App\Enums\CourseLevel;
use App\Models\Course;
use App\Models\Category;
use Illuminate\Database\Seeder;

class CoursesSeeder extends BaseSeeder
{
    /**
     * Run fake seeds - for non production environments
     *
     * @return mixed
     */
    public function runFake()
    {
        Course::firstOrCreate([
            'name' => 'Vue.js Design Pattern',
            'slug' => 'vue-js-design-patter',
            'descriptions' => 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut `wisi enim ad minim laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat



            What You’ll Learn
            Setting up the environment
            Advanced HTML Practices
            Build a portfolio website
            Responsive Designs
            Understand HTML Programming
            Code HTML
            Start building beautiful websites


            <b>Requirements</b>
            Any computer will work: Windows, macOS or Linux
            Basic programming HTML and CSS.
            Basic/Minimal understanding of JavaScript


            Here is exactly what we cover in this course:


            Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Nam liber tempor cum soluta nobis eleifend option congue nihil imperdiet doming id quod mazim placerat facer possim assum. Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat.',
            'price' => 50000,
            'category_id' => Category::where('name', 'Web Development')->first()->id,
            'level' => CourseLevel::BEGINNER,
            'type' => 'VIDEO',
            'tags' => 'html',
            'trailer_url' => 'https://youtube.com/embed/6vG4oO39ivY',
        ]);
    }

    /**
     * Run seeds to be ran only on production environments
     *
     * @return mixed
     */
    public function runProduction()
    {
    }

    /**
     * Run seeds to be ran on every environment (including production)
     *
     * @return mixed
     */
    public function runAlways()
    {
    }
}
