<?php

namespace App\Filament\App\Resources;

use App\Enums\Role;
use App\Filament\App\Resources\DisponibilidadResource\Pages;
use App\Models\Therapists\Availability;
use App\Models\Therapists\MassageTherapist;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class DisponibilidadResource extends Resource
{
    protected static ?string $model = Availability::class;

    protected static ?string $navigationIcon  = 'heroicon-o-calendar-days';
    protected static ?string $navigationLabel = 'Disponibilidad';
    protected static ?string $pluralModelLabel = 'Franjas horarias';
    protected static ?string $modelLabel      = 'Franja horaria';
    protected static ?int    $navigationSort  = 2;

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasRole(Role::SPA_OWNER) ?? false;
    }

    /**
     * Solo las disponibilidades de los masajistas del local autenticado.
     */
    public static function getEloquentQuery(): Builder
    {
        $therapistIds = auth()->user()?->local?->therapists()->pluck('id') ?? collect();

        return parent::getEloquentQuery()->whereIn('therapist_id', $therapistIds);
    }

    /**
     * Nombres de días para reutilizar.
     */
    public static function diasOptions(): array
    {
        return [
            1 => 'Lunes',
            2 => 'Martes',
            3 => 'Miércoles',
            4 => 'Jueves',
            5 => 'Viernes',
            6 => 'Sábado',
            7 => 'Domingo',
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('therapist_id')
                    ->label('Masajista')
                    ->options(function (): array {
                        return MassageTherapist::where('local_id', auth()->user()?->local?->id)
                            ->where('activo', true)
                            ->orderBy('nombre')
                            ->pluck('nombre', 'id')
                            ->toArray();
                    })
                    ->required()
                    ->searchable(),

                Forms\Components\CheckboxList::make('day_of_week')
                    ->label('Días de la semana')
                    ->options(self::diasOptions())
                    ->required()
                    ->columns(4)
                    ->gridDirection('row'),

                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\TimePicker::make('start_time')
                            ->label('Hora de inicio')
                            ->required()
                            ->seconds(false)
                            ->format('H:i')
                            ->native(false),

                        Forms\Components\TimePicker::make('end_time')
                            ->label('Hora de fin')
                            ->required()
                            ->seconds(false)
                            ->format('H:i')
                            ->native(false)
                            ->after('start_time'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('therapist.nombre')
                    ->label('Masajista')
                    ->sortable()
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('day_of_week')
                    ->label('Días')
                    ->state(function (Availability $record): string {
                        $dias = self::diasOptions();
                        $days = is_array($record->day_of_week)
                            ? $record->day_of_week
                            : json_decode($record->getRawOriginal('day_of_week') ?? '[]', true);
                        return collect($days)
                            ->map(fn ($d) => $dias[(int) $d] ?? null)
                            ->filter()
                            ->implode(', ') ?: '—';
                    })
                    ->wrap(),

                Tables\Columns\TextColumn::make('start_time')
                    ->label('Desde')
                    ->time('H:i'),

                Tables\Columns\TextColumn::make('end_time')
                    ->label('Hasta')
                    ->time('H:i'),

                Tables\Columns\TextColumn::make('slots_count')
                    ->label('Turnos/día')
                    ->state(function (Availability $record): string {
                        $local = auth()->user()?->local;
                        if (! $local || ! $local->slot_duration_minutes) {
                            return '—';
                        }
                        $start = \Carbon\Carbon::parse($record->start_time);
                        $end   = \Carbon\Carbon::parse($record->end_time);
                        $slots = (int) floor($start->diffInMinutes($end) / $local->slot_duration_minutes);
                        return $slots . ' turno' . ($slots !== 1 ? 's' : '');
                    })
                    ->color('gray'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('therapist_id')
                    ->label('Masajista')
                    ->options(function (): array {
                        return MassageTherapist::where('local_id', auth()->user()?->local?->id)
                            ->orderBy('nombre')
                            ->pluck('nombre', 'id')
                            ->toArray();
                    }),

                Tables\Filters\SelectFilter::make('day_of_week')
                    ->label('Día')
                    ->options(self::diasOptions())
                    ->query(fn ($query, array $data) => blank($data['value'])
                        ? $query
                        : $query->whereJsonContains('day_of_week', (int) $data['value'])
                    ),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->label('Agregar franja'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('day_of_week');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListDisponibilidad::route('/'),
            'create' => Pages\CreateDisponibilidad::route('/create'),
            'edit'   => Pages\EditDisponibilidad::route('/{record}/edit'),
        ];
    }
}
