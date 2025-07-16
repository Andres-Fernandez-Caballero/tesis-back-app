<?php

namespace App\Filament\Resources;

use App\Core\UseCases\UserManagement\BanUser;
use App\Core\UseCases\UserManagement\UnBanUser;
use App\Enums\Role;
use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';


    public static function canCreate(): bool
    {
        return false;
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('last_name')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('email')
                ->readOnly(),
                
                Forms\Components\Section::make('user_data')
                ->relationship('user_data')
                ->schema([
                    Forms\Components\TextInput::make('dni')
                    ->required()
                    ->maxLength(20),
                    
                    Forms\Components\TextInput::make('phone')
                    ->required()
                    ->maxLength(15),
                    
                    Forms\Components\TextInput::make('address')
                    ->required()
                    ->maxLength(255),
                    
                    Forms\Components\Select::make('gender')
                    ->required()
                    ->options(['male' => 'Masculino', 'famale' => 'Femenino', 'other' => 'Otro']),
                    
                    Forms\Components\DatePicker::make('birth_date')
                    ->required()
                    ->maxDate(now()->subYears(18))
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('email'),
                Tables\Columns\TextColumn::make('roles.name')
                ->badge()
                ->color(fn($state) => match($state) {
                    Role::ADMIN->value => 'success',
                    Role::CLIENT->value => 'info',
                    Role::MASSAGE_THERAPIST->value => 'secondary',
                    default => 'secondary',
                }),
                Tables\Columns\TextColumn::make('score'),
                Tables\Columns\TextColumn::make('state')
                ->formatStateUsing(fn($state) => $state->label())
                ->color(fn($record) => $record->state->color()),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                self::banUserAction()
                ->hidden(fn($record) => !$record->state->isActive()),
                self::unBanUserAction()
                ->hidden(fn($record) => $record->state->isActive()),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    private static function banUserAction(): Tables\Actions\Action
    {
        return Tables\Actions\Action::make('ban')
            ->action(function (User $record) {
                try{
                    app(BanUser::class)->execute($record->id, now()->addDays(30));
                }catch(\Exception $e){
                    Notification::make()
                    ->title('Error')
                    ->body($e->getMessage())
                    ->danger()
                    ->send();
                    return;
                }
            })
            ->requiresConfirmation()
            ->color('danger');
    }

    private static function unBanUserAction(): Tables\Actions\Action
    {
        return Tables\Actions\Action::make('unban')
            ->action(function (User $record) {
                try{
                    app(UnBanUser::class)->execute($record->id);
                }catch(\Exception $e){
                    Notification::make()
                    ->title('Error')
                    ->body($e->getMessage())
                    ->danger()
                    ->send();
                    return;
                }
            })
            ->requiresConfirmation()
            ->color('success');
    }
}
