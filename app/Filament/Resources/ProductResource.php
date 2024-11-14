<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Product;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\ProductResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ProductResource\RelationManagers;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    public static function getModelLabel(): string
    {
        return __('product.modelLabel');
    }

    public static function getPluralModelLabel(): string
    {
        return __('product.pluralModelLabel');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()->schema([
                    Forms\Components\Section::make()->schema([
                        Forms\Components\TextInput::make('product_name')
                            ->label(__('product.product_name'))
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn(string $operation, $state, Set $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null)
                            ->columnSpanFull()
                            ->required(),
                        Forms\Components\TextInput::make('slug')
                            ->label(__('product.slug'))
                            ->unique(Product::class, 'slug', ignoreRecord: true)
                            ->required(),
                        Forms\Components\TextInput::make('price')
                            ->label(__('product.price'))
                            ->numeric()
                            ->prefix('$'),
                        Forms\Components\MarkdownEditor::make('description')
                            ->label(__('product.description'))
                            ->columnSpanFull(),
                        Forms\Components\FileUpload::make('images')
                            ->label(__('product.images'))
                            ->image()
                            ->imageEditor()
                            ->multiple()
                            ->downloadable()
                            ->openable()
                            ->panelLayout('grid')
                            ->columnSpanFull(),
                    ])->columns(2),
                ])->columnSpan(2),
                Forms\Components\Group::make()->schema([
                    Forms\Components\Section::make(__('product.associattion_section'))->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label(__('product.is_active'))
                            ->required(),
                        Forms\Components\Toggle::make('is_featured')
                            ->label(__('product.is_featured'))
                            ->required(),
                        Forms\Components\Toggle::make('in_stock')
                            ->label(__('product.in_stock'))
                            ->required(),
                        Forms\Components\Toggle::make('on_sale')
                            ->label(__('product.on_sale'))
                            ->required(),
                        Forms\Components\Select::make('category_id')
                            ->label(__('product.category_id'))
                            ->relationship('category', 'category_name')
                            ->columnSpanFull(),
                        Forms\Components\Select::make('brand_id')
                            ->label(__('product.brand_id'))
                            ->relationship('brand', 'brand_name')
                            ->columnSpanFull(),
                    ])->columns(2)->columnSpan(1)
                ])->columnSpan(1)
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('product_name')
                    ->label(__('product.product_name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('category.category_name')
                    ->label(__('product.category_id'))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('brand.brand_name')
                    ->label(__('product.brand_id'))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('slug')
                    ->label(__('product.slug'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('price')
                    ->label(__('product.price'))
                    ->money('CLP')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label(__('product.is_active'))
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_featured')
                    ->label(__('product.is_featured'))
                    ->boolean(),
                Tables\Columns\IconColumn::make('in_stock')
                    ->label(__('product.in_stock'))
                    ->boolean(),
                Tables\Columns\IconColumn::make('on_sale')
                    ->label(__('product.on_sale'))
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('product.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('product.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])
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
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
