<?php

namespace Tests\Feature;

use App\Jobs\ExecuteScheduledOrderByAdmin;
use App\Models\OrderByAdmin;
use App\Models\Product;
use App\Models\ScheduledOrderByAdmin;
use App\Models\ScheduledOrderByAdminItem;
use App\Models\StoreShowcase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Notification as NotificationFacade;
use Tests\TestCase;

class ScheduledOrderByAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_multi_item_schedule_and_job_is_dispatched(): void
    {
        Bus::fake();

        $admin = User::factory()->create(['role' => 'admin']);
        $seller = User::factory()->create(['role' => 'user', 'is_seller' => true]);

        $product1 = Product::factory()->create(['status' => 'active', 'sell_price' => 15000]);
        $product2 = Product::factory()->create(['status' => 'active', 'sell_price' => 25000]);

        $show1 = StoreShowcase::factory()->create(['user_id' => $seller->id, 'product_id' => $product1->id]);
        $show2 = StoreShowcase::factory()->create(['user_id' => $seller->id, 'product_id' => $product2->id]);

        $whenLocal = Carbon::now('Asia/Jakarta')->addHour();

        $resp = $this->actingAs($admin)
            ->postJson(route('admin.orders-by-admin.scheduled.store'), [
                'user_id' => $seller->id,
                'timezone' => 'Asia/Jakarta',
                'schedule_at' => $whenLocal->format('Y-m-d\TH:i'), // matches datetime-local input
                'adress' => 'Gudang Seller 12',
                'items' => [
                    [ 'store_showcase_id' => $show1->id, 'product_id' => $product1->id, 'quantity' => 2 ],
                    [ 'store_showcase_id' => $show2->id, 'product_id' => $product2->id, 'quantity' => 3 ],
                ],
            ]);

        $resp->assertCreated();
        $id = $resp->json('id');
        $this->assertNotNull($id);

        Bus::assertDispatched(ExecuteScheduledOrderByAdmin::class, function($job) use ($id){
            return $job->scheduleId === $id;
        });

        $row = ScheduledOrderByAdmin::with('items')->find($id);
        $this->assertNotNull($row);
        $this->assertEquals('scheduled', $row->status);
        $this->assertEquals('Asia/Jakarta', $row->timezone);
    $this->assertEquals('Gudang Seller 12', $row->adress);
        $this->assertCount(2, $row->items);
    }

    public function test_job_creates_orders_for_all_items_and_marks_completed(): void
    {
        Queue::fake();

        $admin = User::factory()->create(['role' => 'admin']);
        $seller = User::factory()->create(['role' => 'user', 'is_seller' => true]);

        $product1 = Product::factory()->create(['status' => 'active', 'sell_price' => 10000]);
        $product2 = Product::factory()->create(['status' => 'active', 'sell_price' => 20000]);

        $show1 = StoreShowcase::factory()->create(['user_id' => $seller->id, 'product_id' => $product1->id]);
        $show2 = StoreShowcase::factory()->create(['user_id' => $seller->id, 'product_id' => $product2->id]);

        $row = ScheduledOrderByAdmin::create([
            'created_by' => $admin->id,
            'user_id' => $seller->id,
            'schedule_at' => now()->addMinutes(5),
            'timezone' => 'Asia/Jakarta',
            'status' => 'scheduled',
            'adress' => 'Alamat Gudang Pusat',
        ]);

        $it1 = ScheduledOrderByAdminItem::create([
            'scheduled_id' => $row->id,
            'store_showcase_id' => $show1->id,
            'product_id' => $product1->id,
            'quantity' => 2,
        ]);
        $it2 = ScheduledOrderByAdminItem::create([
            'scheduled_id' => $row->id,
            'store_showcase_id' => $show2->id,
            'product_id' => $product2->id,
            'quantity' => 3,
        ]);

        // Run job immediately
        (new ExecuteScheduledOrderByAdmin($row->id))->handle();

        $row->refresh();
        $this->assertEquals('completed', $row->status);
        $this->assertNotNull($row->finished_at);

        $it1->refresh();
        $it2->refresh();

        $this->assertNotNull($it1->created_order_id);
        $this->assertNotNull($it2->created_order_id);

        $o1 = OrderByAdmin::find($it1->created_order_id);
        $o2 = OrderByAdmin::find($it2->created_order_id);

        $this->assertEquals($admin->id, $o1->admin_id);
        $this->assertEquals($seller->id, $o1->user_id);
        $this->assertEquals($show1->id, $o1->store_showcase_id);
        $this->assertEquals($product1->id, $o1->product_id);
    $this->assertEquals('Alamat Gudang Pusat', $o1->adress);
        $this->assertEquals(2, $o1->quantity);
        $this->assertEquals(10000, $o1->unit_price);
        $this->assertEquals(20000, $o1->total_price);

    $this->assertEquals('Alamat Gudang Pusat', $o2->adress);
        $this->assertEquals(3, $o2->quantity);
        $this->assertEquals(20000, $o2->unit_price);
        $this->assertEquals(60000, $o2->total_price);
    }

    public function test_store_validation_rejects_cross_seller_showcase(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $sellerA = User::factory()->create(['role' => 'user', 'is_seller' => true]);
        $sellerB = User::factory()->create(['role' => 'user', 'is_seller' => true]);

        $product = Product::factory()->create(['status' => 'active']);
        $showA = StoreShowcase::factory()->create(['user_id' => $sellerA->id, 'product_id' => $product->id]);

        $whenLocal = Carbon::now('Asia/Jakarta')->addHour();

        $resp = $this->actingAs($admin)
            ->postJson(route('admin.orders-by-admin.scheduled.store'), [
                'user_id' => $sellerB->id,
                'timezone' => 'Asia/Jakarta',
                'schedule_at' => $whenLocal->format('Y-m-d\TH:i'),
                'adress' => 'Gudang Salah Seller',
                'items' => [
                    [ 'store_showcase_id' => $showA->id, 'product_id' => $product->id, 'quantity' => 1 ],
                ],
            ]);

        $resp->assertStatus(422);
        $resp->assertJson(['message' => "Etalase #{$showA->id} bukan milik seller tersebut"]);
    }
}
