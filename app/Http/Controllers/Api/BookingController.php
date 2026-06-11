<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BookingCreateRequest;
use App\Http\Resources\BookingResource;
use App\Models\Therapists\Booking;
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
        // Solo el cliente dueño de la reserva puede consultarla
        if ($booking->user_id !== $request->user()->id) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        $stateName = $booking->getRawOriginal('state');

        // Si ya salió de pending_payment, devolver el estado actual directamente
        if ($stateName !== BookingPendingPayment::$name) {
            $isApproved = in_array($stateName, ['pending', 'confirmed', 'completed']);
            return response()->json([
                'state'          => $stateName,
                'payment_status' => $isApproved ? 'approved' : 'rejected',
            ]);
        }

        // Consultar MP para ver si hay un pago asociado a esta reserva
        $mpService = app(MercadoPagoService::class);
        $mpPayment = $mpService->getLatestPaymentByExternalRef($booking->id);

        if (! $mpPayment) {
            return response()->json([
                'state'          => 'pending_payment',
                'payment_status' => 'pending',
            ]);
        }

        $mpStatus = $mpPayment->status ?? 'pending';

        // Si el pago tiene un resultado definitivo, procesarlo
        if (in_array($mpStatus, ['approved', 'authorized', 'rejected', 'cancelled'])) {
            DB::transaction(function () use ($mpService, $booking, $mpPayment) {
                $booking->refresh(); // evitar race conditions
                if ($booking->getRawOriginal('state') === BookingPendingPayment::$name) {
                    $mpService->processPayment($booking, $mpPayment);
                }
            });

            $booking->refresh();
            $stateName = $booking->getRawOriginal('state');

            return response()->json([
                'state'          => $stateName,
                'payment_status' => in_array($mpStatus, ['approved', 'authorized']) ? 'approved' : 'rejected',
            ]);
        }

        return response()->json([
            'state'          => 'pending_payment',
            'payment_status' => 'pending',
        ]);
    }
}
