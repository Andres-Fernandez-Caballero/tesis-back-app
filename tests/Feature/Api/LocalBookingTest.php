<?php

namespace Tests\Feature\Api;

use App\Models\Especialidad;
use App\Models\Local;
use App\Models\Subscriptions\Plan;
use App\Models\Subscriptions\Subscription;
use App\Models\Therapists\Booking;
use App\Models\Therapists\States\Booking\BookingConfirmed;
use App\Models\Therapists\Therapist;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class LocalBookingTest extends TestCase
{
    use RefreshDatabase;

    private function createLocalWithActiveSubscription(): Local
    {
        $owner = User::factory()->create();

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

        return $local;
    }

    private function createTherapist(Local $local): Therapist
    {
        return Therapist::create([
            'type' => 'MassageTherapist',
            'local_id' => $local->id,
            'nombre' => 'Terapeuta de Prueba',
            'activo' => true,
        ]);
    }

    private function createEspecialidad(Local $local, string $nombre = 'Masaje descontracturante'): Especialidad
    {
        return Especialidad::create([
            'local_id' => $local->id,
            'nombre' => $nombre,
            'price' => 100,
        ]);
    }

    public function test_no_se_puede_reservar_dos_veces_la_misma_fecha_y_hora_con_el_mismo_terapeuta()
    {
        $local = $this->createLocalWithActiveSubscription();
        $therapist = $this->createTherapist($local);
        $especialidad = $this->createEspecialidad($local);
        $therapist->especialidades()->attach($especialidad->id);

        $client = User::factory()->create();
        Sanctum::actingAs($client);

        $payload = [
            'masajista_id' => $therapist->id,
            'especialidad_id' => $especialidad->id,
            'date' => now()->addDay()->toDateString(),
            'start_time' => '10:00',
        ];

        $first = $this->postJson("/api/v1/locals/{$local->id}/bookings", $payload);
        $first->assertStatus(201);

        $second = $this->postJson("/api/v1/locals/{$local->id}/bookings", $payload);
        $second->assertStatus(422);

        $this->assertSame(
            1,
            Booking::where('therapist_id', $therapist->id)
                ->whereDate('date', $payload['date'])
                ->where('start_time', $payload['start_time'])
                ->count()
        );
    }

    public function test_no_se_puede_reservar_un_terapeuta_en_una_especialidad_que_no_posee()
    {
        $local = $this->createLocalWithActiveSubscription();
        $therapist = $this->createTherapist($local);
        $especialidadPropia = $this->createEspecialidad($local, 'Masaje descontracturante');
        $especialidadAjena = $this->createEspecialidad($local, 'Drenaje linfático');

        $therapist->especialidades()->attach($especialidadPropia->id);
        // Nota: $especialidadAjena NO se asocia al terapeuta.

        $client = User::factory()->create();
        Sanctum::actingAs($client);

        $response = $this->postJson("/api/v1/locals/{$local->id}/bookings", [
            'masajista_id' => $therapist->id,
            'especialidad_id' => $especialidadAjena->id,
            'date' => now()->addDay()->toDateString(),
            'start_time' => '10:00',
        ]);

        $response->assertStatus(422);

        $this->assertDatabaseMissing('bookings', [
            'therapist_id' => $therapist->id,
            'especialidad_id' => $especialidadAjena->id,
        ]);
    }
}
