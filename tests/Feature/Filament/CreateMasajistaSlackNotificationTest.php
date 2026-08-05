<?php

namespace Tests\Feature\Filament;

use App\Enums\Role;
use App\Filament\App\Resources\MasajistasResource\Pages\CreateMasajista;
use App\Models\Especialidad;
use App\Models\Local;
use App\Models\Subscriptions\Plan;
use App\Models\Subscriptions\Subscription;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use Tests\TestCase;

class CreateMasajistaSlackNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_crear_masajista_notifica_credenciales_a_slack()
    {
        config(['services.slack.accounts_webhook_url' => 'https://hooks.slack.com/services/test']);
        Http::fake();

        $this->seed(RoleSeeder::class);

        $owner = User::factory()->create(['must_change_password' => false]);
        $owner->assignRole(Role::SPA_OWNER);

        $local = Local::create([
            'nombre_local' => 'Spa de Prueba',
            'direccion' => 'Calle Falsa 123',
            'cuit' => '20304050607',
            'telefono' => '1122334455',
            'email' => 'spa@test.com',
            'status' => 'active',
            'slot_duration_minutes' => 60,
            'user_id' => $owner->id,
        ]);

        $plan = Plan::create(['name' => 'Básico', 'price' => 1000, 'currency' => 'ARS', 'is_active' => true]);

        Subscription::create([
            'local_id' => $local->id,
            'plan_id' => $plan->id,
            'status' => 'active',
            'current_period_start' => now()->subDay(),
            'current_period_end' => now()->addMonth(),
        ]);

        Especialidad::create(['local_id' => $local->id, 'nombre' => 'Masajes', 'price' => 100]);

        $this->actingAs($owner);
        Filament::setCurrentPanel(Filament::getPanel('app'));

        Livewire::test(CreateMasajista::class)
            ->fillForm([
                'nombre' => 'Juan',
                'apellido' => 'Perez',
                'email' => 'juan-masajista@example.com',
                'dni' => '12345678',
                'telefono' => '1122334455',
                'activo' => true,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        Http::assertSent(function ($request) {
            return $request->url() === 'https://hooks.slack.com/services/test'
                && str_contains($request['text'], 'masajista')
                && str_contains($request['text'], 'juan-masajista@example.com');
        });
    }
}
