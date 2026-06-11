<x-filament-panels::page>

    {{-- Sección: Foto de perfil --}}
    <form wire:submit="saveFoto">
        {{ $this->fotoForm }}

        <div class="mt-4 flex justify-end">
            <x-filament::button type="submit" size="lg">
                Guardar foto
            </x-filament::button>
        </div>
    </form>

    {{-- Sección: Cambiar contraseña --}}
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
