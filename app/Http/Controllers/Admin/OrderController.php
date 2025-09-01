<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
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
        $order->update([
            'payment_status' => 'paid',
            'payment_confirmed_at' => now(),
            'payment_confirmed_by' => $request->user()->id,
            // Move to packaging after payment approval
            'status' => 'packaging',
        ]);
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
        $next = $map[$order->status] ?? null;
        if (!$next) {
            return back()->withErrors(['order' => 'Tidak bisa lanjut status dari tahap ini.']);
        }
        $order->update(['status' => $next]);
        return back()->with('success', 'Status order berubah menjadi ' . $next . '.');
    }

    /** Manually set to cancelled */
    public function cancel(Request $request, Order $order)
    {
        if (in_array($order->status, ['completed','cancelled'])) {
            return back()->withErrors(['order' => 'Order sudah final.']);
        }
        $order->update(['status' => 'cancelled']);
        return back()->with('success', 'Order dibatalkan.');
    }
}
