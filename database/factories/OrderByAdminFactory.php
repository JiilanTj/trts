<?php

namespace Database\Factories;

use App\Models\OrderByAdmin;
use App\Models\Product;
use App\Models\StoreShowcase;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrderByAdmin>
 */
class OrderByAdminFactory extends Factory
{
    protected $model = OrderByAdmin::class;

    public function definition(): array
    {
        $qty = $this->faker->numberBetween(1, 5);
        $unit = $this->faker->numberBetween(10_000, 100_000);

        return [
            'admin_id' => User::factory()->state(['role' => 'admin']),
            'user_id' => User::factory()->state(['role' => 'user']),
            'store_showcase_id' => StoreShowcase::factory(),
            'product_id' => Product::factory(),
            'adress' => $this->faker->streetAddress(),
            'quantity' => $qty,
            'unit_price' => $unit,
            'total_price' => $unit * $qty,
            'status' => OrderByAdmin::STATUS_PENDING,
        ];
    }

    public function confirmed(): self
    {
        return $this->state(fn () => ['status' => OrderByAdmin::STATUS_CONFIRMED]);
    }
}
