<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /** List orders (filters by status, payment_status, user) */
    public function index(Request $request)
    {
        $query = Order::with('user','items.product')->latest();
        if ($status = $request->get('status')) { $query->where('status', $status); }
        if ($p = $request->get('payment_status')) { $query->where('payment_status', $p); }
        if ($uid = $request->get('user_id')) { $query->where('user_id', $uid); }
        $orders = $query->paginate(30);
        return view('admin.orders.index', compact('orders'));
    }

    /** Show single order */
    public function show(Order $order)
    {
        $order->load('user','items.product','confirmer');
        return view('admin.orders.show', compact('order'));
    }

    /** Approve payment (when waiting_confirmation) */
    public function approvePayment(Request $request, Order $order)
    {
        if ($order->payment_status !== 'waiting_confirmation') {
            return back()->withErrors(['order' => 'Status pembayaran tidak valid untuk approve.']);
        }
        
        DB::transaction(function () use ($order, $request) {
            $order->update([
                'payment_status' => 'paid',
                'payment_confirmed_at' => now(),
                'payment_confirmed_by' => $request->user()->id,
                // Move to packaging after payment approval
                'status' => 'packaging',
            ]);
            
            // Process seller margin payout and credit score increase
            $order->processSellerMarginPayout();
            
            // Create notification for payment approval
            $this->createOrderNotification($order, 'payment', 'Pembayaran Disetujui', 
                "Pembayaran untuk order #{$order->id} telah disetujui. Order akan segera dikemas.");
                
            // Create additional notification if margin was paid out
            if ($order->purchase_type === 'external' && $order->seller_margin_total > 0) {
                $this->createOrderNotification($order, 'payment', 'Margin Seller Diterima', 
                    "Margin sebesar Rp" . number_format($order->seller_margin_total, 0, ',', '.') . 
                    " telah ditambahkan ke saldo Anda. Credit score +5 poin!");
            }
        });
        
        return back()->with('success', 'Pembayaran disetujui, order masuk tahap dikemas.');
    }

    /** Reject / request reupload proof */
    public function rejectPayment(Request $request, Order $order)
    {
        if ($order->payment_status !== 'waiting_confirmation') {
            return back()->withErrors(['order' => 'Status pembayaran tidak valid untuk reject.']);
        }
        $data = $request->validate([
            'admin_notes' => 'nullable|string',
        ]);
        
        $order->update([
            'payment_status' => 'rejected',
            'status' => 'pending',
            'admin_notes' => $data['admin_notes'] ?? null,
        ]);
        
        // Create notification for payment rejection
        $rejectionReason = $data['admin_notes'] ? " Alasan: " . $data['admin_notes'] : "";
        $this->createOrderNotification($order, 'payment', 'Pembayaran Ditolak', 
            "Pembayaran untuk order #{$order->id} ditolak. Silakan upload ulang bukti pembayaran yang valid.{$rejectionReason}");
            
        return back()->with('success', 'Pembayaran ditolak. User diminta upload ulang.');
    }

    /** Transition workflow status: packaging -> shipped -> delivered -> completed */
    public function advanceStatus(Request $request, Order $order)
    {
        $map = [
            'packaging' => 'shipped',
            'shipped' => 'delivered',
            'delivered' => 'completed',
        ];
        
        $statusLabels = [
            'shipped' => 'Dikirim',
            'delivered' => 'Diterima', 
            'completed' => 'Selesai',
        ];
        
        $next = $map[$order->status] ?? null;
        if (!$next) {
            return back()->withErrors(['order' => 'Tidak bisa lanjut status dari tahap ini.']);
        }
        
        $order->update(['status' => $next]);
        
        // Create notification for status change
        $statusLabel = $statusLabels[$next] ?? ucfirst($next);
        $descriptions = [
            'shipped' => "Order #{$order->id} telah dikirim dan sedang dalam perjalanan ke alamat tujuan.",
            'delivered' => "Order #{$order->id} telah sampai di alamat tujuan. Silakan konfirmasi penerimaan barang.",
            'completed' => "Order #{$order->id} telah selesai. Terima kasih telah berbelanja!",
        ];
        
        $this->createOrderNotification($order, 'order', "Order {$statusLabel}", 
            $descriptions[$next] ?? "Status order #{$order->id} berubah menjadi {$statusLabel}.");
            
        return back()->with('success', 'Status order berubah menjadi ' . $next . '.');
    }

    /** Manually set to cancelled */
    public function cancel(Request $request, Order $order)
    {
        if (in_array($order->status, ['completed','cancelled'])) {
            return back()->withErrors(['order' => 'Order sudah final.']);
        }
        $order->update(['status' => 'cancelled']);
        
        // Create notification for user
        $this->createOrderNotification($order, 'order', 'Order Dibatalkan', 
            "Order #{$order->id} telah dibatalkan oleh admin.");
            
        return back()->with('success', 'Order dibatalkan.');
    }

    /**
     * Create notification for order-related actions
     */
    private function createOrderNotification(Order $order, string $category, string $title, string $description): void
    {
        Notification::create([
            'for_user_id' => $order->user_id,
            'category' => $category,
            'title' => $title,
            'description' => $description,
        ]);
    }
}
