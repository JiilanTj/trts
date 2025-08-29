<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductsTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->admin = User::create([
            'full_name' => 'Admin Test',
            'username' => 'admin_test',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'balance' => 0,
            'level' => 1,
        ]);

        $this->category = Category::create([
            'name' => 'Test Category',
            'description' => 'Test category description',
            'status' => 'active',
        ]);
    }

    public function test_admin_can_view_products_index()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.products.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.products.index');
    }

    public function test_sku_must_be_unique()
    {
        Product::create([
            'name' => 'Existing Product',
            'sku' => 'UNIQUE-001',
            'category_id' => $this->category->id,
            'stock' => 1,
            'purchase_price' => 10.00,
            'sell_price' => 20.00,
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.products.store'), [
                'name' => 'New Product',
                'sku' => 'UNIQUE-001', // Same SKU
                'category_id' => $this->category->id,
                'stock' => 1,
                'purchase_price' => 10.00,
                'sell_price' => 20.00,
                'status' => 'active',
            ]);

        $response->assertSessionHasErrors(['sku']);
    }
}
