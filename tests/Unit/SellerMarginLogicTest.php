<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\Category;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SellerMarginLogicTest extends TestCase
{
    /**
     * Test Level 1 seller margin calculation (uses admin-set margin)
     */
    public function test_level_1_seller_margin_calculation()
    {
        // Create a Level 1 seller
        $seller = new User([
            'is_seller' => true,
            'level' => 1,
            'total_transaction_amount' => 0,
        ]);

        // Create a product
        $product = new Product([
            'sell_price' => 10000,  // harga_jual
            'promo_price' => 8000,  // harga_biasa (promo active)
        ]);

        // Level 1 now percentage based (10% of harga_jual)
        $expectedMargin = round(10000 * 0.10); // 1000
        $actualMargin = $product->getSellerMargin($seller);
        
        $this->assertEquals($expectedMargin, $actualMargin);
    }

    /**
     * Test Level 2 seller margin calculation (13% of harga_jual)
     */
    public function test_level_2_seller_margin_calculation()
    {
        // Create a Level 2 seller
        $seller = new User([
            'is_seller' => true,
            'level' => 2,
            'total_transaction_amount' => 150_000_000,
        ]);

        // Create a product
        $product = new Product([
            'sell_price' => 10000,  // harga_jual
            'promo_price' => 8000,  // harga_biasa
        ]);

        // Level 2 should use 13% of harga_jual
        $expectedMargin = round(10000 * (13 / 100)); // 1300
        $actualMargin = $product->getSellerMargin($seller);
        
        $this->assertEquals($expectedMargin, $actualMargin);
    }

    /**
     * Test Level 5 seller margin calculation (22% of harga_jual)
     */
    public function test_level_5_seller_margin_calculation()
    {
        // Create a Level 5 seller
        $seller = new User([
            'is_seller' => true,
            'level' => 5,
            'total_transaction_amount' => 1_500_000_000,
        ]);

        // Create a product
        $product = new Product([
            'sell_price' => 15000,  // harga_jual
            'promo_price' => 12000, // harga_biasa
        ]);

        // Level 5 should use 22% of harga_jual
        $expectedMargin = round(15000 * (22 / 100)); // 3300
        $actualMargin = $product->getSellerMargin($seller);
        
        $this->assertEquals($expectedMargin, $actualMargin);
    }

    /**
     * Test non-seller gets no margin
     */
    public function test_non_seller_gets_no_margin()
    {
        // Create a regular user (not seller)
        $user = new User([
            'is_seller' => false,
            'level' => 1,
        ]);

        // Create a product
        $product = new Product([
            'sell_price' => 10000,
            'promo_price' => 8000,
        ]);

        // Non-seller should get 0 margin
        $margin = $product->getSellerMargin($user);
        $this->assertEquals(0, $margin);
    }

    /**
     * Test price calculation for different purchase types
     */
    public function test_applicable_price_calculation()
    {
        // Create a seller
        $seller = new User([
            'is_seller' => true,
            'level' => 2,
        ]);

        // Create a regular user
        $user = new User([
            'is_seller' => false,
            'level' => 1,
        ]);

        // Create a product
        $product = new Product([
            'sell_price' => 10000,  // harga_jual
            'promo_price' => 8000,  // harga_biasa (promo active)
        ]);

        // Test seller external purchase (should pay harga_jual)
        $externalPrice = $product->getApplicablePrice($seller, 'external');
        $this->assertEquals(10000, $externalPrice);

        // Test seller self purchase (should pay harga_biasa)
        $selfPrice = $product->getApplicablePrice($seller, 'self');
        $this->assertEquals(8000, $selfPrice);

        // Test regular user (should always pay harga_biasa)
        $userExternalPrice = $product->getApplicablePrice($user, 'external');
        $userSelfPrice = $product->getApplicablePrice($user, 'self');
        $this->assertEquals(8000, $userExternalPrice);
        $this->assertEquals(8000, $userSelfPrice);
    }

    /**
     * Test Level 1 fallback when promo_price is not available
     */
    public function test_level_1_margin_without_promo()
    {
        // Create a Level 1 seller
        $seller = new User([
            'is_seller' => true,
            'level' => 1,
        ]);

        // Create a product without promo (harga_biasa = sell_price)
        $product = new Product([
            'sell_price' => 10000,  // harga_jual
            'promo_price' => null,  // no promo, so harga_biasa = sell_price
        ]);

        // With percentage system promo absence irrelevant: margin = 10% of harga_jual
        $expectedMargin = round(10000 * 0.10); // 1000
        $actualMargin = $product->getSellerMargin($seller);
        
        $this->assertEquals($expectedMargin, $actualMargin);
    }

    /**
     * Test Level 1 margin when promo price is higher than sell price
     */
    public function test_level_1_margin_promo_higher_than_sell()
    {
        // Create a Level 1 seller
        $seller = new User([
            'is_seller' => true,
            'level' => 1,
        ]);

        // Create a product where promo_price > sell_price (invalid promo)
        $product = new Product([
            'sell_price' => 10000,   // harga_jual
            'promo_price' => 12000,  // invalid promo (higher than sell), so harga_biasa = sell_price
        ]);

        // Since promo is invalid, harga_biasa = sell_price, so margin should be 0
        $expectedMargin = round(10000 * 0.10); // 1000
        $actualMargin = $product->getSellerMargin($seller);
        
        $this->assertEquals($expectedMargin, $actualMargin);
    }

    /**
     * Test user level system methods
     */
    public function test_user_level_methods()
    {
        // Test Level 1
        $level1User = new User(['level' => 1]);
        $this->assertEquals(10, $level1User->getLevelMarginPercent());
        $this->assertEquals('Bintang 1', $level1User->getLevelBadge());

        // Test Level 2
        $level2User = new User(['level' => 2]);
        $this->assertEquals(13, $level2User->getLevelMarginPercent());
        $this->assertEquals('Bintang 2', $level2User->getLevelBadge());

        // Test Level 3
        $level3User = new User(['level' => 3]);
        $this->assertEquals(15, $level3User->getLevelMarginPercent());
        $this->assertEquals('Bintang 3', $level3User->getLevelBadge());

        // Test Level 5
        $level5User = new User(['level' => 5]);
        $this->assertEquals(22, $level5User->getLevelMarginPercent());
        $this->assertEquals('Bintang 5', $level5User->getLevelBadge());

        // Test Level 6
        $level6User = new User(['level' => 6]);
        $this->assertEquals(25, $level6User->getLevelMarginPercent());
        $this->assertEquals('Toko dari Mulut ke Mulut', $level6User->getLevelBadge());
    }

    /** @test */
    public function balance_payment_updates_transaction_amount_for_level_progression()
    {
        // Create user with initial level and transaction amount
        $user = User::factory()->create([
            'balance' => 1000000, // 1M balance
            'total_transaction_amount' => 90000000, // 90M, close to level 2 (100M)
        ]);
        
        // Create product
        $product = Product::factory()->create([
            'sell_price' => 50000,
            'harga_biasa' => 40000,
            'stock' => 100,
        ]);
        
        // Create order with balance payment
        $orderData = [
            'purchase_type' => 'external',
            'payment_method' => 'balance',
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 400, // 400 * 50000 = 20M total
                ]
            ],
            'external_customer_name' => 'Test Customer',
            'external_customer_phone' => '081234567890',
            'address' => 'Test Address',
        ];
        
        // Act as user and store order
        $response = $this->actingAs($user)->post(route('user.orders.store'), $orderData);
        
        $response->assertRedirect();
        
        // Assert transaction amount was updated
        $user->refresh();
        $expectedNewTotal = 90000000 + (400 * 50000); // 90M + 20M = 110M
        $this->assertEquals($expectedNewTotal, $user->total_transaction_amount);
        
        // Assert user leveled up to level 2
        $this->assertEquals(2, $user->level);
        $this->assertEquals('Bintang 2', $user->getLevelBadge());
        $this->assertEquals(13, $user->getLevelMarginPercent());
    }

    /** @test */
    public function both_payment_methods_update_transaction_amount_correctly()
    {
        // Test balance payment
        $balanceUser = User::factory()->create([
            'balance' => 500000,
            'total_transaction_amount' => 0,
        ]);
        
        $product = Product::factory()->create([
            'sell_price' => 100000,
            'harga_biasa' => 80000,
            'stock' => 100,
        ]);
        
        // Balance order
        $balanceOrderData = [
            'purchase_type' => 'external',
            'payment_method' => 'balance',
            'items' => [['product_id' => $product->id, 'quantity' => 3]],
            'external_customer_name' => 'Test Customer',
            'external_customer_phone' => '081234567890',
            'address' => 'Test Address',
        ];
        
        $this->actingAs($balanceUser)->post(route('user.orders.store'), $balanceOrderData);
        
        $balanceUser->refresh();
        $this->assertEquals(300000, $balanceUser->total_transaction_amount); // 3 * 100000
        
        // Test manual transfer payment (should be updated when admin approves)
        $manualUser = User::factory()->create([
            'balance' => 0,
            'total_transaction_amount' => 0,
        ]);
        
        $manualOrderData = [
            'purchase_type' => 'external',
            'payment_method' => 'manual_transfer',
            'items' => [['product_id' => $product->id, 'quantity' => 3]],
            'external_customer_name' => 'Test Customer',
            'external_customer_phone' => '081234567890',
            'address' => 'Test Address',
        ];
        
        $response = $this->actingAs($manualUser)->post(route('user.orders.store'), $manualOrderData);
        $order = Order::latest()->first();
        
        // Before admin approval, no transaction amount update
        $manualUser->refresh();
        $this->assertEquals(0, $manualUser->total_transaction_amount);
        
        // Simulate admin approving payment
        $admin = User::factory()->create(['is_admin' => true]);
        $order->update(['payment_status' => 'waiting_confirmation']);
        
        $this->actingAs($admin)->post(route('admin.orders.approve-payment', $order));
        
        $manualUser->refresh();
        $this->assertEquals(300000, $manualUser->total_transaction_amount); // 3 * 100000
        
        // Both users should have same transaction amount now
        $this->assertEquals($balanceUser->total_transaction_amount, $manualUser->total_transaction_amount);
    }
}
