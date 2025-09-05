<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    public function index()
    {
        // Dummy notifications data
        $notifications = [
            [
                'id' => 1,
                'type' => 'order',
                'title' => 'Pesanan Dikonfirmasi',
                'message' => 'Pesanan #ORD-2024-001 telah dikonfirmasi dan sedang diproses.',
                'time' => '2 jam yang lalu',
                'read' => false,
                'icon' => 'check-circle',
                'color' => 'emerald'
            ],
            [
                'id' => 2,
                'type' => 'payment',
                'title' => 'Pembayaran Berhasil',
                'message' => 'Pembayaran sebesar Rp250.000 telah berhasil diproses.',
                'time' => '5 jam yang lalu',
                'read' => true,
                'icon' => 'credit-card',
                'color' => 'blue'
            ],
            [
                'id' => 3,
                'type' => 'system',
                'title' => 'Pembaruan Sistem',
                'message' => 'Sistem telah diperbarui dengan fitur-fitur terbaru.',
                'time' => '1 hari yang lalu',
                'read' => true,
                'icon' => 'cog',
                'color' => 'purple'
            ],
            [
                'id' => 4,
                'type' => 'promo',
                'title' => 'Promo Spesial',
                'message' => 'Dapatkan diskon 20% untuk pembelian berikutnya!',
                'time' => '2 hari yang lalu',
                'read' => true,
                'icon' => 'gift',
                'color' => 'pink'
            ],
            [
                'id' => 5,
                'type' => 'security',
                'title' => 'Login dari Perangkat Baru',
                'message' => 'Akun Anda telah login dari perangkat baru pada 3 Jan 2025.',
                'time' => '3 hari yang lalu',
                'read' => true,
                'icon' => 'shield-check',
                'color' => 'amber'
            ],
            [
                'id' => 6,
                'type' => 'order',
                'title' => 'Pesanan Dikirim',
                'message' => 'Pesanan #ORD-2024-002 telah dikirim dan dalam perjalanan.',
                'time' => '4 hari yang lalu',
                'read' => true,
                'icon' => 'truck',
                'color' => 'cyan'
            ],
        ];
        
        return view('user.history.index', compact('notifications'));
    }
}
