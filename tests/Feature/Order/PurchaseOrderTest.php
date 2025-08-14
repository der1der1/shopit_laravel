<?php

namespace Tests\Feature\Order;

use Tests\TestCase;
use App\Models\User;
use App\Models\productsModel;
use App\Models\purchasedModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class PurchaseOrderTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create([
            'prvilige' => 'B',
            'account' => 'test@example.com',
            'want' => ''
        ]);

        $this->actingAs($this->user);
    }

    public function test_user_can_add_product_to_cart()
    {
        $product = productsModel::factory()->create();

        $response = $this->post(route('want'), [
            'product_id' => $product->id
        ]);

        $response->assertRedirect(route('home'));
        $response->assertSessionHas('success', '加入成功');

        $this->user->refresh();
        $this->assertStringContainsString(
            $product->id . ',',
            $this->user->want
        );
    }

    public function test_user_can_view_payment_page()
    {
        $product = productsModel::factory()->create();
        $purchased = purchasedModel::factory()->create([
            'account' => $this->user->account,
            'purchased' => "{$product->id},2"
        ]);

        $response = $this->get(route('pay_show'));

        $response->assertStatus(200);
        $response->assertViewHas('products');
        $response->assertViewHas('purchased');
        $response->assertSee($product->product_name);
    }

    public function test_user_can_set_delivery_to_store()
    {
        $purchased = purchasedModel::factory()->create([
            'account' => $this->user->account
        ]);

        $response = $this->post(route('pay_to_shop'), [
            'store' => '測試門市'
        ]);

        $response->assertRedirect(route('pay_show'));
        $response->assertSessionHas('success', '超商寄送到測試門市');

        $this->assertDatabaseHas('purchaseds', [
            'id' => $purchased->id,
            'to_shop' => '測試門市',
            'shop1_addr2' => '1'
        ]);
    }

    public function test_user_can_set_delivery_to_home()
    {
        $purchased = purchasedModel::factory()->create([
            'account' => $this->user->account
        ]);

        $response = $this->post(route('pay_to_home'), [
            'address' => '測試地址'
        ]);

        $response->assertRedirect(route('pay_show'));
        $response->assertSessionHas('success', '宅配到：測試地址');

        $this->assertDatabaseHas('purchaseds', [
            'id' => $purchased->id,
            'to_address' => '測試地址',
            'shop1_addr2' => '2'
        ]);
    }

    public function test_user_can_set_payment_info()
    {
        $purchased = purchasedModel::factory()->create([
            'account' => $this->user->account
        ]);

        $response = $this->post(route('pay_name'), [
            'name_input' => '測試用戶'
        ]);

        $response->assertRedirect(route('pay_show'));
        $response->assertSessionHas('success', '取貨大名：測試用戶');

        $this->assertDatabaseHas('purchaseds', [
            'id' => $purchased->id,
            'name' => '測試用戶'
        ]);

        $response = $this->post(route('pay_account'), [
            'account_input' => '123456789'
        ]);

        $response->assertRedirect(route('pay_show'));
        $response->assertSessionHas('success', '扣款帳號：123456789');

        $this->assertDatabaseHas('purchaseds', [
            'id' => $purchased->id,
            'bank_account' => '123456789'
        ]);
    }

    public function test_user_can_confirm_order()
    {
        $purchased = purchasedModel::factory()->create([
            'account' => $this->user->account,
            'name' => '測試用戶',
            'bank_account' => '123456789',
            'shop1_addr2' => '1',
            'show' => '0'
        ]);

        $response = $this->post(route('pay_confirm'), [
            'name' => '測試用戶',
            'bank_account' => '123456789',
            'shop1_addr2' => '1'
        ]);

        $response->assertRedirect(route('home'));
        $response->assertSessionHas('success', '訂單已送出！');

        $this->assertDatabaseHas('purchaseds', [
            'id' => $purchased->id,
            'show' => '1'
        ]);
    }
}