<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\ExecuteScheduledOrderByAdmin;
use App\Models\ScheduledOrderByAdmin;
use App\Models\ScheduledOrderByAdminItem;
use App\Models\StoreShowcase;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\User; // added
use Illuminate\Support\Facades\DB;

class ScheduledOrderByAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','role:admin']);
    }

    public function index(Request $request)
    {
        if (!$request->wantsJson()) {
            // Provide sellers list for the create form (limit to recent sellers)
            $users = User::query()->users()->sellers()->orderByDesc('id')->limit(50)->get(['id','full_name','username']);
            return view('admin.orders-by-admin.scheduled.index', compact('users'));
        }
        $rows = ScheduledOrderByAdmin::with([
                'seller:id,full_name,username',
                'items.product:id,name',
                'items.storeShowcase:id,user_id,product_id'
            ])
            ->latest()->paginate(30);
        return response()->json($rows);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'timezone' => 'nullable|string|max:64',
            'schedule_at' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.store_showcase_id' => 'required|exists:store_showcases,id',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $tz = $data['timezone'] ?? 'Asia/Jakarta';
        try { $local = Carbon::parse($data['schedule_at'], $tz); }
        catch (\Throwable $e) { return response()->json(['message' => 'Format jadwal tidak valid'], 422); }
        $utc = $local->clone()->setTimezone('UTC');

        // Validate ownership for each item and same seller ownership
        foreach ($data['items'] as $idx => $item) {
            $showcase = StoreShowcase::findOrFail($item['store_showcase_id']);
            if ((int)$showcase->user_id !== (int)$data['user_id']) {
                return response()->json(['message' => "Etalase #{$showcase->id} bukan milik seller tersebut"], 422);
            }
            if ((int)$showcase->product_id !== (int)$item['product_id']) {
                return response()->json(['message' => "Produk tidak sesuai dengan etalase #{$showcase->id}"], 422);
            }
        }

        $row = ScheduledOrderByAdmin::create([
            'created_by' => $request->user()->id,
            'user_id' => $data['user_id'],
            // keep single-item columns blank in multi-item mode
            'store_showcase_id' => null,
            'product_id' => null,
            'quantity' => 0,
            'schedule_at' => $utc,
            'timezone' => $tz,
            'status' => 'scheduled',
        ]);

        // Create item rows
        foreach ($data['items'] as $item) {
            ScheduledOrderByAdminItem::create([
                'scheduled_id' => $row->id,
                'store_showcase_id' => $item['store_showcase_id'],
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
            ]);
        }

        // dispatch delayed per schedule id (job will fan-out)
        $job = (new ExecuteScheduledOrderByAdmin($row->id))->onQueue('scheduled');
        dispatch($job)->delay($utc);

        return response()->json(['message' => 'Order admin terjadwal', 'id' => $row->id], 201);
    }

    public function show(ScheduledOrderByAdmin $scheduled)
    {
        $scheduled->load(['seller:id,full_name', 'items.product:id,name']);
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

        // Atomically claim the row so UI updates immediately and avoid double-execution
        $claimed = DB::transaction(function() use ($scheduled) {
            $row = ScheduledOrderByAdmin::where('id', $scheduled->id)->lockForUpdate()->first();
            if (!$row) { return false; }
            if (!in_array($row->status, ['scheduled','failed'])) { return false; }
            $row->update(['status' => 'processing', 'started_at' => now()]);
            return true;
        });

        if (!$claimed) {
            return response()->json(['message' => 'Sudah diproses oleh worker lain'], 409);
        }

        dispatch(new ExecuteScheduledOrderByAdmin($scheduled->id));
        return response()->json(['message' => 'Dikirim ke antrian']);
    }
}
