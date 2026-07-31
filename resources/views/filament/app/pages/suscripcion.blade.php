<x-filament-panels::page>

    @php
        $subscription = $this->getSubscription();
        $plan = $this->getPlan();
    @endphp

    <x-filament::section>
        <x-slot name="heading">
            {{ $plan?->name ?? 'Plan Base' }}
        </x-slot>

        <div class="space-y-4">
            <p class="text-sm text-gray-500">
                Precio: <strong>${{ number_format($plan?->price ?? 0, 0, ',', '.') }} {{ $plan?->currency ?? 'ARS' }}</strong> / mes
            </p>

            @if ($subscription)
                <div>
                    Estado:
                    <x-filament::badge :color="$subscription->status->getColor()">
                        {{ $subscription->status->getLabel() }}
                    </x-filament::badge>
                </div>

                @if ($subscription->isActive())
                    <p class="text-sm text-gray-500">
                        Tu suscripción está activa hasta el {{ $subscription->current_period_end->format('d/m/Y') }}.
                    </p>
                @else
                    <p class="text-sm text-gray-500">
                        Tu local no puede operar ni mostrar anuncios hasta completar el pago de este mes.
                    </p>
                @endif
            @else
                <p class="text-sm text-gray-500">
                    Tu local todavía no tiene una suscripción activa. Suscribite para empezar a operar.
                </p>
            @endif

            @unless ($subscription?->isActive())
                <x-filament::button wire:click="pay" size="lg">
                    Pagar suscripción
                </x-filament::button>
            @endunless
        </div>
    </x-filament::section>

</x-filament-panels::page>
