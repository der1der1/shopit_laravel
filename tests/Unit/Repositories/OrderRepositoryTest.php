<?php

namespace Tests\Unit\Repositories;

use Tests\TestCase;
use App\Models\purchasedModel as Order;
use App\Models\productsModel as Product;
use App\Repositories\OrderRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected $orderRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->orderRepository = new OrderRepository();
    }

    public function test_find_orders_by_account()
    {
        // Create test products
        $product1 = Product::factory()->create([
            'product_name' => 'Test Product 1',
            'price' => 100,
            'storage' => 10
        ]);

        $product2 = Product::factory()->create([
            'product_name' => 'Test Product 2',
            'price' => 200,
            'storage' => 10
        ]);

        // Create test order
        $order = Order::create([
            'account' => 'test@example.com',
            'purchased' => $product1->id . ',2,' . $product1->price . ';' . $product2->id . ',1,' . $product2->price,
            'name' => 'Test User',
            'to_shop' => 'Test Shop',
            'to_address' => 'Test Address',
            'shop1_addr2' => '1',
            'bank_account' => '1234567890',
            'total' => 300,
            'show' => 0,
            'payed' => 0,
            'delivered' => 0,
            'bill' => '123456'
        ]);

        // Test finding all orders for an account
        $orders = $this->orderRepository->findByAccount('test@example.com');

        $this->assertCount(1, $orders);
        $this->assertEquals($order->id, $orders->first()->id);
        
        // Verify purchased items are properly formatted
        $formattedPurchased = $orders->first()->purchased;
        $this->assertIsArray($formattedPurchased);
        $this->assertCount(2, $formattedPurchased);
        
        $this->assertEquals([
            'product_name' => 'Test Product 1',
            'number' => '2',
            'price' => '100'
        ], $formattedPurchased[0]);

        $this->assertEquals([
            'product_name' => 'Test Product 2',
            'number' => '1',
            'price' => '200'
        ], $formattedPurchased[1]);
    }

    public function test_find_specific_order_by_account_and_id()
    {
        // Create test product
        $product = Product::factory()->create([
            'product_name' => 'Test Product',
            'price' => 100,
            'storage' => 10
        ]);

        // Create test order
        $order = Order::create([
            'account' => 'test@example.com',
            'purchased' => $product->id . ',1,' . $product->price,
            'name' => 'Test User',
            'to_shop' => 'Test Shop',
            'to_address' => 'Test Address',
            'shop1_addr2' => '1',
            'bank_account' => '1234567890',
            'total' => 100,
            'show' => 0,
            'payed' => 0,
            'delivered' => 0,
            'bill' => '123456'
        ]);

        // Test finding specific order
        $orders = $this->orderRepository->findByAccount('test@example.com', $order->id);

        $this->assertCount(1, $orders);
        $this->assertEquals($order->id, $orders->first()->id);
    }

    public function test_handle_invalid_purchase_format()
    {
        // Create test order with invalid purchase format
        $order = Order::create([
            'account' => 'test@example.com',
            'purchased' => '1,100', // Missing quantity
            'name' => 'Test User',
            'to_shop' => 'Test Shop',
            'to_address' => 'Test Address',
            'shop1_addr2' => '1',
            'bank_account' => '1234567890',
            'total' => 100,
            'show' => 0,
            'payed' => 0,
            'delivered' => 0,
            'bill' => '123456'
        ]);

        $orders = $this->orderRepository->findByAccount('test@example.com');
        
        // Should still return the order but with empty purchased array
        $this->assertCount(1, $orders);
        $this->assertEmpty($orders->first()->purchased);
    }

    public function test_handle_nonexistent_product()
    {
        // Create order with non-existent product ID
        $order = Order::create([
            'account' => 'test@example.com',
            'purchased' => '999,1,100', // Non-existent product ID
            'name' => 'Test User',
            'to_shop' => 'Test Shop',
            'to_address' => 'Test Address',
            'shop1_addr2' => '1',
            'bank_account' => '1234567890',
            'total' => 100,
            'show' => 0,
            'payed' => 0,
            'delivered' => 0,
            'bill' => '123456'
        ]);

        $orders = $this->orderRepository->findByAccount('test@example.com');
        
        // Should still return the order but with empty purchased array
        $this->assertCount(1, $orders);
        $this->assertEmpty($orders->first()->purchased);
    }

    public function test_handle_empty_purchased_field()
    {
        // Create order with empty purchased field
        $order = Order::create([
            'account' => 'test@example.com',
            'purchased' => '',
            'name' => 'Test User',
            'to_shop' => 'Test Shop',
            'to_address' => 'Test Address',
            'shop1_addr2' => '1',
            'bank_account' => '1234567890',
            'total' => 0,
            'show' => 0,
            'payed' => 0,
            'delivered' => 0,
            'bill' => '123456'
        ]);

        $orders = $this->orderRepository->findByAccount('test@example.com');
        
        $this->assertCount(1, $orders);
        $this->assertIsArray($orders->first()->purchased);
        $this->assertEmpty($orders->first()->purchased);
    }
}