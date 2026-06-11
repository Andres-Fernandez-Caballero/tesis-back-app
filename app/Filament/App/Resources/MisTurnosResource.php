<?php

namespace App\Filament\App\Resources;

use App\Enums\PaymentStatus;
use App\Enums\Role;
use App\Enums\TransactionStatus;
use App\Filament\App\Resources\MisTurnosResource\Pages;
use App\Models\Therapists\Booking;
use App\Models\Therapists\States\Booking\BookingCancelled;
use App\Models\Therapists\States\Booking\BookingCompleted;
use App\Models\Therapists\States\Booking\BookingConfirmed;
use App\Models\Therapists\States\Booking\BookingExpired;
use App\Models\Therapists\States\Booking\BookingPending;
use App\Models\Therapists\States\Booking\BookingPendingPayment;
use App\Notifications\UserNotification;
use App\Services\MercadoPagoService;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MisTurnosResource extends Resource
{
    protected static ?string $model = Booking::class;

    protected static ?string $navigationIcon  = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'Mis turnos';
    protected static ?string $pluralModelLabel = 'Mis turnos';
    protected static ?string $modelLabel      = 'Turno';
    protected static ?int    $navigationSort  = 1;

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasRole(Role::MASSAGE_THERAPIST) ?? false;
    }

    /**
     * Solo los turnos del masajista autenticado.
     */
    public static function getEloquentQuery(): Builder
    {
        $therapistId = auth()->user()?->therapist?->id;

        return parent::getEloquentQuery()
            ->with(['user', 'especialidad', 'transaction.payments'])
            ->when(
                $therapistId,
                fn (Builder $q) => $q->where('therapist_id', $therapistId)
            )
            ->when(
                ! $therapistId,
                fn (Builder $q) => $q->whereRaw('0 = 1')
            );
    }

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Cliente')
                    ->formatStateUsing(fn ($state, Booking $record) => trim(($record->user?->name ?? '') . ' ' . ($record->user?->last_name ?? '')))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('date')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('start_time')
                    ->label('Horario')
                    ->formatStateUsing(fn ($state, Booking $record) =>
                        substr($state ?? '', 0, 5) . ' – ' . substr($record->end_time ?? '', 0, 5)
                    ),

                Tables\Columns\TextColumn::make('especialidad.nombre')
                    ->label('Especialidad')
                    ->placeholder('—')
                    ->badge(),

                Tables\Columns\TextColumn::make('price')
                    ->label('Seña')
                    ->formatStateUsing(fn ($state) => $state !== null
                        ? '$ ' . number_format((float) $state, 0, ',', '.')
                        : '—'
                    )
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('state')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match (get_class($state)) {
                        BookingPendingPayment::class => 'Pago pendiente',
                        BookingPending::class        => 'Pendiente',
                        BookingConfirmed::class      => 'Confirmado',
                        BookingCompleted::class      => 'Finalizado',
                        BookingCancelled::class      => 'Cancelado',
                        BookingExpired::class        => 'Expirado',
                        default                      => $state->label(),
                    })
                    ->color(fn ($state) => match (get_class($state)) {
                        BookingPendingPayment::class => 'info',
                        BookingPending::class        => 'warning',
                        BookingConfirmed::class      => 'success',
                        BookingCompleted::class      => 'gray',
                        BookingCancelled::class      => 'danger',
                        BookingExpired::class        => 'danger',
                        default                      => 'gray',
                    }),
            ])
            ->defaultSort('date', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('state')
                    ->label('Estado')
                    ->options([
                        'pending_payment' => 'Pago pendiente',
                        'pending'         => 'Pendiente',
                        'confirmed'       => 'Confirmado',
                        'completed'       => 'Finalizado',
                        'cancelled'       => 'Cancelado',
                        'expired'         => 'Expirado',
                    ])
                    ->query(fn (Builder $query, array $data) => $data['value']
                        ? $query->where('state', $data['value'])
                        : $query
                    ),
            ])
            ->actions([
                Tables\Actions\Action::make('finalizar')
                    ->label('Finalizar')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Finalizar turno')
                    ->modalDescription('¿Confirmar que este turno fue realizado?')
                    ->visible(fn (Booking $record) => $record->state instanceof BookingConfirmed)
                    ->action(function (Booking $record): void {
                        $record->state->transitionTo(BookingCompleted::class);

                        if ($record->user) {
                            $record->user->notify(new UserNotification(
                                title: '¡Turno finalizado! ⭐',
                                body:  '¿Qué te pareció el servicio? Tocá aquí para dejar tu calificación.',
                                data:  ['screen' => 'review', 'bookingId' => $record->id],
                            ));
                        }

                        Notification::make()->title('Turno finalizado')->success()->send();
                    }),

                Tables\Actions\Action::make('cancelar')
                    ->label('Cancelar')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Cancelar turno')
                    ->modalDescription(function (Booking $record): string {
                        $record->loadMissing('transaction.payments');
                        $tieneSeña = $record->transaction?->payments()
                            ->where('payment_status', PaymentStatus::APPROVED)
                            ->exists();

                        return $tieneSeña
                            ? 'El cliente abonó una seña. Si cancelás, se emitirá el reembolso automáticamente y se notificará al cliente. ¿Cancelar igualmente?'
                            : '¿Cancelar este turno? Se notificará al cliente.';
                    })
                    ->visible(fn (Booking $record) => $record->state instanceof BookingConfirmed)
                    ->action(function (Booking $record): void {
                        $record->loadMissing('transaction.payments');

                        $approvedPayment = $record->transaction?->payments()
                            ->where('payment_status', PaymentStatus::APPROVED)
                            ->latest()
                            ->first();

                        $refundOk = true;

                        if ($approvedPayment?->external_id) {
                            $mpService = app(MercadoPagoService::class);
                            $refundOk  = $mpService->refund($approvedPayment->external_id);

                            if ($refundOk) {
                                $approvedPayment->update(['payment_status' => PaymentStatus::REFUNDED]);
                                $record->transaction->update(['status' => TransactionStatus::REFUNDED]);
                            }
                        }

                        $record->state->transitionTo(BookingCancelled::class);

                        if ($record->user) {
                            $body = ($approvedPayment && $refundOk)
                                ? "Tu turno del {$record->date} fue cancelado por el masajista. Tu seña será reembolsada en los próximos días hábiles."
                                : "Tu turno del {$record->date} fue cancelado por el masajista.";

                            $record->user->notify(new UserNotification(
                                title: 'Turno cancelado',
                                body:  $body,
                            ));
                        }

                        $title = $approvedPayment
                            ? ($refundOk ? 'Turno cancelado y reembolso emitido' : 'Turno cancelado (el reembolso falló — revisá MP)')
                            : 'Turno cancelado';

                        Notification::make()
                            ->title($title)
                            ->color($refundOk ? 'warning' : 'danger')
                            ->send();
                    }),
            ])
            ->bulkActions([])
            ->emptyStateHeading('Sin turnos')
            ->emptyStateDescription('Cuando tengas turnos asignados, aparecerán aquí.')
            ->emptyStateIcon('heroicon-o-calendar');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMisTurnos::route('/'),
        ];
    }
}
