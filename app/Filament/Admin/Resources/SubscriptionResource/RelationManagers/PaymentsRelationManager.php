<?php

namespace App\Filament\Admin\Resources\SubscriptionResource\RelationManagers;

use App\Enums\PaymentStatus;
use App\Models\Subscriptions\SubscriptionPayment;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class PaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'payments';

    protected static ?string $title = 'Historial de pagos';

    protected static ?string $label = 'pago';

    protected static ?string $pluralLabel = 'pagos';

    public function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('period_start')
                    ->label('Período')
                    ->formatStateUsing(fn (SubscriptionPayment $record) => $record->period_start->format('m/Y')),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Monto')
                    ->formatStateUsing(fn (SubscriptionPayment $record) => number_format($record->amount, 2) . ' ' . $record->currency),

                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->badge(),

                Tables\Columns\TextColumn::make('preference_id')
                    ->label('Preference ID')
                    ->copyable()
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('external_id')
                    ->label('Payment ID (MP)')
                    ->copyable()
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('paid_at')
                    ->label('Pagado el')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i'),
            ])
            ->headerActions([])
            ->actions([
                Tables\Actions\Action::make('marcar_aprobado')
                    ->label('Marcar aprobado')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Marcar pago como aprobado')
                    ->modalDescription('Usalo si el pago se confirmó por fuera de Mercado Pago (ej: transferencia).')
                    ->visible(fn (SubscriptionPayment $record) => $record->status === PaymentStatus::PENDING)
                    ->action(function (SubscriptionPayment $record): void {
                        $record->update([
                            'status'  => PaymentStatus::APPROVED,
                            'paid_at' => now(),
                        ]);

                        Notification::make()
                            ->title('Pago marcado como aprobado')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\Action::make('marcar_fallido')
                    ->label('Marcar fallido')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Marcar pago como fallido')
                    ->modalDescription('Libera el período para que se genere un nuevo intento de pago.')
                    ->visible(fn (SubscriptionPayment $record) => $record->status === PaymentStatus::PENDING)
                    ->action(function (SubscriptionPayment $record): void {
                        $record->update(['status' => PaymentStatus::FAILED]);

                        Notification::make()
                            ->title('Pago marcado como fallido')
                            ->warning()
                            ->send();
                    }),
            ])
            ->bulkActions([])
            ->defaultSort('created_at', 'desc');
    }
}
