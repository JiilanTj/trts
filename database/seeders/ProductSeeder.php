<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all categories
        $categories = Category::all();

        if ($categories->isEmpty()) {
            $this->command->error('No categories found. Please run CategorySeeder first.');
            return;
        }

        $products = [
            [
                'name' => 'MacBook Pro 16-inch',
                'sku' => 'MBP16-001',
                'description' => 'Apple MacBook Pro 16-inch with M3 Pro chip, 18GB memory, 512GB SSD.',
                'category_id' => $categories->where('name', 'Electronics')->first()?->id ?? $categories->first()->id,
                'purchase_price' => 2200.00,
                'sell_price' => 2499.00,
                'promo_price' => 2299.00,
                'stock' => 15,
                'weight' => 2100.00,
                'status' => 'active',
                'expiry_date' => null,
            ],
            [
                'name' => 'iPhone 15 Pro',
                'sku' => 'IPH15P-001',
                'description' => 'Latest iPhone 15 Pro with 128GB storage in Natural Titanium.',
                'category_id' => $categories->where('name', 'Electronics')->first()?->id ?? $categories->first()->id,
                'purchase_price' => 850.00,
                'sell_price' => 999.00,
                'promo_price' => null,
                'stock' => 8,
                'weight' => 187.00,
                'status' => 'active',
                'expiry_date' => null,
            ],
            [
                'name' => 'Wireless Bluetooth Headphones',
                'sku' => 'WBH-001',
                'description' => 'Premium noise-cancelling wireless headphones with 30-hour battery life.',
                'category_id' => $categories->where('name', 'Electronics')->first()?->id ?? $categories->first()->id,
                'purchase_price' => 120.00,
                'sell_price' => 179.99,
                'promo_price' => 149.99,
                'stock' => 25,
                'weight' => 280.00,
                'status' => 'active',
                'expiry_date' => null,
            ],
            [
                'name' => 'Organic Coffee Beans',
                'sku' => 'OCB-001',
                'description' => 'Premium organic coffee beans from Colombia. Medium roast.',
                'category_id' => $categories->where('name', 'Food & Beverages')->first()?->id ?? $categories->first()->id,
                'purchase_price' => 8.50,
                'sell_price' => 14.99,
                'promo_price' => null,
                'stock' => 50,
                'weight' => 450.00,
                'status' => 'active',
                'expiry_date' => now()->addMonths(12),
            ],
            [
                'name' => 'Green Tea Box',
                'sku' => 'GTB-001',
                'description' => 'Premium green tea with natural antioxidants. Pack of 100 tea bags.',
                'category_id' => $categories->where('name', 'Food & Beverages')->first()?->id ?? $categories->first()->id,
                'purchase_price' => 5.25,
                'sell_price' => 9.99,
                'promo_price' => 7.99,
                'stock' => 3,
                'weight' => 200.00,
                'status' => 'active',
                'expiry_date' => now()->addMonths(18),
            ],
            [
                'name' => 'Cotton T-Shirt',
                'sku' => 'CTS-001',
                'description' => '100% organic cotton t-shirt. Available in multiple sizes.',
                'category_id' => $categories->where('name', 'Clothing')->first()?->id ?? $categories->first()->id,
                'purchase_price' => 12.00,
                'sell_price' => 24.99,
                'promo_price' => null,
                'stock' => 40,
                'weight' => 150.00,
                'status' => 'active',
                'expiry_date' => null,
            ],
            [
                'name' => 'Denim Jeans',
                'sku' => 'DJ-001',
                'description' => 'Classic blue denim jeans with modern fit. Various sizes available.',
                'category_id' => $categories->where('name', 'Clothing')->first()?->id ?? $categories->first()->id,
                'purchase_price' => 35.00,
                'sell_price' => 69.99,
                'promo_price' => 54.99,
                'stock' => 22,
                'weight' => 600.00,
                'status' => 'active',
                'expiry_date' => null,
            ],
            [
                'name' => 'Essential Oil Set',
                'sku' => 'EOS-001',
                'description' => 'Set of 6 essential oils including lavender, peppermint, and eucalyptus.',
                'category_id' => $categories->where('name', 'Health & Beauty')->first()?->id ?? $categories->first()->id,
                'purchase_price' => 18.00,
                'sell_price' => 34.99,
                'promo_price' => null,
                'stock' => 18,
                'weight' => 240.00,
                'status' => 'active',
                'expiry_date' => now()->addMonths(24),
            ],
            [
                'name' => 'Yoga Mat',
                'sku' => 'YM-001',
                'description' => 'Non-slip yoga mat with alignment lines. Perfect for all yoga practices.',
                'category_id' => $categories->where('name', 'Sports & Outdoors')->first()?->id ?? $categories->first()->id,
                'purchase_price' => 22.00,
                'sell_price' => 39.99,
                'promo_price' => 29.99,
                'stock' => 12,
                'weight' => 1200.00,
                'status' => 'active',
                'expiry_date' => null,
            ],
            [
                'name' => 'Expired Vitamin Supplements',
                'sku' => 'EVS-001',
                'description' => 'Vitamin D3 supplements - EXPIRED PRODUCT FOR TESTING.',
                'category_id' => $categories->where('name', 'Health & Beauty')->first()?->id ?? $categories->first()->id,
                'purchase_price' => 8.00,
                'sell_price' => 15.99,
                'promo_price' => null,
                'stock' => 0,
                'weight' => 100.00,
                'status' => 'inactive',
                'expiry_date' => now()->subDays(30),
            ],
        ];

        foreach ($products as $productData) {
            Product::create($productData);
        }

        $this->command->info('Sample products created successfully!');
    }
}
