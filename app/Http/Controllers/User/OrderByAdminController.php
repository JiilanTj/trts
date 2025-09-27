<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\OrderByAdmin;
use App\Models\User;
use App\Models\Notification; // add
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderByAdminController extends Controller
{
    // GET /orders-by-admin (paginated)
    public function index(Request $request)
    {
        $userId = Auth::id();

        // Normalize status filter to enum casing
        $status = $request->query('status');
        $normalizedStatus = $status ? strtoupper($status) : null;

        $orders = OrderByAdmin::query()
            ->with(['admin:id,username,full_name', 'storeShowcase', 'product'])
            ->where('user_id', $userId)
            ->when($normalizedStatus, function ($q, $st) {
                $q->where('status', $st);
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        // If Blade view exists, render the page with status filter tabs and counts
        if (view()->exists('user.orders-by-admin.index')) {
            $statusOptions = [
                OrderByAdmin::STATUS_PENDING => 'Pending',
                OrderByAdmin::STATUS_CONFIRMED => 'Confirmed',
                OrderByAdmin::STATUS_PACKED => 'Packed',
                OrderByAdmin::STATUS_SHIPPED => 'Shipped',
                OrderByAdmin::STATUS_DELIVERED => 'Delivered',
            ];

            $counts = OrderByAdmin::query()
                ->select('status', DB::raw('COUNT(*) as c'))
                ->where('user_id', $userId)
                ->groupBy('status')
                ->pluck('c', 'status')
                ->toArray();

            return view('user.orders-by-admin.index', [
                'orders' => $orders,
                'status' => $normalizedStatus,
                'statusOptions' => $statusOptions,
                'statusCounts' => $counts,
            ]);
        }

        return response()->json($orders);
    }

    // GET /orders-by-admin/{orders_by_admin}
    public function show(OrderByAdmin $orders_by_admin)
    {
        $this->authorizeAccess($orders_by_admin);
        $orders_by_admin->load(['admin:id,username,full_name', 'storeShowcase', 'product']);

        if (view()->exists('user.orders-by-admin.show')) {
            return view('user.orders-by-admin.show', ['order' => $orders_by_admin]);
        }

        return response()->json($orders_by_admin);
    }

    // PATCH /orders-by-admin/{orders_by_admin}/confirm
    public function confirm(OrderByAdmin $orders_by_admin)
    {
        $this->authorizeAccess($orders_by_admin);

        if ($orders_by_admin->status !== OrderByAdmin::STATUS_PENDING) {
            if (view()->exists('user.orders-by-admin.show')) {
                return back()->withErrors(['status' => 'Order tidak dalam status PENDING.']);
            }
            return response()->json([
                'message' => 'Order tidak dalam status PENDING.'
            ], 422);
        }

        $authUser = Auth::user();
        $finalOrder = null;

        try {
            DB::transaction(function () use ($orders_by_admin, $authUser, &$finalOrder) {
                // Lock both order and user row to avoid race conditions
                /** @var OrderByAdmin $order */
                $order = OrderByAdmin::whereKey($orders_by_admin->getKey())
                    ->lockForUpdate()
                    ->firstOrFail();

                /** @var User $user */
                $user = User::whereKey($authUser->getKey())
                    ->lockForUpdate()
                    ->firstOrFail();

                // Re-validate state under lock
                if ($order->status !== OrderByAdmin::STATUS_PENDING) {
                    throw ValidationException::withMessages([
                        'status' => 'Order tidak dalam status PENDING.'
                    ]);
                }

                if (!$user->hasSufficientBalance((int)$order->total_price)) {
                    throw ValidationException::withMessages([
                        'balance' => 'Saldo tidak mencukupi.'
                    ]);
                }

                // Deduct and confirm
                $user->deductBalance((int)$order->total_price);
                $order->status = OrderByAdmin::STATUS_CONFIRMED;
                $order->save();

                $finalOrder = $order->fresh(['admin:id,username,full_name', 'storeShowcase', 'product']);
            });
        } catch (ValidationException $ve) {
            if (view()->exists('user.orders-by-admin.show')) {
                return back()->withErrors($ve->errors());
            }
            throw $ve;
        }

        // Create notifications for the user
        try {
            $amount = (int) ($finalOrder->total_price ?? 0);
            $marginPercent = (int) ($authUser->getLevelMarginPercent() ?? 0);
            $profit = (int) round($amount * $marginPercent / 100);
            $totalPlusProfit = $amount + $profit;

            $orderCode = date('dmy') . 'P' . substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 8);
            
            Notification::create([
                'for_user_id' => $authUser->id,
                'category' => 'order',
                'title' => 'Konfirmasi Pesanan Berhasil',
                'description' => 'Pesanan no. ' . $orderCode . ' dengan total Rp' . number_format($amount, 0, ',', '.') . ' berhasil dikonfirmasi. Saldo Anda dipotong Rp' . number_format($amount, 0, ',', '.') . '.',
            ]);

            Notification::create([
                'for_user_id' => $authUser->id,
                'category' => 'payment',
                'title' => 'Keuntungan Akan Masuk ke Saldo',
                'description' => 'Keuntungan sebesar Rp' . number_format($totalPlusProfit, 0, ',', '.') . ' (Pesanan: Rp' . number_format($amount, 0, ',', '.') . ' + Profit ' . $marginPercent . '%: Rp' . number_format($profit, 0, ',', '.') . ') akan segera masuk ke Saldo Anda.',
            ]);
        } catch (\Throwable $e) {
            // fail-safe: ignore notification failure
        }

        if (view()->exists('user.orders-by-admin.show')) {
            return redirect()
                ->route('user.orders-by-admin.show', $orders_by_admin)
                ->with('success', 'Berhasil konfirmasi order.')
                ->with('info', 'Total Keuntungan Rp ' . number_format($totalPlusProfit, 0, ',', '.') . ' (Total: Rp' . number_format($amount, 0, ',', '.') . ' + Profit ' . $marginPercent . '%: Rp' . number_format($profit, 0, ',', '.') . ') akan segera masuk ke Saldo anda.');
        }

        return response()->json($finalOrder);
    }

    protected function authorizeAccess(OrderByAdmin $order): void
    {
        $userId = Auth::id();
        if (!$userId || $order->user_id !== $userId) {
            abort(403, 'Forbidden');
        }
    }
}
