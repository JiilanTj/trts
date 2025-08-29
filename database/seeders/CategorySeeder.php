<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Electronics',
                'description' => 'Electronic devices and gadgets',
                'status' => 'active',
            ],
            [
                'name' => 'Fashion',
                'description' => 'Clothing and fashion accessories',
                'status' => 'active',
            ],
            [
                'name' => 'Home & Garden',
                'description' => 'Home improvement and garden supplies',
                'status' => 'active',
            ],
            [
                'name' => 'Sports & Recreation',
                'description' => 'Sports equipment and recreational items',
                'status' => 'active',
            ],
            [
                'name' => 'Books & Media',
                'description' => 'Books, magazines, and media content',
                'status' => 'active',
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
