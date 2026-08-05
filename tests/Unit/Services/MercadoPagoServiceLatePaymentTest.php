<?php

namespace Tests\Unit\Services;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\TransactionStatus;
use App\Models\Especialidad;
use App\Models\Local;
use App\Models\Payments\Payment;
use App\Models\Therapists\Booking;
use App\Models\Therapists\States\Booking\BookingCancelled;
use App\Models\Therapists\States\Booking\BookingConfirmed;
use App\Models\Therapists\States\Booking\BookingExpired;
use App\Models\Therapists\Therapist;
use App\Models\User;
use App\Notifications\UserNotification;
use App\Services\MercadoPagoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class MercadoPagoServiceLatePaymentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['mercadopago.access_token' => 'TEST-fake-access-token']);
    }

    private function makeBookingWithPayment(string $bookingState): array
    {
        $therapistUser = User::factory()->create();
        $client = User::factory()->create();

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
            'user_id' => $therapistUser->id,
            'nombre' => 'Terapeuta de Prueba',
            'activo' => true,
        ]);

        $especialidad = Especialidad::create(['local_id' => $local->id, 'nombre' => 'Masajes', 'price' => 100]);

        $booking = Booking::create([
            'therapist_id' => $therapist->id,
            'local_id' => $local->id,
            'especialidad_id' => $especialidad->id,
            'user_id' => $client->id,
            'date' => now()->addDay()->toDateString(),
            'start_time' => '10:00',
            'end_time' => '11:00',
            'price' => 100,
            'state' => $bookingState,
        ]);

        $transaction = $booking->transaction()->create([
            'client_id' => $client->id,
            'therapist_id' => $therapistUser->id,
            'amount' => 100,
            'currency' => 'ARS',
            'status' => TransactionStatus::PENDING,
            'description' => 'Seña de prueba',
        ]);

        $payment = Payment::create([
            'user_id' => $client->id,
            'transaction_id' => $transaction->id,
            'currency' => 'ARS',
            'payment_status' => PaymentStatus::PENDING,
            'payment_method' => PaymentMethod::MERCADO_PAGO,
            'amount' => 100,
            'external_id' => 'mp-test-123',
            'preference_id' => 'pref-test-123',
        ]);

        return [$booking, $payment, $therapistUser, $client];
    }

    public function test_pago_tardio_reactiva_turno_cancelado_y_avisa_al_masajista()
    {
        Notification::fake();

        [$booking, $payment, $therapistUser, $client] = $this->makeBookingWithPayment(BookingCancelled::$name);

        app(MercadoPagoService::class)->processPayment($booking, $payment);

        $booking->refresh();
        $this->assertInstanceOf(BookingConfirmed::class, $booking->state);
        $this->assertEquals(PaymentStatus::APPROVED, $payment->fresh()->payment_status);

        Notification::assertSentTo($therapistUser, UserNotification::class, fn ($n) => str_contains($n->title, 'reactivado'));
        Notification::assertSentTo($client, UserNotification::class, fn ($n) => str_contains($n->title, 'Pago Aprobado'));
    }

    public function test_pago_tardio_reactiva_turno_expirado()
    {
        Notification::fake();

        [$booking, $payment] = $this->makeBookingWithPayment(BookingExpired::$name);

        app(MercadoPagoService::class)->processPayment($booking, $payment);

        $booking->refresh();
        $this->assertInstanceOf(BookingConfirmed::class, $booking->state);
    }

    public function test_webhook_duplicado_en_turno_ya_confirmado_no_falla_ni_reavisa()
    {
        Notification::fake();

        [$booking, $payment, $therapistUser] = $this->makeBookingWithPayment(BookingConfirmed::$name);

        app(MercadoPagoService::class)->processPayment($booking, $payment);

        $booking->refresh();
        $this->assertInstanceOf(BookingConfirmed::class, $booking->state);

        Notification::assertNotSentTo($therapistUser, UserNotification::class, fn ($n) => str_contains($n->title, 'reactivado'));
    }
}
