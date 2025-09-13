<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\Category;
use App\Models\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SellerMarginIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_level_1_seller_margin_calculation()
    {
        // Create a Level 1 seller (margin_percent = null)
        $seller = User::factory()->create([
            'is_seller' => true,
            'level' => 1,
            'balance' => 1000000, // 1M balance for purchase
            'total_transaction_amount' => 0,
        ]);

        // Create a category and product
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'category_id' => $category->id,
            'sell_price' => 10000,  // harga_jual
            'promo_price' => 8000,  // harga_biasa (promo active)
            'stock' => 100,
            'status' => 'active',
        ]);

        // Expected margin for Level 1: harga_jual - harga_biasa = 10000 - 8000 = 2000
        $expectedMargin = $product->getSellerMargin($seller);
        $this->assertEquals(2000, $expectedMargin);

        // Test applicable price for external purchase
        $externalPrice = $product->getApplicablePrice($seller, 'external');
        $this->assertEquals(10000, $externalPrice); // Should be harga_jual

        // Test applicable price for self purchase
        $selfPrice = $product->getApplicablePrice($seller, 'self');
        $this->assertEquals(8000, $selfPrice); // Should be harga_biasa
    }

    public function test_level_2_seller_margin_calculation()
    {
        // Create a Level 2 seller (margin_percent = 13%)
        $seller = User::factory()->create([
            'is_seller' => true,
            'level' => 2,
            'balance' => 1000000,
            'total_transaction_amount' => 150_000_000,
        ]);

        // Create a category and product
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'category_id' => $category->id,
            'sell_price' => 10000,  // harga_jual
            'promo_price' => 8000,  // harga_biasa
            'stock' => 100,
            'status' => 'active',
        ]);

        // Expected margin for Level 2: 13% of harga_jual = 13% of 10000 = 1300
        $expectedMargin = $product->getSellerMargin($seller);
        $this->assertEquals(1300, $expectedMargin);

        // Verify level methods
        $this->assertEquals(13, $seller->getLevelMarginPercent());
        $this->assertEquals('Bintang 2', $seller->getLevelBadge());
    }

    public function test_level_5_seller_margin_calculation()
    {
        // Create a Level 5 seller (margin_percent = 22%)
        $seller = User::factory()->create([
            'is_seller' => true,
            'level' => 5,
            'balance' => 1000000,
            'total_transaction_amount' => 1_500_000_000,
        ]);

        // Create a category and product
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'category_id' => $category->id,
            'sell_price' => 10000,  // harga_jual
            'promo_price' => 8000,  // harga_biasa
            'stock' => 100,
            'status' => 'active',
        ]);

        // Expected margin for Level 5: 22% of harga_jual = 22% of 10000 = 2200
        $expectedMargin = $product->getSellerMargin($seller);
        $this->assertEquals(2200, $expectedMargin);

        // Verify level methods
        $this->assertEquals(22, $seller->getLevelMarginPercent());
        $this->assertEquals('Bintang 5', $seller->getLevelBadge());
    }

    public function test_external_order_with_margin_payout()
    {
        // Create a Level 3 seller (margin_percent = 15%)
        $seller = User::factory()->create([
            'is_seller' => true,
            'level' => 3,
            'balance' => 1000000,
            'total_transaction_amount' => 300_000_000,
            'credit_score' => 50,
        ]);

        // Create a category and product
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'category_id' => $category->id,
            'sell_price' => 10000,  // harga_jual
            'promo_price' => 8000,  // harga_biasa
            'stock' => 100,
            'status' => 'active',
        ]);

        // Create an external order with balance payment
        $order = Order::create([
            'user_id' => $seller->id,
            'purchase_type' => 'external',
            'external_customer_name' => 'John Doe',
            'external_customer_phone' => '081234567890',
            'address' => 'Test Address',
            'subtotal' => 10000,
            'discount_total' => 0,
            'grand_total' => 10000,
            'seller_margin_total' => 1500, // 15% of 10000
            'payment_method' => 'balance',
            'payment_status' => 'paid',
            'status' => 'packaging',
            'payment_confirmed_at' => now(),
        ]);

        $initialBalance = $seller->balance;
        $initialCreditScore = $seller->credit_score;

        // Process margin payout
        $order->processSellerMarginPayout();

        // Refresh seller data
        $seller->refresh();

        // Verify balance increase
        $this->assertEquals($initialBalance + 1500, $seller->balance);

        // Verify credit score increase
        $this->assertEquals($initialCreditScore + 5, $seller->credit_score);

        // Verify notification was created
        $notification = Notification::where('for_user_id', $seller->id)
            ->where('category', 'payment')
            ->where('title', 'Margin Seller Diterima')
            ->first();

        $this->assertNotNull($notification);
        $this->assertStringContainsString('Rp1.500', $notification->description);
        $this->assertStringContainsString('Margin 15%', $notification->description);
        $this->assertStringContainsString('Bintang 3', $notification->description);
        $this->assertStringContainsString('Credit score +5 poin!', $notification->description);
    }

    public function test_level_1_external_order_notification()
    {
        // Create a Level 1 seller (margin_percent = null)
        $seller = User::factory()->create([
            'is_seller' => true,
            'level' => 1,
            'balance' => 1000000,
            'total_transaction_amount' => 0,
            'credit_score' => 10,
        ]);

        // Create a category and product
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'category_id' => $category->id,
            'sell_price' => 12000,  // harga_jual
            'promo_price' => 10000,  // harga_biasa
            'stock' => 100,
            'status' => 'active',
        ]);

        // Create order with Level 1 margin (2000)
        $order = Order::create([
            'user_id' => $seller->id,
            'purchase_type' => 'external',
            'external_customer_name' => 'Jane Doe',
            'external_customer_phone' => '081234567891',
            'address' => 'Test Address 2',
            'subtotal' => 12000,
            'discount_total' => 0,
            'grand_total' => 12000,
            'seller_margin_total' => 2000, // harga_jual - harga_biasa
            'payment_method' => 'balance',
            'payment_status' => 'paid',
            'status' => 'packaging',
            'payment_confirmed_at' => now(),
        ]);

        // Process margin payout
        $order->processSellerMarginPayout();

        // Verify notification for Level 1 (no percentage, uses admin-set margin)
        $notification = Notification::where('for_user_id', $seller->id)
            ->where('category', 'payment')
            ->where('title', 'Margin Seller Diterima')
            ->first();

        $this->assertNotNull($notification);
        $this->assertStringContainsString('Rp2.000', $notification->description);
        $this->assertStringContainsString('Margin sesuai harga jual', $notification->description);
        $this->assertStringContainsString('Bintang 1', $notification->description);
        $this->assertStringNotContainsString('Margin %', $notification->description);
    }

    public function test_non_seller_gets_no_margin()
    {
        // Create a regular user (not seller)
        $user = User::factory()->create([
            'is_seller' => false,
            'level' => 1,
        ]);

        // Create a category and product
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'category_id' => $category->id,
            'sell_price' => 10000,
            'promo_price' => 8000,
            'stock' => 100,
            'status' => 'active',
        ]);

        // Non-seller should get 0 margin
        $margin = $product->getSellerMargin($user);
        $this->assertEquals(0, $margin);

        // Non-seller should always pay harga_biasa regardless of purchase type
        $externalPrice = $product->getApplicablePrice($user, 'external');
        $selfPrice = $product->getApplicablePrice($user, 'self');
        $this->assertEquals(8000, $externalPrice);
        $this->assertEquals(8000, $selfPrice);
    }
}
