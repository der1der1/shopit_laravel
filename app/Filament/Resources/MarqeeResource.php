<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MarqeeResource\Pages;
use App\Filament\Resources\MarqeeResource\RelationManagers;
use App\Models\marqeeModel;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MarqeeResource extends Resource
{
    protected static ?string $model = marqeeModel::class;

    protected static ?string $navigationIcon = 'heroicon-o-queue-list';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('texts')
                    ->required()
                    ->maxLength(255),
                Forms\Components\DatePicker::make('updated_at')
                    ->required()
                    ->maxDate(now()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('texts')
                    ->columnSpan('full')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMarqees::route('/'),
            'create' => Pages\CreateMarqee::route('/create'),
            'edit' => Pages\EditMarqee::route('/{record}/edit'),
        ];
    }
}
