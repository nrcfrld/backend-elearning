<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(RoleTableSeeder::class);
        $this->call(UserStorySeeder::class);
        $this->call(CategoriesSeeder::class);
        $this->call(CoursesSeeder::class);
    }
}
