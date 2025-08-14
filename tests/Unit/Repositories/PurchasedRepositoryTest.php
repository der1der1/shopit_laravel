<?php

namespace Tests\Unit\Repositories;

use Tests\TestCase;
use App\Repositories\PurchasedRepository;
use App\Models\purchasedModel;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PurchasedRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected $purchasedRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->purchasedRepository = new PurchasedRepository();
    }

    public function test_find_by_id()
    {
        $purchased = purchasedModel::factory()->create([
            'account' => 'test@example.com',
            'purchased' => '1,2;3,1',
            'show' => '1'
        ]);

        $result = $this->purchasedRepository->findById($purchased->id);
        
        $this->assertEquals($purchased->id, $result->id);
        $this->assertEquals($purchased->account, $result->account);
    }

    public function test_get_visible_orders()
    {
        purchasedModel::factory()->create([
            'account' => 'test1@example.com',
            'purchased' => '1,2',
            'show' => '1'
        ]);

        purchasedModel::factory()->create([
            'account' => 'test2@example.com',
            'purchased' => '3,1',
            'show' => '0'
        ]);

        $results = $this->purchasedRepository->getVisibleOrders();
        
        $this->assertCount(1, $results);
        $this->assertEquals('test1@example.com', $results->first()->account);
    }

    public function test_create()
    {
        $data = [
            'account' => 'test@example.com',
            'purchased' => '1,2;3,1',
            'show' => '1'
        ];

        $result = $this->purchasedRepository->create($data);
        
        $this->assertDatabaseHas('purchaseds', $data);
        $this->assertEquals($data['account'], $result->account);
    }

    public function test_update()
    {
        $purchased = purchasedModel::factory()->create([
            'account' => 'test@example.com',
            'purchased' => '1,2;3,1',
            'show' => '1'
        ]);

        $this->purchasedRepository->update($purchased->id, [
            'show' => '0'
        ]);

        $this->assertDatabaseHas('purchaseds', [
            'id' => $purchased->id,
            'show' => '0'
        ]);
    }

    public function test_delete()
    {
        $purchased = purchasedModel::factory()->create();
        
        $this->purchasedRepository->delete($purchased->id);
        
        $this->assertDatabaseMissing('purchaseds', [
            'id' => $purchased->id
        ]);
    }

    public function test_get_last_order_by_account()
    {
        purchasedModel::factory()->create([
            'account' => 'test@example.com',
            'purchased' => '1,2',
            'created_at' => now()->subDay()
        ]);

        $lastOrder = purchasedModel::factory()->create([
            'account' => 'test@example.com',
            'purchased' => '3,1',
            'created_at' => now()
        ]);

        $result = $this->purchasedRepository->getLastOrderByAccount('test@example.com');
        
        $this->assertEquals($lastOrder->id, $result->id);
    }

    public function test_get_user_orders()
    {
        purchasedModel::factory()->create([
            'account' => 'test@example.com',
            'purchased' => '1,2'
        ]);

        purchasedModel::factory()->create([
            'account' => 'test@example.com',
            'purchased' => '3,1'
        ]);

        purchasedModel::factory()->create([
            'account' => 'other@example.com',
            'purchased' => '4,1'
        ]);

        $results = $this->purchasedRepository->getUserOrders('test@example.com');
        
        $this->assertCount(2, $results);
        $this->assertEquals('test@example.com', $results->first()->account);
    }
}