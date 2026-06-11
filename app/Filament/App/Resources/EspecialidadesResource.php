<?php

namespace App\Filament\App\Resources;

use App\Enums\Role;
use App\Filament\App\Resources\EspecialidadesResource\Pages;
use App\Models\Especialidad;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class EspecialidadesResource extends Resource
{
    protected static ?string $model = Especialidad::class;

    protected static ?string $navigationIcon  = 'heroicon-o-tag';
    protected static ?string $navigationLabel = 'Especialidades';
    protected static ?string $pluralModelLabel = 'Especialidades de masajes';
    protected static ?string $modelLabel      = 'Especialidad';
    protected static ?int    $navigationSort  = 3;

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasRole(Role::SPA_OWNER) ?? false;
    }

    /**
     * Solo las especialidades del local del dueño autenticado.
     */
    public static function getEloquentQuery(): Builder
    {
        $localId = auth()->user()?->local?->id;

        return parent::getEloquentQuery()
            ->when($localId, fn (Builder $q) => $q->where('local_id', $localId))
            ->when(! $localId, fn (Builder $q) => $q->whereRaw('0 = 1'));
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre')
                    ->label('Nombre de la especialidad')
                    ->placeholder('Ej: Masaje deportivo, Reflexología, Shiatsu...')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('price')
                    ->label('Precio de seña ($)')
                    ->helperText('Monto que el cliente abona como seña al reservar el turno. Requerido para poder publicar la especialidad.')
                    ->numeric()
                    ->minValue(1)
                    ->step(0.01)
                    ->default(2000)
                    ->suffix('ARS')
                    ->required()
                    ->validationMessages([
                        'required' => 'El precio de la seña es obligatorio. Ingresá el monto que cobrás al cliente al reservar.',
                        'min'      => 'El precio debe ser mayor a cero.',
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->label('Especialidad')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('price')
                    ->label('Precio de seña')
                    ->money('ARS')
                    ->placeholder('—')
                    ->sortable(),

                Tables\Columns\TextColumn::make('therapists_count')
                    ->label('Masajistas')
                    ->counts('therapists')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Agregada')
                    ->dateTime('d/m/Y')
                    ->sortable(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Nueva especialidad')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['local_id'] = auth()->user()?->local?->id;
                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Editar'),

                Tables\Actions\DeleteAction::make()
                    ->label('Eliminar')
                    ->before(function (Especialidad $record, Tables\Actions\DeleteAction $action): void {
                        if ($record->therapists()->exists()) {
                            Notification::make()
                                ->warning()
                                ->title('No se puede eliminar')
                                ->body('Esta especialidad está asignada a uno o más masajistas. Desasignala primero.')
                                ->persistent()
                                ->send();
                            $action->cancel();
                        }
                    }),
            ])
            ->bulkActions([])
            ->defaultSort('nombre');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEspecialidades::route('/'),
        ];
    }
}
