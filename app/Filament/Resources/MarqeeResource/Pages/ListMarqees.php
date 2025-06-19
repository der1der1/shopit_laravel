<?php

namespace App\Filament\Resources\MarqeeResource\Pages;

use App\Filament\Resources\MarqeeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMarqees extends ListRecords
{
    protected static string $resource = MarqeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
