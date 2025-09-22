<?php

namespace Database\Factories;

use App\Models\StoreShowcase;
use App\Models\User;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StoreShowcase>
 */
class StoreShowcaseFactory extends Factory
{
    protected $model = StoreShowcase::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'product_id' => Product::factory(),
            'sort_order' => $this->faker->numberBetween(1, 100),
            'is_featured' => false,
            'is_active' => true,
            'featured_until' => null,
            'share_token' => null, // akan diisi otomatis di booted()
        ];
    }
}
