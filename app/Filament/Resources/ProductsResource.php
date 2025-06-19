<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductsResource\Pages;
use App\Filament\Resources\ProductsResource\RelationManagers;
use App\Models\productsModel;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
// for uploading files
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\ActionGroup;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Tables\Filters\SelectFilter;

class ProductsResource extends Resource
{
    protected static ?string $model = productsModel::class;
    protected static ?string $navigationIcon = 'heroicon-o-cake';
    // protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()
                    ->schema([
                        Section::make('information')
                            ->schema([
                                Forms\Components\TextInput::make('product_name')
                                    ->required()
                                    ->maxLength(45),
                                Section::make('category')
                                    ->description('Select an existing category or enter a new one.')
                                        ->schema([
                                            Forms\Components\Select::make('category')
                                                ->label('Category')
                                                ->options(
                                                    productsModel::query()
                                                        ->distinct()
                                                        ->pluck('category', 'category') // 從本表抓取唯一的 category 值
                                                        ->toArray()
                                                )
                                                ->searchable()
                                                ->preload()
                                                ->placeholder('Select an existing category'),
                                            Forms\Components\TextInput::make('new_category')
                                                ->label('Or enter a new category')
                                                ->placeholder('Enter a new category'),
                                ]),
                            ])->columnSpan(2),
                        Group::make()
                            ->schema([
                                Section::make('The Price')->schema([
                                    Forms\Components\TextInput::make('price')
                                        ->numeric()
                                        ->required()
                                        ->prefix('$')
                                        ->maxLength(45),
                                    Forms\Components\TextInput::make('ori_price')
                                        ->required()
                                        ->maxLength(45),
                                ]),
                                Section::make('The Picture')->schema([
                                    Forms\Components\TextInput::make('pic_name')
                                        ->maxLength(45),
                                    Forms\Components\FileUpload::make('pic_dir')
                                        ->image()
                                        ->imageEditor()  // editable
                                        ->imagePreviewHeight('250') // 啟用預覽
                                        ->imageEditorAspectRatios([  // for cropping image
                                            null,
                                            '16:9',
                                            '4:3',
                                            '1:1',
                                        ])
                                        ->visibility('public') // Set visibility to private??
                                        ->label('Product Image') // Label for the file upload field 
                                        ->disk('public') // Specify the disk where files will be stored
                                        ->directory('img/pictureTarget') // Specify the directory within the disk
                                        ->preserveFilenames() // Preserve original filenames
                                        // ->maxSize(30760) // Set maximum file size in KB (3 MB in this case)
                                        ->downloadable()
                                        ->moveFiles(true) // Move files to the specified directory
                                ]),
                            ])->columnSpan(1),
                        Section::make('description')
                            ->schema([
                                Forms\Components\MarkdownEditor::make('description')
                                    ->required()
                                    ->maxLength(225)
                                    ->columnSpanFull(),
                            ])->columns(1),
                    ])->columns(3)
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                Tables\Columns\TextColumn::make('pic_name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\ImageColumn::make('pic_dir')
                    ->disk('public')
                    ->label('Product Image')
                    ->sortable(),
                Tables\Columns\TextColumn::make('product_name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->money('USD')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ori_price')
                    ->money('USD')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('category')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('category')  // 這邊一定要放這個table的 col name
                    ->label('category filter')
                    ->options(
                        productsModel::query()
                            ->distinct()
                            ->pluck('category', 'category') // 從本表抓取唯一的 category 值
                            ->toArray()
                    ),
                SelectFilter::make('product_name')  // 這邊一定要放這個table的 col name
                    ->label('name filter')
                    ->options(
                        productsModel::query()
                            ->distinct()
                            ->pluck('product_name', 'product_name') // 從本表抓取唯一的 category 值
                            ->toArray()
                    ),
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProducts::route('/create'),
            'edit' => Pages\EditProducts::route('/{record}/edit'),
        ];
    }
}
