<?php

namespace App\Filament\Clusters\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Brand;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use App\Filament\Clusters\ManageProduct;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Clusters\Resources\BrandResource\Pages;
use App\Filament\Resources\BrandResource\RelationManagers;

class BrandResource extends Resource
{
    protected static ?string $model = Brand::class;

    protected static ?string $navigationIcon = 'heroicon-o-bookmark';

    protected static ?string $cluster = ManageProduct::class;

    public static function getModelLabel(): string
    {
        return __('brand.modelLabel');
    }

    public static function getPluralModelLabel(): string
    {
        return __('brand.pluralModelLabel');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()->schema([

                    Forms\Components\TextInput::make('brand_name')
                        ->label(__('brand.brand_name'))
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn(string $operation, $state, Set $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null)
                        ->required(),
                    Forms\Components\TextInput::make('slug')
                        ->label(__('brand.slug'))
                        ->unique(Brand::class, 'slug', ignoreRecord: true)
                        ->required(),
                    Forms\Components\Toggle::make('is_active')
                        ->label(__('brand.is_active'))
                        ->onIcon('heroicon-m-check')
                        ->offIcon('heroicon-m-x-mark')
                        ->onColor('success')
                        ->offColor('danger')
                        ->required(),
                    Forms\Components\FileUpload::make('image')
                        ->label(__('brand.image'))
                        ->image()
                        ->imageEditor(),
                ])->columns(3)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('brand_name')
                    ->label(__('brand.brand_name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('slug')
                    ->label(__('brand.slug'))
                    ->searchable(),
                Tables\Columns\ImageColumn::make('image')
                    ->label(__('brand.image')),
                Tables\Columns\IconColumn::make('is_active')
                    ->label(__('brand.is_active'))
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListBrands::route('/'),
            'create' => Pages\CreateBrand::route('/create'),
            'edit' => Pages\EditBrand::route('/{record}/edit'),
        ];
    }
}
