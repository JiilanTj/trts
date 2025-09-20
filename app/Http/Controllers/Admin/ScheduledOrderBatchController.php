<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\ExecuteScheduledOrderBatch;
use App\Models\ScheduledOrderBatch;
use App\Models\ScheduledOrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ScheduledOrderBatchController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','role:admin']);
    }

    /** List batches (JSON) */
    public function index(Request $request)
    {
        $this->authorize('viewAny', ScheduledOrderBatch::class);
        $batches = ScheduledOrderBatch::withCount('items')
            ->latest()
            ->paginate(30);
        return response()->json($batches);
    }

    /** Create a new scheduled batch and dispatch job */
    public function store(Request $request)
    {
        $this->authorize('create', ScheduledOrderBatch::class);
        $data = $request->validate([
            'buyer_id' => 'required|exists:users,id',
            'purchase_type' => 'required|in:self,external',
            'external_customer_name' => 'nullable|string|max:120',
            'external_customer_phone' => 'nullable|string|max:40',
            'address' => 'required|string|max:255',
            'user_notes' => 'nullable|string',
            'auto_paid' => 'nullable|boolean',
            'timezone' => 'nullable|string|max:64', // default Asia/Jakarta
            'schedule_at' => 'required|string', // e.g. 2025-09-20 20:00
            'items' => 'required|array|min:1',
            'items.*.seller_id' => 'required|exists:users,id',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price_cap' => 'nullable|integer|min:1',
            'items.*.tolerance_percent' => 'nullable|integer|min:0|max:100',
        ]);

        $tz = $data['timezone'] ?? 'Asia/Jakarta';
        // Parse schedule_at in provided TZ, store as UTC
        try {
            $scheduleLocal = Carbon::parse($data['schedule_at'], $tz);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Format schedule_at tidak valid.'], 422);
        }
        $scheduleUtc = $scheduleLocal->clone()->setTimezone('UTC');

        $batch = DB::transaction(function () use ($request, $data, $scheduleUtc, $tz) {
            $batch = ScheduledOrderBatch::create([
                'buyer_id' => $data['buyer_id'],
                'created_by' => $request->user()->id,
                'purchase_type' => $data['purchase_type'],
                'from_etalase' => true, // forced true for this feature
                'auto_paid' => (bool)($data['auto_paid'] ?? false),
                'external_customer_name' => $data['external_customer_name'] ?? null,
                'external_customer_phone' => $data['external_customer_phone'] ?? null,
                'address' => $data['address'],
                'user_notes' => $data['user_notes'] ?? null,
                'schedule_at' => $scheduleUtc,
                'timezone' => $tz,
                'status' => 'scheduled',
            ]);

            foreach ($data['items'] as $it) {
                ScheduledOrderItem::create([
                    'batch_id' => $batch->id,
                    'seller_id' => $it['seller_id'],
                    'product_id' => $it['product_id'],
                    'quantity' => $it['quantity'],
                    'price_cap' => $it['price_cap'] ?? null,
                    'tolerance_percent' => $it['tolerance_percent'] ?? null,
                ]);
            }

            return $batch;
        });

        // Dispatch job (delayed to UTC time)
        $delaySeconds = now('UTC')->diffInSeconds($scheduleUtc, false);
        $job = new ExecuteScheduledOrderBatch($batch->id);
        $delaySeconds > 0
            ? dispatch($job)->delay($scheduleUtc)
            : dispatch($job);

        return response()->json([
            'message' => 'Batch dijadwalkan.',
            'batch_id' => $batch->id,
            'schedule_at_utc' => $scheduleUtc->toDateTimeString(),
        ], 201);
    }

    /** Show batch detail (JSON) */
    public function show(ScheduledOrderBatch $batch)
    {
        $this->authorize('view', $batch);
        $batch->load(['buyer:id,full_name', 'creator:id,full_name', 'items.product:id,name', 'items.seller:id,full_name']);
        return response()->json($batch);
    }

    /** Cancel batch if still scheduled */
    public function cancel(Request $request, ScheduledOrderBatch $batch)
    {
        $this->authorize('update', $batch);
        if ($batch->status !== 'scheduled') {
            return response()->json(['message' => 'Tidak dapat dibatalkan. Status: ' . $batch->status], 422);
        }
        $batch->update(['status' => 'canceled']);
        return response()->json(['message' => 'Batch dibatalkan.']);
    }

    /** Run now regardless of schedule (if not processed yet) */
    public function runNow(Request $request, ScheduledOrderBatch $batch)
    {
        $this->authorize('update', $batch);
        if (!in_array($batch->status, ['scheduled', 'failed', 'partial'])) {
            return response()->json(['message' => 'Batch tidak dapat dijalankan sekarang (status: ' . $batch->status . ').'], 422);
        }
        dispatch(new ExecuteScheduledOrderBatch($batch->id));
        return response()->json(['message' => 'Batch dikirim ke antrian untuk dieksekusi.']);
    }
}
