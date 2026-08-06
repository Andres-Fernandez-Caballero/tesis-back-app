<?php

namespace Tests\Feature\Api;

use App\Models\Especialidad;
use App\Models\Local;
use App\Models\Therapists\Booking;
use App\Models\Therapists\States\Booking\BookingCancelled;
use App\Models\Therapists\States\Booking\BookingCompleted;
use App\Models\Therapists\States\Booking\BookingConfirmed;
use App\Models\Therapists\States\Booking\BookingPendingPayment;
use App\Models\Therapists\Therapist;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class BookingCancelTest extends TestCase
{
    use RefreshDatabase;

    private function createBooking(string $state, ?User $client = null): Booking
    {
        $client ??= User::factory()->create();
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

        $therapist = Therapist::create([
            'type' => 'MassageTherapist',
            'local_id' => $local->id,
            'nombre' => 'Terapeuta de Prueba',
            'activo' => true,
        ]);

        $especialidad = Especialidad::create(['local_id' => $local->id, 'nombre' => 'Masajes', 'price' => 100]);

        return Booking::create([
            'therapist_id' => $therapist->id,
            'local_id' => $local->id,
            'especialidad_id' => $especialidad->id,
            'user_id' => $client->id,
            'date' => now()->addDay()->toDateString(),
            'start_time' => '10:00',
            'end_time' => '11:00',
            'price' => 100,
            'state' => $state,
        ]);
    }

    public function test_el_cliente_puede_cancelar_un_turno_confirmado_sin_reembolso()
    {
        $client = User::factory()->create();
        $booking = $this->createBooking(BookingConfirmed::$name, $client);

        Sanctum::actingAs($client);

        $response = $this->postJson("/api/v1/bookings/{$booking->id}/cancel");

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'El turno sera cancelado pero el dinero no sera reembolsado',
        ]);

        $this->assertInstanceOf(BookingCancelled::class, $booking->fresh()->state);
    }

    public function test_el_cliente_puede_cancelar_un_turno_pendiente_de_pago()
    {
        $client = User::factory()->create();
        $booking = $this->createBooking(BookingPendingPayment::$name, $client);

        Sanctum::actingAs($client);

        $response = $this->postJson("/api/v1/bookings/{$booking->id}/cancel");

        $response->assertStatus(200);
        $this->assertInstanceOf(BookingCancelled::class, $booking->fresh()->state);
    }

    public function test_no_se_puede_cancelar_un_turno_ya_cancelado()
    {
        $client = User::factory()->create();
        $booking = $this->createBooking(BookingCancelled::$name, $client);

        Sanctum::actingAs($client);

        $response = $this->postJson("/api/v1/bookings/{$booking->id}/cancel");

        $response->assertStatus(422);
        $response->assertJson(['message' => 'El turno ya está cancelado.']);
    }

    public function test_no_se_puede_cancelar_un_turno_ya_finalizado()
    {
        $client = User::factory()->create();
        $booking = $this->createBooking(BookingCompleted::$name, $client);

        Sanctum::actingAs($client);

        $response = $this->postJson("/api/v1/bookings/{$booking->id}/cancel");

        $response->assertStatus(422);
        $response->assertJson(['message' => 'No se puede cancelar un turno ya finalizado.']);
        $this->assertInstanceOf(BookingCompleted::class, $booking->fresh()->state);
    }

    public function test_un_cliente_no_puede_cancelar_el_turno_de_otro()
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $booking = $this->createBooking(BookingConfirmed::$name, $owner);

        Sanctum::actingAs($intruder);

        $response = $this->postJson("/api/v1/bookings/{$booking->id}/cancel");

        $response->assertStatus(403);
        $this->assertInstanceOf(BookingConfirmed::class, $booking->fresh()->state);
    }

    public function test_requiere_autenticacion()
    {
        $booking = $this->createBooking(BookingConfirmed::$name);

        $response = $this->postJson("/api/v1/bookings/{$booking->id}/cancel");

        $response->assertStatus(401);
    }
}
