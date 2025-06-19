<?php

namespace App\Filament\Resources\ProductsResource\Pages;

use App\Filament\Resources\ProductsResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateProducts extends CreateRecord
{
    protected static string $resource = ProductsResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // 如果 new_category 有值，使用它作為 category
        $data['category'] = $data['new_category'] ?: $data['category'];
        unset($data['new_category']); // 移除 new_category，避免保存到資料庫
        return $data;
    }
}
