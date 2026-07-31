<?php

namespace App\Filament\Admin\Resources;

use App\Enums\SubscriptionStatus;
use App\Filament\Admin\Resources\SubscriptionResource\Pages;
use App\Filament\Admin\Resources\SubscriptionResource\RelationManagers;
use App\Models\Subscriptions\Subscription;
use Carbon\Carbon;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SubscriptionResource extends Resource
{
    protected static ?string $model = Subscription::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationLabel = 'Suscripciones';

    protected static ?string $navigationGroup = 'Locales';

    protected static ?string $pluralModelLabel = 'Suscripciones';

    protected static ?string $modelLabel = 'Suscripción';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Estado')
                    ->schema([
                        Infolists\Components\TextEntry::make('status')
                            ->label('Estado')
                            ->badge(),
                    ]),

                Infolists\Components\Section::make('Local y plan')
                    ->columns(2)
                    ->schema([
                        Infolists\Components\TextEntry::make('local.nombre_local')
                            ->label('Local'),
                        Infolists\Components\TextEntry::make('local.email')
                            ->label('Correo del local')
                            ->placeholder('—'),
                        Infolists\Components\TextEntry::make('plan.name')
                            ->label('Plan'),
                        Infolists\Components\TextEntry::make('plan.price')
                            ->label('Precio')
                            ->formatStateUsing(fn ($record) => number_format($record->plan->price, 2) . ' ' . $record->plan->currency),
                    ]),

                Infolists\Components\Section::make('Período vigente')
                    ->columns(2)
                    ->schema([
                        Infolists\Components\TextEntry::make('current_period_start')
                            ->label('Inicio de período')
                            ->date('d/m/Y'),
                        Infolists\Components\TextEntry::make('current_period_end')
                            ->label('Fin de período')
                            ->date('d/m/Y'),
                    ]),

                Infolists\Components\Section::make('Registro')
                    ->columns(2)
                    ->schema([
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Fecha de alta')
                            ->dateTime('d/m/Y H:i'),
                        Infolists\Components\TextEntry::make('updated_at')
                            ->label('Última actualización')
                            ->dateTime('d/m/Y H:i'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('local.nombre_local')
                    ->label('Local')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('plan.name')
                    ->label('Plan')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('current_period_start')
                    ->label('Inicio período')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('current_period_end')
                    ->label('Fin período')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('payments_count')
                    ->label('Pagos')
                    ->getStateUsing(fn (Subscription $record): int => $record->payments()->count())
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Alta')
                    ->dateTime('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options(SubscriptionStatus::class),

                Tables\Filters\SelectFilter::make('plan_id')
                    ->label('Plan')
                    ->relationship('plan', 'name'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Ver'),

                Tables\Actions\Action::make('activar')
                    ->label('Activar manualmente')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Activar suscripción')
                    ->modalDescription('¿Confirmás que el pago de este período fue recibido por otro medio? Se activará la suscripción sin pasar por Mercado Pago.')
                    ->modalSubmitActionLabel('Sí, activar')
                    ->visible(fn (Subscription $record) => ! $record->isActive())
                    ->action(function (Subscription $record): void {
                        $record->update([
                            'status'               => SubscriptionStatus::ACTIVE,
                            'current_period_start' => Carbon::now()->startOfMonth(),
                            'current_period_end'   => Carbon::now()->endOfMonth(),
                        ]);

                        Notification::make()
                            ->title('Suscripción activada')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\Action::make('marcar_pendiente')
                    ->label('Marcar como pendiente')
                    ->icon('heroicon-o-clock')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Marcar suscripción como pendiente de pago')
                    ->modalDescription('El local dejará de poder operar hasta completar el pago de este período.')
                    ->modalSubmitActionLabel('Sí, marcar como pendiente')
                    ->visible(fn (Subscription $record) => $record->isActive())
                    ->action(function (Subscription $record): void {
                        $record->update(['status' => SubscriptionStatus::PENDING_PAYMENT]);

                        Notification::make()
                            ->title('Suscripción marcada como pendiente')
                            ->warning()
                            ->send();
                    }),
            ])
            ->bulkActions([])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\PaymentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSubscriptions::route('/'),
            'view'  => Pages\ViewSubscription::route('/{record}'),
        ];
    }
}
