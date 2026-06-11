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

class MiCalendarioWidget extends FullCalendarWidget
{
    protected static ?string $heading = 'Mis turnos';

    protected static bool $isDiscovered = false;

    public Model | string | null $model = Booking::class;

    public static function canView(): bool
    {
        return auth()->user()?->hasRole(Role::MASSAGE_THERAPIST) ?? false;
    }

    /**
     * El masajista no puede crear reservas manualmente — se oculta el botón "New".
     */
    protected function headerActions(): array
    {
        return [];
    }

    public function fetchEvents(array $info): array
    {
        $therapistId = auth()->user()?->therapist?->id;

        if (! $therapistId) {
            return [];
        }

        // Usar la columna `date` (DATE) para el filtrado, no `start_time` (TIME)
        $from = Carbon::parse($info['start'])->toDateString();
        $to   = Carbon::parse($info['end'])->toDateString();

        return Booking::with(['user', 'especialidad'])
            ->where('therapist_id', $therapistId)
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
        return Booking::with(['user', 'especialidad'])->findOrFail($key);
    }

    public function getFormSchema(): array
    {
        return [
            Placeholder::make('cliente')
                ->label('Cliente')
                ->content(fn (?Booking $record) => $record
                    ? trim(($record->user?->name ?? '') . ' ' . ($record->user?->last_name ?? ''))
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
     * modalActions() se cachea al arrancar Livewire.
     * Usamos ->visible() con closures para evaluación diferida.
     */
    protected function modalActions(): array
    {
        return [
            // Finalizar — solo Confirmed → Completed
            Action::make('finalizar')
                ->label('Finalizar turno')
                ->color('success')
                ->icon('heroicon-o-check-badge')
                ->requiresConfirmation()
                ->modalHeading('Finalizar turno')
                ->modalDescription('¿Marcar este turno como finalizado?')
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

                    Notification::make()->title('Turno finalizado')->success()->send();
                }),
        ];
    }

    // ─── Helpers ────────────────────────────────────────────────────────────────

    private function buildEventTitle(Booking $booking): string
    {
        $client = $booking->user?->name ?? 'Cliente';
        $esp    = $booking->especialidad?->nombre ?? '';

        return $esp ? "{$client} ({$esp})" : $client;
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
