<?php

namespace App\Filament\Resources\Utils\Tags;

use App\Filament\Resources\Utils\Tags\TagWithImageResource\Pages;
use App\Filament\Resources\Utils\Tags\TagWithImageResource\RelationManagers;
use App\Models\Utils\Tags\TagWithImage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TagWithImageResource extends Resource
{
    protected static ?string $model = TagWithImage::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $label = "Tags";

    protected static ?string $modelLabel = "Tag";

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name.massagist')
                    ->label('Nombre (massagist)')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('slug.massagist')
                    ->label('Slug (massagist)')
                    ->maxLength(255),

                Forms\Components\TextInput::make('type')
                    ->label('Tipo')
                    ->default('es')
                    ->required()
                    ->maxLength(10),

                Forms\Components\FileUpload::make('image')
                    ->label('Imagen')
                    ->image()
                    ->directory('tags/images')
                    ->maxSize(1024),
            ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->getStateUsing(fn(TagWithImage $record) => $record->getTranslation('name', 'massagist')),
                Tables\Columns\ImageColumn::make('image')
                    ->label('Imagen'),

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
            'index' => Pages\ListTagWithImages::route('/'),
            'create' => Pages\CreateTagWithImage::route('/create'),
            'edit' => Pages\EditTagWithImage::route('/{record}/edit'),
        ];
    }
}
