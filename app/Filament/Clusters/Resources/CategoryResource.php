<?php

namespace App\Filament\Clusters\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Set;
use App\Models\Category;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use App\Filament\Clusters\ManageProduct;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Clusters\Resources\CategoryResource\Pages;
use App\Filament\Resources\CategoryResource\RelationManagers;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $cluster = ManageProduct::class;

    public static function getModelLabel(): string
    {
        return __('category.modelLabel');
    }

    public static function getPluralModelLabel(): string
    {
        return __('category.pluralModelLabel');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()->schema([

                    Forms\Components\Select::make('parent_id')
                        ->label(__('category.parent_id'))
                        ->relationship('parent', 'id'),
                    Forms\Components\TextInput::make('category_name')
                        ->label(__('category.category_name'))
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn(string $operation, $state, Set $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null)
                        ->required(),
                    Forms\Components\TextInput::make('slug')
                        ->label(__('category.slug'))
                        ->unique(Category::class, 'slug', ignoreRecord: true)
                        ->required(),
                    Forms\Components\Toggle::make('is_active')
                        ->label(__('category.is_active'))
                        ->required(),
                    Forms\Components\FileUpload::make('image')
                        ->label(__('category.image'))
                        ->image(),
                ])->columns(3)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('parent.id')
                    ->label(__('category.parent_id'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('category_name')
                    ->label(__('category.category_name'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('slug')
                    ->label(__('category.slug'))
                    ->searchable(),
                Tables\Columns\ImageColumn::make('image')
                    ->label(__('category.image')),
                Tables\Columns\IconColumn::make('is_active')
                    ->label(__('category.is_active'))
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
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
