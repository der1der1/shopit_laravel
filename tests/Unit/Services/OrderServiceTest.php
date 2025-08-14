<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\OrderService;
use App\Services\EmailService;
use App\Services\ProductService;
use App\Repositories\PurchasedRepository;
use App\Repositories\UserRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Illuminate\Support\Facades\Auth;

class OrderServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $orderService;
    protected $purchasedRepository;
    protected $userRepository;
    protected $emailService;
    protected $productService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->purchasedRepository = Mockery::mock(PurchasedRepository::class);
        $this->userRepository = Mockery::mock(UserRepository::class);
        $this->emailService = Mockery::mock(EmailService::class);
        $this->productService = Mockery::mock(ProductService::class);

        $this->orderService = new OrderService(
            $this->purchasedRepository,
            $this->userRepository,
            $this->emailService,
            $this->productService
        );
    }

    public function test_add_to_cart()
    {
        $user = (object)[
            'id' => 1,
            'account' => 'test@example.com',
            'want' => ''
        ];

        Auth::shouldReceive('user')
            ->once()
            ->andReturn($user);

        $this->userRepository->shouldReceive('findByAccount')
            ->with('test@example.com')
            ->once()
            ->andReturn($user);

        $this->userRepository->shouldReceive('update')
            ->with(1, ['want' => '123,'])
            ->once()
            ->andReturn(true);

        $result = $this->orderService->addToCart('123');
        $this->assertTrue($result);
    }

    public function test_get_last_order()
    {
        $order = (object)[
            'id' => 1,
            'purchased' => '1,2;3,1',
            'account' => 'test@example.com'
        ];

        $user = (object)[
            'id' => 1,
            'account' => 'test@example.com'
        ];

        $product1 = (object)[
            'id' => 1,
            'product_name' => 'Test Product 1',
            'description' => 'Test Description 1',
            'price' => 100,
            'pic_dir' => 'test1.jpg'
        ];

        $product2 = (object)[
            'id' => 3,
            'product_name' => 'Test Product 2',
            'description' => 'Test Description 2',
            'price' => 200,
            'pic_dir' => 'test2.jpg'
        ];

        $this->purchasedRepository->shouldReceive('getLastOrderByAccount')
            ->with('test@example.com')
            ->once()
            ->andReturn($order);

        $this->productService->shouldReceive('getProductById')
            ->with('1')
            ->once()
            ->andReturn($product1);

        $this->productService->shouldReceive('getProductById')
            ->with('3')
            ->once()
            ->andReturn($product2);

        $this->userRepository->shouldReceive('findByAccount')
            ->with('test@example.com')
            ->once()
            ->andReturn($user);

        $result = $this->orderService->getLastOrder('test@example.com');

        $this->assertEquals($order, $result['order']);
        $this->assertEquals($user, $result['user']);
        $this->assertCount(2, $result['products']);
    }

    public function test_confirm_order()
    {
        $order = (object)[
            'id' => 1,
            'purchased' => '1,2',
            'account' => 'test@example.com',
            'toArray' => function() {
                return ['id' => 1];
            }
        ];

        $user = (object)[
            'id' => 1,
            'account' => 'test@example.com',
            'info0' => 'Old message'
        ];

        $product = (object)[
            'id' => 1,
            'product_name' => 'Test Product',
            'description' => 'Test Description',
            'price' => 100,
            'pic_dir' => 'test.jpg'
        ];

        $this->purchasedRepository->shouldReceive('getLastOrderByAccount')
            ->with('test@example.com')
            ->once()
            ->andReturn($order);

        $this->userRepository->shouldReceive('findByAccount')
            ->with('test@example.com')
            ->once()
            ->andReturn($user);

        $this->purchasedRepository->shouldReceive('update')
            ->with(1, ['show' => '1'])
            ->once()
            ->andReturn(true);

        $this->userRepository->shouldReceive('update')
            ->once()
            ->andReturn(true);

        $this->productService->shouldReceive('getProductById')
            ->with('1')
            ->once()
            ->andReturn($product);

        $this->emailService->shouldReceive('sendPurchaseConfirmation')
            ->once()
            ->andReturn(true);

        $result = $this->orderService->confirmOrder('test@example.com');
        $this->assertTrue($result);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}