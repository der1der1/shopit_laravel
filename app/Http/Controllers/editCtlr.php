<?php

namespace App\Http\Controllers;

use App\Http\Requests\Product\EditProductRequest;
use App\Http\Requests\Product\AddProductRequest;
use App\Services\ProductService;
use App\Services\MarqueeService;
use Illuminate\Support\Facades\Auth;

class editCtlr extends Controller
{
    protected $productService;
    protected $marqueeService;

    public function __construct(
        ProductService $productService,
        MarqueeService $marqueeService
    ) {
        $this->productService = $productService;
        $this->marqueeService = $marqueeService;
    }

    public function edit_show()
    {
        $user = Auth::user();
        
        if ($user->prvilige !== 'A') {
            return redirect()->route('home')->with('error', '您沒有權限執行此操作');
        }

        $marqee = $this->marqueeService->getAllMarquees();
        $products = $this->productService->getAllProducts();
        
        return view('edit', compact('user', 'marqee', 'products'));
    }

    public function edit_product_store(EditProductRequest $request)
    {
        $validated = $request->validated();
        
        if (isset($validated['delete']) && $validated['delete'] === '1') {
            $this->productService->deleteProduct($validated['id']);
            return redirect()->route('edit_show')->with('success', '商品已刪除');
        }

        $this->productService->updateProduct(
            $validated['id'],
            $validated,
            $request->hasFile('pic_dir') ? $request->file('pic_dir') : null
        );

        return redirect()->route('edit_show')->with('success', '商品已更新');
    }

    public function edit_product_add(AddProductRequest $request)
    {
        try {
            $validated = $request->validated();
            
            $this->productService->createProduct(
                $validated,
                $request->file('pic_dir')
            );

            return redirect()->route('edit_show')->with('success', '新增成功');
        } catch (\Exception $e) {
            return back()->withErrors(['msg' => $e->getMessage()]);
        }
    }
}
