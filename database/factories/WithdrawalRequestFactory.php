<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\WithdrawalRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WithdrawalRequest>
 */
class WithdrawalRequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $amount = $this->faker->numberBetween(50000, 1000000);
        $adminFee = $amount * 0.025; // 2.5% admin fee
        
        return [
            'user_id' => User::factory(),
            'account_holder_name' => $this->faker->name(),
            'account_number' => $this->faker->numerify('####-####-####-####'),
            'bank_name' => $this->faker->randomElement([
                'Bank Central Asia (BCA)',
                'Bank Mandiri',
                'Bank Rakyat Indonesia (BRI)',
                'Bank Negara Indonesia (BNI)',
                'Bank CIMB Niaga',
                'Bank Danamon',
                'Bank Permata',
                'Bank Maybank',
                'Bank OCBC NISP',
                'Bank Panin'
            ]),
            'bank_code' => $this->faker->numerify('###'),
            'amount' => $amount,
            'admin_fee' => $adminFee,
            'total_deducted' => $amount + $adminFee,
            'status' => $this->faker->randomElement([
                WithdrawalRequest::STATUS_PENDING,
                WithdrawalRequest::STATUS_PROCESSING,
                WithdrawalRequest::STATUS_COMPLETED,
                WithdrawalRequest::STATUS_REJECTED
            ]),
            'notes' => $this->faker->optional()->sentence(),
            'admin_notes' => $this->faker->optional()->sentence(),
            'transaction_reference' => $this->faker->optional()->regexify('[A-Z0-9]{10}'),
        ];
    }

    /**
     * Indicate that the withdrawal request is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => WithdrawalRequest::STATUS_PENDING,
        ]);
    }

    /**
     * Indicate that the withdrawal request is processing.
     */
    public function processing(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => WithdrawalRequest::STATUS_PROCESSING,
            'processed_at' => now(),
            'processed_by' => User::factory(),
        ]);
    }

    /**
     * Indicate that the withdrawal request is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => WithdrawalRequest::STATUS_COMPLETED,
            'processed_at' => now()->subHours(2),
            'completed_at' => now(),
            'processed_by' => User::factory(),
            'transaction_reference' => $this->faker->regexify('[A-Z0-9]{12}'),
        ]);
    }

    /**
     * Indicate that the withdrawal request is rejected.
     */
    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => WithdrawalRequest::STATUS_REJECTED,
            'processed_at' => now(),
            'processed_by' => User::factory(),
            'admin_notes' => 'Rekening tidak valid atau dokumen tidak lengkap.',
        ]);
    }
}
