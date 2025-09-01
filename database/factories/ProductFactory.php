<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        // Ensure category id 1 exists (minimal) if not already inserted by test setup.
        if (!DB::table('categories')->where('id',1)->exists()) {
            DB::table('categories')->insert([
                'id' => 1,
                'name' => 'Default Kategori',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return [
            'name' => $this->faker->words(2, true),
            'sku' => 'SKU'.strtoupper(uniqid()),
            'category_id' => 1,
            'stock' => $this->faker->numberBetween(0,50),
            'purchase_price' => 10000,
            'promo_price' => null,
            'sell_price' => 20000,
            'profit' => 10000,
            'image' => null,
            'description' => $this->faker->sentence(),
            'weight' => (string)$this->faker->numberBetween(50,500),
            'expiry_date' => null,
            'status' => 'active',
        ];
    }
}
