<?php

namespace Tests\Feature\Filament;

use App\Enums\Role;
use App\Models\Local;
use App\Models\Subscriptions\Plan;
use App\Models\Subscriptions\Subscription;
use App\Models\Therapists\Therapist;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocalSuspensionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);
    }

    private function createLocal(string $status, ?User $owner = null): Local
    {
        $owner ??= User::factory()->create(['must_change_password' => false]);

        return Local::create([
            'nombre_local' => 'Spa de Prueba',
            'direccion' => 'Calle Falsa 123',
            'cuit' => '20304050607',
            'telefono' => '1122334455',
            'email' => 'spa@test.com',
            'status' => $status,
            'slot_duration_minutes' => 60,
            'user_id' => $owner->id,
        ]);
    }

    private function activateSubscription(Local $local): void
    {
        $plan = Plan::create([
            'name' => 'Básico',
            'price' => 1000,
            'currency' => 'ARS',
            'is_active' => true,
        ]);

        Subscription::create([
            'local_id' => $local->id,
            'plan_id' => $plan->id,
            'status' => 'active',
            'current_period_start' => now()->subDay(),
            'current_period_end' => now()->addMonth(),
        ]);
    }

    private function createOwnerWithLocal(string $status, bool $withActiveSubscription = false): User
    {
        $owner = User::factory()->create(['must_change_password' => false]);
        $owner->assignRole(Role::SPA_OWNER);

        $local = $this->createLocal($status, $owner);

        if ($withActiveSubscription) {
            $this->activateSubscription($local);
        }

        return $owner;
    }

    private function createCollaboratorTherapist(Local $local): User
    {
        $therapistUser = User::factory()->create(['must_change_password' => false]);
        $therapistUser->assignRole(Role::MASSAGE_THERAPIST);

        Therapist::create([
            'type' => 'MassageTherapist',
            'local_id' => $local->id,
            'user_id' => $therapistUser->id,
            'nombre' => 'Masajista Colaborador',
            'activo' => true,
        ]);

        return $therapistUser;
    }

    public function test_dueno_de_local_suspendido_es_redirigido_al_mensaje_de_cuenta_suspendida()
    {
        $owner = $this->createOwnerWithLocal('suspended');

        $response = $this->actingAs($owner)->get('/app/local-configuracion');

        $response->assertRedirect('/app/cuenta-suspendida');
    }

    public function test_dueno_de_local_suspendido_puede_ver_la_pagina_de_cuenta_suspendida()
    {
        $owner = $this->createOwnerWithLocal('suspended');

        $response = $this->actingAs($owner)->get('/app/cuenta-suspendida');

        $response->assertOk();
        $response->assertSee('Cuenta suspendida');
    }

    public function test_dueno_de_local_activo_no_es_redirigido()
    {
        $owner = $this->createOwnerWithLocal('active', withActiveSubscription: true);

        $response = $this->actingAs($owner)->get('/app/local-configuracion');

        $response->assertOk();
    }

    public function test_masajista_colaborador_de_local_suspendido_es_redirigido_al_mensaje_de_cuenta_suspendida()
    {
        $local = $this->createLocal('suspended');
        $therapistUser = $this->createCollaboratorTherapist($local);

        $response = $this->actingAs($therapistUser)->get('/app/mis-turnos');

        $response->assertRedirect('/app/cuenta-suspendida');
    }

    public function test_masajista_colaborador_de_local_suspendido_puede_ver_la_pagina_de_cuenta_suspendida()
    {
        $local = $this->createLocal('suspended');
        $therapistUser = $this->createCollaboratorTherapist($local);

        $response = $this->actingAs($therapistUser)->get('/app/cuenta-suspendida');

        $response->assertOk();
        $response->assertSee('Cuenta suspendida');
    }

    public function test_masajista_colaborador_de_local_activo_no_es_redirigido()
    {
        $local = $this->createLocal('active');
        $this->activateSubscription($local);
        $therapistUser = $this->createCollaboratorTherapist($local);

        $response = $this->actingAs($therapistUser)->get('/app/mis-turnos');

        $response->assertOk();
    }

    public function test_masajista_independiente_sin_local_no_se_ve_afectado()
    {
        $therapistUser = User::factory()->create(['must_change_password' => false]);
        $therapistUser->assignRole(Role::MASSAGE_THERAPIST);

        Therapist::create([
            'type' => 'MassageTherapist',
            'user_id' => $therapistUser->id,
            'certificate_file' => 'certificados/test.pdf',
            'certificate_file_name' => 'certificado',
        ]);

        $response = $this->actingAs($therapistUser)->get('/app/mis-turnos');

        $response->assertOk();
    }
}
