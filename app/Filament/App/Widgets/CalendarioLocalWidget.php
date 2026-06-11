<?php

namespace App\Filament\App\Widgets;

use App\Enums\Role;
use App\Models\Therapists\Booking;
use App\Models\Therapists\States\Booking\BookingCancelled;
use App\Models\Therapists\States\Booking\BookingCompleted;
use App\Models\Therapists\States\Booking\BookingConfirmed;
use App\Models\Therapists\States\Booking\BookingPending;
use App\Services\Notifications\NotificationService;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\Placeholder;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Saade\FilamentFullCalendar\Actions\ViewAction;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;

class CalendarioLocalWidget extends FullCalendarWidget
{
    protected static ?string $heading = 'Calendario de turnos';

    public Model | string | null $model = Booking::class;

    public static function canView(): bool
    {
        return auth()->user()?->hasRole(Role::SPA_OWNER) ?? false;
    }

    /**
     * Sin botón "New" en el header — las reservas las crean los clientes desde la app.
     */
    protected function headerActions(): array
    {
        return [];
    }

    public function fetchEvents(array $info): array
    {
        $localId = auth()->user()?->local?->id;

        if (! $localId) {
            return [];
        }

        // La columna `date` es DATE y `start_time`/`end_time` son TIME.
        // FullCalendar envía $info['start'] y $info['end'] como ISO datetimes.
        $from = Carbon::parse($info['start'])->toDateString();
        $to   = Carbon::parse($info['end'])->toDateString();

        return Booking::with(['therapist', 'user', 'especialidad'])
            ->whereHas('therapist', fn ($q) => $q->where('local_id', $localId))
            ->whereDate('date', '>=', $from)
            ->whereDate('date', '<=', $to)
            ->get()
            ->map(fn (Booking $booking) => [
                'id'    => $booking->id,
                'title' => $this->buildEventTitle($booking),
                // Combinar date (DATE) + start_time (TIME) en un datetime ISO
                'start' => $booking->date . 'T' . $booking->start_time,
                'end'   => $booking->date . 'T' . $booking->end_time,
                'color' => $this->stateColor($booking->state::class),
            ])
            ->toArray();
    }

    public function resolveRecord(int | string $key): Model
    {
        return Booking::with(['therapist', 'user', 'especialidad'])->findOrFail($key);
    }

    public function getFormSchema(): array
    {
        return [
            Placeholder::make('cliente')
                ->label('Cliente')
                ->content(fn (?Booking $record) => $record
                    ? trim(($record->user?->name ?? '') . ' ' . ($record->user?->last_name ?? ''))
                    : '—'),

            Placeholder::make('masajista')
                ->label('Masajista')
                ->content(fn (?Booking $record) => $record
                    ? trim(($record->therapist?->nombre ?? '') . ' ' . ($record->therapist?->apellido ?? ''))
                    : '—'),

            Placeholder::make('fecha')
                ->label('Fecha')
                ->content(fn (?Booking $record) => $record?->date
                    ? Carbon::parse($record->date)->translatedFormat('l d/m/Y')
                    : '—'),

            Placeholder::make('horario')
                ->label('Horario')
                ->content(fn (?Booking $record) => $record
                    ? (substr($record->start_time ?? '', 0, 5) . ' – ' . substr($record->end_time ?? '', 0, 5))
                    : '—'),

            Placeholder::make('especialidad')
                ->label('Especialidad')
                ->content(fn (?Booking $record) => $record?->especialidad?->nombre ?? '—'),

            Placeholder::make('precio')
                ->label('Seña')
                ->content(fn (?Booking $record) => $record?->price !== null
                    ? '$ ' . number_format((float) $record->price, 2, ',', '.')
                    : '—'),

            Placeholder::make('state')
                ->label('Estado')
                ->content(fn (?Booking $record) => $record?->state?->label() ?? '—'),
        ];
    }

    protected function viewAction(): Action
    {
        return ViewAction::make()
            ->label('Ver turno')
            ->modalHeading('Detalle del turno');
    }

