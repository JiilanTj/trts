<?php

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function makeUser(array $overrides = []): User {
    return User::factory()->create(array_merge([
        'role' => 'user',
        'is_seller' => false,
    ], $overrides));
}

function makeProduct(array $overrides = []): Product {
    return Product::factory()->create(array_merge([
        'stock' => 10,
        'purchase_price' => 10000,
        'sell_price' => 20000,
        'promo_price' => null,
        'status' => 'active',
        'sku' => 'SKU' . uniqid(),
        'name' => 'Produk Uji',
        'profit' => 10000,
        'category_id' => 1,
    ], $overrides));
}

beforeEach(function(){
    // Minimal category (table has no slug column in current migration).
    \DB::table('categories')->insert([
        'id' => 1,
        'name' => 'Kategori Uji',
        'status' => 'active',
        'created_at' => now(),
        'updated_at' => now(),
    ]);
});

it('non-seller buys with harga biasa (no promo)', function(){
    $user = makeUser();
    $product = makeProduct();
    $this->actingAs($user)
        ->post(route('browse.products.buy', $product), [ 'purchase_type' => 'self'])
        ->assertRedirect();
    $this->assertStringContainsString('Harga Biasa', session('success'));
    $this->assertStringNotContainsString('Harga Jual', session('success'));
});

it('seller self purchase uses harga biasa when promo absent', function(){
    $user = makeUser(['is_seller' => true]);
    $product = makeProduct();
    $this->actingAs($user)
        ->post(route('browse.products.buy', $product), [ 'purchase_type' => 'self'])
        ->assertRedirect();
    $this->assertStringContainsString('Harga Biasa', session('success'));
});

it('seller external purchase uses harga jual', function(){
    $user = makeUser(['is_seller' => true]);
    $product = makeProduct();
    $this->actingAs($user)
        ->post(route('browse.products.buy', $product), [ 'purchase_type' => 'external'])
        ->assertRedirect();
    $this->assertStringContainsString('Harga Jual', session('success'));
});

it('harga biasa picks promo when promo lower than sell_price', function(){
    $user = makeUser();
    $product = makeProduct(['promo_price' => 15000]);
    expect($product->harga_biasa)->toBe(15000);
});

it('harga biasa falls back to sell_price when promo higher or equal', function(){
    $user = makeUser();
    $product = makeProduct(['promo_price' => 25000]);
    expect($product->harga_biasa)->toBe($product->sell_price);
});

it('external purchase ignores promo and uses sell_price', function(){
    $user = makeUser(['is_seller' => true]);
    $product = makeProduct(['promo_price' => 15000]);
    $this->actingAs($user)
        ->post(route('browse.products.buy', $product), [ 'purchase_type' => 'external'])
        ->assertRedirect();
    $this->assertStringContainsString('Harga Jual', session('success'));
});

it('rejects invalid purchase_type and defaults to self', function(){
    $user = makeUser(['is_seller' => true]);
    $product = makeProduct();
    $this->actingAs($user)
        ->post(route('browse.products.buy', $product), [ 'purchase_type' => 'xxx'])
        ->assertRedirect();
    $this->assertStringContainsString('Harga Biasa', session('success'));
});

it('cannot buy inactive product', function(){
    $user = makeUser();
    $product = makeProduct(['status' => 'inactive']);
    $this->actingAs($user)
        ->post(route('browse.products.buy', $product))
        ->assertStatus(404);
});

it('cannot buy when stock empty', function(){
    $user = makeUser();
    $product = makeProduct(['stock' => 0]);
    $this->actingAs($user)
        ->post(route('browse.products.buy', $product))
        ->assertSessionHas('error');
});
