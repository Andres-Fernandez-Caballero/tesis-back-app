<?php

namespace App\Filament\App\Resources;

use App\Enums\PaymentStatus;
use App\Enums\Role;
use App\Enums\TransactionStatus;
use App\Filament\App\Resources\SolicitudesPendientesResource\Pages;
use App\Models\Therapists\Booking;
use App\Models\Therapists\States\Booking\BookingCancelled;
use App\Models\Therapists\States\Booking\BookingCompleted;
use App\Models\Therapists\States\Booking\BookingConfirmed;
use App\Notifications\UserNotification;
use App\Services\MercadoPagoService;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SolicitudesPendientesResource extends Resource
{
    protected static ?string $model = Booking::class;

    protected static ?string $navigationIcon  = 'heroicon-o-calendar-days';
    protected static ?string $navigationLabel = 'Turnos';
    protected static ?string $pluralModelLabel = 'Turnos confirmados';
    protected static ?string $modelLabel      = 'Turno';
    protected static ?int    $navigationSort  = 0;

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasRole(Role::SPA_OWNER) ?? false;
    }

    /**
     * Turnos confirmados del local del dueño autenticado.
     */
    public static function getEloquentQuery(): Builder
    {
        $localId = auth()->user()?->local?->id;

        return parent::getEloquentQuery()
            ->with(['user', 'especialidad', 'therapist', 'transaction.payments'])
            ->where('state', 'confirmed')
            ->when(
                $localId,
                fn (Builder $q) => $q->where('local_id', $localId)
            )
            ->when(
                ! $localId,
                fn (Builder $q) => $q->whereRaw('0 = 1')
            );
    }

    // ─── Badge: cantidad de turnos confirmados próximos ─────────────────────────

    public static function getNavigationBadge(): ?string
    {
        $localId = auth()->user()?->local?->id;
        if (! $localId) {
            return null;
        }

        $count = Booking::where('state', 'confirmed')
            ->where('local_id', $localId)
            ->where('date', '>=', now()->toDateString())
            ->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'success';
    }

    public static function getNavigationBadgePollingInterval(): ?string
    {
        return '30s';
    }

    // ────────────────────────────────────────────────────────────────────────────

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
                    ->formatStateUsing(fn ($state, Booking $record) =>
                        trim(($record->user?->name ?? '') . ' ' . ($record->user?->last_name ?? ''))
                    )
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('masajista')
                    ->label('Masajista')
                    ->getStateUsing(fn (Booking $record): string =>
                        trim(($record->therapist?->nombre ?? '') . ' ' . ($record->therapist?->apellido ?? '')) ?: '—'
                    ),

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
                    ),
            ])
            ->defaultSort('date', 'asc')
            ->filters([
                Tables\Filters\Filter::make('proximos')
                    ->label('Solo próximos')
                    ->query(fn (Builder $query) => $query->where('date', '>=', now()->toDateString()))
                    ->default(),
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
                                ? "Tu turno del {$record->date} fue cancelado por el local. Tu seña será reembolsada en los próximos días hábiles."
                                : "Tu turno del {$record->date} fue cancelado por el local.";

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
            ->emptyStateHeading('Sin turnos confirmados')
            ->emptyStateDescription('Los turnos confirmados de tu local aparecerán aquí.')
            ->emptyStateIcon('heroicon-o-calendar-days');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSolicitudesPendientes::route('/'),
        ];
    }
}
