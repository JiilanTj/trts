<?php

require_once 'vendor/autoload.php';

use App\Models\ScheduledOrderByAdmin;
use App\Models\ScheduledOrderByAdminItem;
use App\Models\User;
use App\Models\StoreShowcase;
use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Testing New Adress Implementation ===\n";

// Test creating scheduled order with adress in items
try {
    $admin = User::where('role', 'admin')->first();
    $seller = User::where('is_seller', true)->first(); 
    $showcase = StoreShowcase::first();
    
    if (!$admin || !$seller || !$showcase) {
        echo "Missing required data\n";
        exit(1);
    }
    
    $testAdress = 'Test Address Jalan Mangga No. 123, Jakarta Selatan';
    
    // Create scheduled order
    $row = ScheduledOrderByAdmin::create([
        'created_by' => $admin->id,
        'user_id' => $seller->id,
        'store_showcase_id' => null, // multi-item mode
        'product_id' => null,
        'adress' => $testAdress,
        'quantity' => 0,
        'schedule_at' => now()->addMinute(),
        'timezone' => 'Asia/Jakarta',
        'status' => 'scheduled',
    ]);
    
    echo "Created ScheduledOrderByAdmin ID: {$row->id}\n";
    echo "Parent adress: '{$row->adress}'\n";
    
    // Create item with adress
    $item = ScheduledOrderByAdminItem::create([
        'scheduled_id' => $row->id,
        'store_showcase_id' => $showcase->id,
        'product_id' => $showcase->product_id,
        'quantity' => 2,
        'adress' => $testAdress, // NEW: item now has adress
    ]);
    
    echo "Created item ID: {$item->id}\n";
    echo "Item adress: '{$item->adress}'\n";
    
    // Now test job execution manually
    $job = new \App\Jobs\ExecuteScheduledOrderByAdmin($row->id);
    echo "\nExecuting job...\n";
    $job->handle();
    
    // Check results
    $row->refresh();
    echo "Job status: {$row->status}\n";
    
    if ($row->status === 'completed') {
        $item->refresh();
        if ($item->created_order_id) {
            $order = \App\Models\OrderByAdmin::find($item->created_order_id);
            if ($order) {
                echo "Created OrderByAdmin ID: {$order->id}\n";
                echo "Order adress: '{$order->adress}'\n";
                echo "SUCCESS: Adress copied from item to order!\n";
            }
        }
    }
    
    // Cleanup
    if ($row->status === 'completed' && $item->created_order_id) {
        \App\Models\OrderByAdmin::find($item->created_order_id)?->delete();
    }
    $item->delete();
    $row->delete();
    
    echo "\nTest completed successfully!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}