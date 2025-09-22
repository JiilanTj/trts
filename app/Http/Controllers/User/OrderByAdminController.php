<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\OrderByAdmin;
use App\Models\User;
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

        $orders = OrderByAdmin::query()
            ->with(['admin:id,username,full_name', 'storeShowcase', 'product'])
            ->where('user_id', $userId)
            ->when($request->query('status'), function ($q, $status) {
                $q->where('status', $status);
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return response()->json($orders);
    }

    // GET /orders-by-admin/{orders_by_admin}
    public function show(OrderByAdmin $orders_by_admin)
    {
        $this->authorizeAccess($orders_by_admin);
        $orders_by_admin->load(['admin:id,username,full_name', 'storeShowcase', 'product']);
        return response()->json($orders_by_admin);
    }

    // PATCH /orders-by-admin/{orders_by_admin}/confirm
    public function confirm(OrderByAdmin $orders_by_admin)
    {
        $this->authorizeAccess($orders_by_admin);

        if ($orders_by_admin->status !== OrderByAdmin::STATUS_PENDING) {
            return response()->json([
                'message' => 'Order tidak dalam status PENDING.'
            ], 422);
        }

        $authUser = Auth::user();
        $finalOrder = null;

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
