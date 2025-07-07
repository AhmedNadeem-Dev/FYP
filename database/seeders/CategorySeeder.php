<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['category_id' => 1, 'name' => 'Aluminum', 'created_at' => now(), 'updated_at' => now()],
            ['category_id' => 2, 'name' => 'Copper', 'created_at' => now(), 'updated_at' => now()],
            ['category_id' => 3, 'name' => 'Brass', 'created_at' => now(), 'updated_at' => now()],
            ['category_id' => 4, 'name' => 'Steel', 'created_at' => now(), 'updated_at' => now()],
            ['category_id' => 5, 'name' => 'Iron', 'created_at' => now(), 'updated_at' => now()],
            ['category_id' => 6, 'name' => 'Lead', 'created_at' => now(), 'updated_at' => now()],
            ['category_id' => 7, 'name' => 'Other', 'created_at' => now(), 'updated_at' => now()],
            ['category_id' => 8, 'name' => 'Plastic', 'created_at' => now(), 'updated_at' => now()],
            ['category_id' => 9, 'name' => 'Glass', 'created_at' => now(), 'updated_at' => now()],
            ['category_id' => 10, 'name' => 'Electronics', 'created_at' => now(), 'updated_at' => now()],
        ];

        // Insert categories, ignore duplicates
        DB::table('categories')->insertOrIgnore($categories);
    }
} 