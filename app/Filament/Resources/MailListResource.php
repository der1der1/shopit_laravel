<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MailListResource\Pages;
use App\Models\mailListModel;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

use Filament\Tables\Columns\TextInputColumn;


class MailListResource extends Resource
{
    protected static ?string $model = mailListModel::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Checkbox::make('onoff')
                    ->label('啟用'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextInputColumn::make('name')
                    ->columnSpan('full')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextInputColumn::make('title')
                    ->columnSpan('full')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextInputColumn::make('email')
                    ->columnSpan('full')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\ToggleColumn::make('onoff')
                    ->columnSpan('full')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
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
            'index' => Pages\ListMailLists::route('/'),
            'create' => Pages\CreateMailList::route('/create'),
            'edit' => Pages\EditMailList::route('/{record}/edit'),
        ];
    }
}
