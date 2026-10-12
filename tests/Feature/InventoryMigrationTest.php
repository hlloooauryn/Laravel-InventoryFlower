<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryMigrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/');
        $response->assertRedirect('/login');
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $response = $this->post('/login', [
            'username' => 'admin',
            'password' => 'admin123',
        ]);

        $response->assertRedirect('/');
        $this->assertAuthenticated();
    }

    public function test_product_listing_and_filtering(): void
    {
        $admin = User::where('username', 'admin')->first();

        $response = $this->actingAs($admin)->get('/products?search=Mawar');
        $response->assertStatus(200);
        $response->assertSee('Mawar Merah Premium');
    }

    public function test_product_creation_and_initial_stock_movement(): void
    {
        $admin = User::where('username', 'admin')->first();
        $category = Category::first();

        $response = $this->actingAs($admin)->post('/products', [
            'sku' => 'FL-TEST99',
            'category_id' => $category->id,
            'name' => 'Bunga Anggrek Ungu',
            'price' => 50000,
            'stock' => 10,
            'minimum_stock' => 2,
        ]);

        $response->assertRedirect('/products');
        $this->assertDatabaseHas('products', ['sku' => 'FL-TEST99', 'name' => 'Bunga Anggrek Ungu']);

        $product = Product::where('sku', 'FL-TEST99')->first();
        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'type' => 'IN',
            'quantity' => 10,
        ]);
    }

    public function test_business_rule_prevent_product_deletion_if_stock_movement_exists(): void
    {
        $admin = User::where('username', 'admin')->first();
        $product = Product::where('sku', 'FL-ROSERED')->first();

        $this->assertTrue($product->stockMovements()->exists());

        $response = $this->actingAs($admin)->delete("/products/{$product->id}");
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('products', ['id' => $product->id]);
    }

    public function test_stock_out_decrements_and_prevents_negative_stock(): void
    {
        $admin = User::where('username', 'admin')->first();
        $product = Product::where('sku', 'FL-ROSERED')->first();
        $initialStock = $product->stock;

        // Valid stock out
        $response = $this->actingAs($admin)->post('/transactions/stock-out', [
            'product_id' => $product->id,
            'quantity' => 5,
            'note' => 'Penjualan buket',
        ]);

        $response->assertRedirect('/products');
        $product->refresh();
        $this->assertEquals($initialStock - 5, $product->stock);

        // Exceeding stock out (business rule check)
        $responseExcess = $this->actingAs($admin)->post('/transactions/stock-out', [
            'product_id' => $product->id,
            'quantity' => 9999,
            'note' => 'Permintaan berlebih',
        ]);

        $responseExcess->assertSessionHas('error');
        $product->refresh();
        $this->assertEquals($initialStock - 5, $product->stock);
    }
}
