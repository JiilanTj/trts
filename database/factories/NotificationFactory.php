<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Notification>
 */
class NotificationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categories = ['order', 'payment', 'system', 'promotion'];
        $category = $this->faker->randomElement($categories);
        
        $titles = [
            'order' => [
                'Order Baru Diterima',
                'Order Dikonfirmasi', 
                'Order Sedang Dikemas',
                'Order Telah Dikirim',
                'Order Selesai',
            ],
            'payment' => [
                'Pembayaran Berhasil',
                'Pembayaran Ditolak',
                'Menunggu Konfirmasi Pembayaran',
                'Margin Seller Telah Ditambahkan',
            ],
            'system' => [
                'Maintenance Server',
                'Update Aplikasi',
                'Perubahan Kebijakan',
                'Fitur Baru Tersedia',
            ],
            'promotion' => [
                'Diskon Spesial Hari Ini',
                'Flash Sale Dimulai',
                'Cashback untuk Member Baru',
                'Promo Weekend Sale',
            ],
        ];

        $descriptions = [
            'order' => [
                'Order #ORD001 telah diterima dan sedang diproses.',
                'Pembayaran order telah dikonfirmasi admin.',
                'Order sedang dikemas dan akan segera dikirim.',
                'Order telah dikirim dengan nomor resi REK123456.',
                'Order telah diselesaikan. Terima kasih!',
            ],
            'payment' => [
                'Pembayaran sebesar Rp 150.000 telah berhasil diproses.',
                'Pembayaran ditolak. Silakan upload bukti transfer yang valid.',
                'Bukti pembayaran telah diterima, menunggu konfirmasi admin.',
                'Margin seller sebesar Rp 25.000 telah ditambahkan ke saldo.',
            ],
            'system' => [
                'Maintenance server dijadwalkan pada 02:00 - 04:00 WIB.',
                'Aplikasi telah diupdate dengan fitur dan perbaikan terbaru.',
                'Kebijakan privasi telah diperbarui. Silakan baca selengkapnya.',
                'Fitur chat customer service telah tersedia.',
            ],
            'promotion' => [
                'Dapatkan diskon hingga 50% untuk semua produk hari ini!',
                'Flash sale dimulai! Buruan sebelum kehabisan.',
                'Dapatkan cashback 10% untuk pembelian pertama.',
                'Weekend sale dengan potongan harga menarik.',
            ],
        ];

        return [
            'for_user_id' => User::factory(),
            'category' => $category,
            'title' => $this->faker->randomElement($titles[$category]),
            'description' => $this->faker->randomElement($descriptions[$category]),
            'created_at' => $this->faker->dateTimeBetween('-30 days', 'now'),
        ];
    }
}
