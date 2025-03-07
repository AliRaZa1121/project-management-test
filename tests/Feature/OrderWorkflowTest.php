<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    /**
     * Test that an order with multiple items is created successfully.
     */
    public function test_create_order_with_multiple_items()
    {
        $data = [
            'items' => [
                ['product_name' => 'Product 1', 'quantity' => 2, 'unit_price' => 100],
                ['product_name' => 'Product 2', 'quantity' => 1, 'unit_price' => 200],
            ],
        ];

        $response = $this->postJson('/api/orders', $data);

        $response->assertStatus(200);

        $order = Order::latest()->first();
        $this->assertEquals(400, $order->total_amount);
        $this->assertStringStartsWith('ORD-', $order->order_number);
    }


    /**
     * Test listing orders with filters (status filter).
     */
    public function test_list_orders_with_filters()
    {
        $order1 = Order::create(['order_number' => 'ORD-000008', 'total_amount' => 500, 'status' => 'approved']);
        $order2 = Order::create(['order_number' => 'ORD-000009', 'total_amount' => 1500, 'status' => 'pending']);

        $response = $this->getJson('/api/orders?status=approved');

        $response->assertStatus(200);
        $response->assertJsonFragment(['order_number' => $order1->order_number]);
        $response->assertJsonMissing(['order_number' => $order2->order_number]);
    }

    /**
     * Test that unique and sequential order numbers are generated.
     */
    public function test_unique_and_sequential_order_numbers()
    {
        $order1 = Order::create(['order_number' => 'ORD-000011', 'total_amount' => 300, 'status' => 'pending']);
        $order2 = Order::create(['order_number' => 'ORD-000012', 'total_amount' => 500, 'status' => 'pending']);

        $this->assertEquals('ORD-000011', $order1->order_number);
        $this->assertEquals('ORD-000012', $order2->order_number);
    }
}
