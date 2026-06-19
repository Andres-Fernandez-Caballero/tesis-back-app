<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BookingCreateRequest;
use App\Http\Resources\BookingResource;
use App\Models\Therapists\Booking;
use App\Models\Therapists\States\Booking\BookingCancelled;
use App\Models\Therapists\States\Booking\BookingPendingPayment;
use App\Services\BookingService;
use App\Services\MercadoPagoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public function __construct(
        private readonly BookingService $service
    ) {}

    public function store(BookingCreateRequest $request)
    {
        $data = $request->validated();

        $client = $request->user();
        $booking = $this->service->createBooking($data, $client);

        return response()->json(BookingResource::make($booking), 201);
    }

    public function index(Request $request)
    {
        $pagination = $request->query('pagination', 10);
        $bookings = $this->service->getAllBookings((int)$pagination);
        return BookingResource::collection($bookings);
    }

    public function showClientBookings(Request $request)
    {
        $client = $request->user();
        $pagination = $request->query('pagination', 10);
        $bookings = $this->service->getClientBookings($client, (int)$pagination);
        return BookingResource::collection($bookings);
    }

    /**
     * GET /api/v1/bookings/{booking}/payment-status
     *
     * Polling endpoint: el frontend lo llama periódicamente después de que el
     * usuario vuelve del checkout externo de Mercado Pago.
     * Si la reserva sigue en pending_payment, consulta la API de MP directamente.
     * Esto permite que funcione incluso sin webhook configurado (desarrollo local).
     */
    public function paymentStatus(Request $request, Booking $booking): JsonResponse
    {
        $stateName = $booking->getRawOriginal('state');


        return response()->json([
            'state'          => $stateName,
            'payment_status' => $booking->transaction?->hasApprovedPayment() ? 'approved' : 'pending',
        ]);
    }

    /**
     * POST /api/v1/bookings/{booking}/cancel-pending
     *
     * Cancela una reserva en estado pending_payment.
     * Se llama cuando el usuario cierra el checkout de Mercado Pago sin completar el pago.
     */
    public function cancelPending(Request $request, Booking $booking): JsonResponse
    {
        if ($booking->user_id !== $request->user()->id) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        if ($booking->getRawOriginal('state') !== BookingPendingPayment::$name) {
            return response()->json(['message' => 'La reserva no está pendiente de pago.'], 422);
        }

        $booking->state->transitionTo(BookingCancelled::class);

        return response()->json(['message' => 'Reserva cancelada.']);
    }
}
