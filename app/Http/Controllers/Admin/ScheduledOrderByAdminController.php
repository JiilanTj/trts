<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\ExecuteScheduledOrderByAdmin;
use App\Models\ScheduledOrderByAdmin;
use App\Models\StoreShowcase;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ScheduledOrderByAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','role:admin']);
    }

    public function index(Request $request)
    {
        if (!$request->wantsJson()) {
            return view('admin.orders-by-admin.scheduled.index');
        }
        $rows = ScheduledOrderByAdmin::with(['seller:id,full_name,username','product:id,name'])
            ->latest()->paginate(30);
        return response()->json($rows);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'store_showcase_id' => 'required|exists:store_showcases,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'timezone' => 'nullable|string|max:64',
            'schedule_at' => 'required|string',
        ]);

        $tz = $data['timezone'] ?? 'Asia/Jakarta';
        try { $local = Carbon::parse($data['schedule_at'], $tz); }
        catch (\Throwable $e) { return response()->json(['message' => 'Format jadwal tidak valid'], 422); }
        $utc = $local->clone()->setTimezone('UTC');

        // Validate ownership
        $showcase = StoreShowcase::findOrFail($data['store_showcase_id']);
        if ((int)$showcase->user_id !== (int)$data['user_id']) {
            return response()->json(['message' => 'Etalase bukan milik seller tersebut'], 422);
        }

        $row = ScheduledOrderByAdmin::create([
            'created_by' => $request->user()->id,
            'user_id' => $data['user_id'],
            'store_showcase_id' => $data['store_showcase_id'],
            'product_id' => $data['product_id'],
            'quantity' => $data['quantity'],
            'schedule_at' => $utc,
            'timezone' => $tz,
            'status' => 'scheduled',
        ]);

        // dispatch delayed
        $job = (new ExecuteScheduledOrderByAdmin($row->id))->onQueue('scheduled');
        dispatch($job)->delay($utc);

        return response()->json(['message' => 'Order admin terjadwal', 'id' => $row->id], 201);
    }

    public function show(ScheduledOrderByAdmin $scheduled)
    {
        $scheduled->load(['seller:id,full_name', 'product:id,name']);
        return response()->json($scheduled);
    }

    public function cancel(ScheduledOrderByAdmin $scheduled)
    {
        if ($scheduled->status !== 'scheduled') {
            return response()->json(['message' => 'Tidak bisa dibatalkan'], 422);
        }
        $scheduled->update(['status' => 'canceled']);
        return response()->json(['message' => 'Dibatalkan']);
    }

    public function runNow(ScheduledOrderByAdmin $scheduled)
    {
        if (!in_array($scheduled->status, ['scheduled','failed'])) {
            return response()->json(['message' => 'Tidak bisa dijalankan sekarang'], 422);
        }
        dispatch(new ExecuteScheduledOrderByAdmin($scheduled->id));
        return response()->json(['message' => 'Dikirim ke antrian']);
    }
}
