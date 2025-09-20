<?php

namespace Tests\Feature;

use App\Jobs\ExecuteScheduledOrderBatch;
use App\Models\Product;
use App\Models\ScheduledOrderBatch;
use App\Models\ScheduledOrderItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class ScheduledOrderBatchTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_schedule_batch_and_job_is_dispatched_at_future_time(): void
    {
        Bus::fake();

        $admin = User::factory()->create(['role' => 'admin']);
        $buyer = User::factory()->create(['role' => 'user']);
        $seller = User::factory()->create(['role' => 'user', 'is_seller' => true]);
        $product = Product::factory()->create(['status' => 'active']);

        $whenLocal = Carbon::now('Asia/Jakarta')->addHour();

        $resp = $this->actingAs($admin)
            ->post(route('admin.scheduled-orders.store'), [
                'buyer_id' => $buyer->id,
                'purchase_type' => 'self',
                'address' => 'Alamat test',
                'timezone' => 'Asia/Jakarta',
                'schedule_at' => $whenLocal->format('Y-m-d H:i'),
                'items' => [
                    ['seller_id' => $seller->id, 'product_id' => $product->id, 'quantity' => 2],
                ],
            ]);

        $resp->assertCreated();
        $batchId = $resp->json('batch_id');
        $this->assertNotNull($batchId);

        Bus::assertDispatched(ExecuteScheduledOrderBatch::class);

        $batch = ScheduledOrderBatch::find($batchId);
        $this->assertEquals('scheduled', $batch->status);
        $this->assertEquals('Asia/Jakarta', $batch->timezone);
    }
}
