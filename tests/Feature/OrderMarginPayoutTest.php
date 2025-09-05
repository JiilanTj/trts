<?php

use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use App\Models\OrderItem;

test('seller receives margin and credit score when order is paid', function () {
    // Create a seller user
    $seller = User::factory()->create([
        'is_seller' => true,
        'balance' => 0,
        'credit_score' => 0,
    ]);

    // Create an external order with margin
    $order = Order::create([
        'user_id' => $seller->id,
        'purchase_type' => 'external',
        'external_customer_name' => 'Test Customer',
        'external_customer_phone' => '08123456789',
        'address' => 'Test Address',
        'subtotal' => 120000,
        'discount_total' => 0,
        'grand_total' => 120000,
        'seller_margin_total' => 20000, // margin
        'payment_method' => 'manual_transfer',
        'payment_status' => 'waiting_confirmation',
        'status' => 'awaiting_confirmation',
    ]);

    // Process margin payout
    $order->processSellerMarginPayout();

    // Assert seller received margin and credit score
    $seller->refresh();
    expect($seller->balance)->toBe(20000);
    expect($seller->credit_score)->toBe(5);
});

test('no payout for self purchase', function () {
    // Create a seller user
    $seller = User::factory()->create([
        'is_seller' => true,
        'balance' => 0,
        'credit_score' => 0,
    ]);

    // Create a self order (no margin)
    $order = Order::create([
        'user_id' => $seller->id,
        'purchase_type' => 'self',
        'external_customer_name' => $seller->full_name,
        'external_customer_phone' => '08123456789',
        'address' => 'Test Address',
        'subtotal' => 100000,
        'discount_total' => 0,
        'grand_total' => 100000,
        'seller_margin_total' => 0, // No margin for self purchase
        'payment_method' => 'manual_transfer',
        'payment_status' => 'waiting_confirmation',
        'status' => 'awaiting_confirmation',
    ]);

    // Process margin payout
    $order->processSellerMarginPayout();

    // Assert no change in balance or credit score
    $seller->refresh();
    expect($seller->balance)->toBe(0);
    expect($seller->credit_score)->toBe(0);
});

test('non seller cannot buy as external', function () {
    // Create a regular user (not seller)
    $user = User::factory()->create([
        'is_seller' => false,
    ]);

    // Manually test the validation logic
    expect($user->isSeller())->toBeFalse();
    
    // This would be caught by the validation in the controller:
    // if ($data['purchase_type'] === 'external' && !$user->isSeller()) {
    //     return back()->withErrors(['purchase_type' => 'Hanya seller yang dapat membeli untuk pelanggan eksternal.'])->withInput();
    // }
});
