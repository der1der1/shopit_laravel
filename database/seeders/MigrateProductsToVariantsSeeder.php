<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\productsModel;
use App\Models\ProductVariantModel;

class MigrateProductsToVariantsSeeder extends Seeder
{
    /**
     * 將現有商品資料轉換為預設品項
     * 
     * 此 Seeder 會為每個現有商品建立一個「標準版」預設品項
     * 將商品的 price, ori_price, quantity, min_quantity 複製到品項中
     */
    public function run(): void
    {
        $this->command->info('開始遷移商品資料到品項表...');

        // 取得所有商品
        $products = productsModel::all();
        $migratedCount = 0;
        $skippedCount = 0;

        foreach ($products as $product) {
            // 檢查該商品是否已有品項
            $existingVariants = ProductVariantModel::where('product_id', $product->id)->count();
            
            if ($existingVariants > 0) {
                $this->command->warn("商品 ID {$product->id} ({$product->product_name}) 已有 {$existingVariants} 個品項，跳過");
                $skippedCount++;
                continue;
            }

            // 建立預設品項
            try {
                ProductVariantModel::create([
                    'product_id' => $product->id,
                    'variant_name' => '標準版',
                    'unicode' => 'STD-' . $product->id . '-' . time(),
                    'price' => $product->price ?? 0,
                    'ori_price' => $product->ori_price ?? null,
                    'use_oriprice' => !empty($product->ori_price),
                    'quantity' => $product->quantity ?? 0,
                    'min_quantity' => $product->min_quantity ?? 0,
                    'pic_dir' => $product->pic_dir ?? null,
                    'is_default' => true,
                    'is_active' => $product->is_active ?? true,
                    'sort_order' => 0,
                ]);

                $migratedCount++;
                $this->command->info("✓ 商品 ID {$product->id} ({$product->product_name}) 已建立預設品項");
            } catch (\Exception $e) {
                $this->command->error("✗ 商品 ID {$product->id} 建立品項失敗: " . $e->getMessage());
            }
        }

        $this->command->info("========================================");
        $this->command->info("遷移完成！");
        $this->command->info("總商品數：{$products->count()}");
        $this->command->info("成功遷移：{$migratedCount}");
        $this->command->info("跳過：{$skippedCount}");
        $this->command->info("========================================");
    }
}
