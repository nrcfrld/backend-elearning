<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategoriesSeeder extends BaseSeeder
{
   /**
     * Run fake seeds - for non production environments
     *
     * @return mixed
     */
    public function runFake() {
        Category::firstOrCreate([
            'name' => 'Software Development',
            'descriptions' => 'Software Development',
        ]);

        Category::firstOrCreate([
            'name' => 'Design',
            'descriptions' => 'Design',
        ]);
    }

    /**
     * Run seeds to be ran only on production environments
     *
     * @return mixed
     */
    public function runProduction() {

    }

    /**
     * Run seeds to be ran on every environment (including production)
     *
     * @return mixed
     */
    public function runAlways() {

    }
}
