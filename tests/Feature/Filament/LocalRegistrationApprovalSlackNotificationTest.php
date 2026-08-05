<?php

namespace Tests\Feature\Filament;

use App\Enums\LocalRegistrationStatus;
use App\Enums\Role;
use App\Filament\Admin\Resources\LocalRegistrationResource\Pages\ListLocalRegistrations;
use App\Models\LocalRegistration;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use Tests\TestCase;

class LocalRegistrationApprovalSlackNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_aprobar_solicitud_de_local_notifica_credenciales_a_slack()
    {
        config(['services.slack.accounts_webhook_url' => 'https://hooks.slack.com/services/test']);
        Http::fake();

        $this->seed(RoleSeeder::class);

        $admin = User::factory()->create(['must_change_password' => false]);
        $admin->assignRole(Role::ADMIN);

        $registration = LocalRegistration::create([
            'nombre' => 'Dueño',
            'apellido' => 'De Prueba',
            'nombre_local' => 'Local Nuevo',
            'direccion' => 'Calle Falsa 123',
            'cuit' => '20304050607',
            'email' => 'nuevo-local@example.com',
            'telefono' => '1122334455',
            'localidad' => 'CABA',
            'latitude' => -34.6,
            'longitude' => -58.4,
            'status' => LocalRegistrationStatus::PENDING,
        ]);

        $this->actingAs($admin);
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        Livewire::test(ListLocalRegistrations::class)
            ->callTableAction('aprobar', $registration);

        Http::assertSent(function ($request) {
            return $request->url() === 'https://hooks.slack.com/services/test'
                && str_contains($request['text'], 'local')
                && str_contains($request['text'], 'nuevo-local@example.com');
        });
    }
}