    /**
     * modalActions() se cachea al arrancar Livewire (antes de que el usuario haga click).
     * Usamos ->visible() con closures para evaluación diferida.
     */
    protected function modalActions(): array
    {
        return [
            // Confirmar — Pending → Confirmed
            Action::make('confirmar')
                ->label('Confirmar')
                ->color('success')
                ->icon('heroicon-o-check-circle')
                ->requiresConfirmation()
                ->modalHeading('Confirmar turno')
                ->modalDescription('¿Confirmar este turno como aprobado?')
                ->visible(fn () => isset($this->record)
                    && $this->record instanceof Booking
                    && $this->record->state instanceof BookingPending)
                ->action(function (): void {
                    /** @var Booking $booking */
                    $booking = $this->record;
                    $booking->state->transitionTo(BookingConfirmed::class);
                    $this->refreshRecords();

                    if ($booking->user) {
                        $hora = substr($booking->start_time ?? '', 0, 5);
                        app(NotificationService::class)->send(
                            $booking->user,
                            'Turno confirmado ✓',
                            "Tu turno del {$booking->date} a las {$hora} fue confirmado.",
                        );
                    }

                    Notification::make()->title('Turno confirmado')->success()->send();
                }),

            // Completar — Confirmed → Completed
            Action::make('completar')
                ->label('Marcar como completado')
                ->color('success')
                ->icon('heroicon-o-check-badge')
                ->requiresConfirmation()
                ->modalHeading('Completar turno')
                ->modalDescription('¿Marcar este turno como completado?')
                ->visible(fn () => isset($this->record)
                    && $this->record instanceof Booking
                    && $this->record->state instanceof BookingConfirmed)
                ->action(function (): void {
                    /** @var Booking $booking */
                    $booking = $this->record;
                    $booking->state->transitionTo(BookingCompleted::class);
                    $this->refreshRecords();

                    if ($booking->user) {
                        app(NotificationService::class)->send(
                            $booking->user,
                            '¡Turno finalizado!',
                            '¿Qué te pareció el servicio? Dejá tu calificación.',
                            null,
                            ['screen' => 'review', 'bookingId' => $booking->id],
                        );
                    }

                    Notification::make()->title('Turno completado')->success()->send();
                }),

            // Cancelar — Pending/Confirmed → Cancelled
            Action::make('cancelar')
                ->label('Cancelar turno')
                ->color('danger')
                ->icon('heroicon-o-x-circle')
                ->requiresConfirmation()
                ->modalHeading('Cancelar turno')
                ->modalDescription('¿Estás seguro de que querés cancelar este turno?')
                ->visible(fn () => isset($this->record)
                    && $this->record instanceof Booking
                    && ($this->record->state instanceof BookingPending
                        || $this->record->state instanceof BookingConfirmed))
                ->action(function (): void {
                    /** @var Booking $booking */
                    $booking = $this->record;
                    $booking->state->transitionTo(BookingCancelled::class);
                    $this->refreshRecords();

                    if ($booking->user) {
                        app(NotificationService::class)->send(
                            $booking->user,
                            'Turno cancelado',
                            "Tu turno del {$booking->date} fue cancelado.",
                        );
                    }

                    Notification::make()->title('Turno cancelado')->warning()->send();
                }),
        ];
    }

    // ─── Helpers ────────────────────────────────────────────────────────────────

    private function buildEventTitle(Booking $booking): string
    {
        $client    = $booking->user?->name ?? 'Cliente';
        $masajista = $booking->therapist?->nombre ?? '';
        $esp       = $booking->especialidad?->nombre ?? '';

        $title = $client;
        if ($masajista) {
            $title .= " · {$masajista}";
        }
        if ($esp) {
            $title .= " ({$esp})";
        }

        return $title;
    }

    private function stateColor(string $stateClass): string
    {
        return match ($stateClass) {
            BookingPending::class   => '#F59E0B',
            BookingConfirmed::class => '#10B981',
            BookingCompleted::class => '#6B7280',
            BookingCancelled::class => '#EF4444',
            default                 => '#6B7280',
        };
    }
}
