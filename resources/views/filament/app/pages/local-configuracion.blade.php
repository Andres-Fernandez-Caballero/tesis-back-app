<x-filament-panels::page>

    {{-- Formulario: datos del local --}}
    <form wire:submit="save">
        {{ $this->form }}

        <div class="mt-6 flex justify-end">
            <x-filament::button type="submit" size="lg">
                Guardar cambios
            </x-filament::button>
        </div>
    </form>

    {{-- Formulario: cambiar contraseña --}}
    <form wire:submit="savePassword" class="mt-8">
        {{ $this->passwordForm }}

        <div class="mt-4 flex justify-end">
            <x-filament::button type="submit" size="lg" color="gray">
                Cambiar contraseña
            </x-filament::button>
        </div>
    </form>

    <x-filament-actions::modals />

</x-filament-panels::page>
