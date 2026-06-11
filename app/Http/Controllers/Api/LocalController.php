<?php

namespace App\Http\Controllers\Api;

use App\Enums\LocalStatus;
use App\Enums\TransactionStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\EspecialidadResource;
use App\Http\Resources\LocalResource;
use App\Http\Resources\MasistaResource;
use App\Http\Resources\BookingResource;
use App\Models\Local;
use App\Models\Therapists\Booking;
use App\Models\Therapists\States\Booking\BookingPendingPayment;
use App\Services\LocalAvailabilityService;
use App\Services\MercadoPagoService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LocalController extends Controller
{
    public function __construct(
        private readonly LocalAvailabilityService $localAvailabilityService,
    ) {}

    /** GET /api/v1/locals */
    public function index(): JsonResponse
    {
        $locals = Local::where('status', LocalStatus::ACTIVE)
            ->withAvg('reviews', 'local_score')
            ->withCount('reviews')
            ->orderBy('nombre_local')
            ->get();

        return response()->json(['data' => LocalResource::collection($locals)]);
    }

    /** GET /api/v1/locals/{local}/especialidades */
    public function especialidades(Local $local): JsonResponse
    {
        return response()->json([
            'data' => EspecialidadResource::collection($local->especialidades),
        ]);
    }

    /** GET /api/v1/locals/{local}/slots?days_ahead=14 */
    public function slots(Request $request, Local $local): JsonResponse
    {
        $daysAhead = $request->integer('days_ahead', 14);

        $slots = $this->localAvailabilityService->getAvailableSlots($local, $daysAhead);

        return response()->json(['data' => $slots]);
    }

    /** GET /api/v1/locals/{local}/masajistas?date=YYYY-MM-DD&time=HH:MM&especialidad_id= */
    public function masajistas(Request $request, Local $local): JsonResponse
    {
        $request->validate([
            'date' => ['required', 'date_format:Y-m-d'],
            'time' => ['required', 'date_format:H:i'],
            'especialidad_id' => ['nullable', 'integer', 'exists:especialidades,id'],
        ]);

        $masajistas = $this->localAvailabilityService->getAvailableMasajistas(
            local: $local,
            date: $request->string('date'),
            startTime: $request->string('time'),
            especialidadId: $request->integer('especialidad_id') ?: null,
        );

        return response()->json(['data' => MasistaResource::collection($masajistas)]);
    }

    /** POST /api/v1/locals/{local}/bookings  (auth:sanctum) */
    public function createBooking(Request $request, Local $local): JsonResponse
    {
        $data = $request->validate([
            'masajista_id'    => ['required', 'integer', 'exists:therapists,id'],
            // Especialidad obligatoria: todo turno requiere especialidad con precio
            'especialidad_id' => ['required', 'integer', 'exists:especialidades,id'],
            'date'            => ['required', 'date_format:Y-m-d', 'after_or_equal:today'],
            'start_time'      => ['required', 'date_format:H:i'],
            'notes'           => ['nullable', 'string', 'max:1000'],
        ]);

        $endTime = Carbon::createFromFormat('H:i', $data['start_time'])
            ->addMinutes($local->slot_duration_minutes)
            ->format('H:i');

        // Snapshot del precio al momento de la reserva
        $especialidad = \App\Models\Especialidad::find($data['especialidad_id']);
        $price        = $especialidad?->price;

        if ($price === null) {
            return response()->json([
                'message' => 'La especialidad seleccionada no tiene precio configurado.',
            ], 422);
        }

        return DB::transaction(function () use ($data, $local, $request, $endTime, $price, $especialidad) {
            $booking = Booking::create([
                'therapist_id'    => $data['masajista_id'],
                'local_id'        => $local->id,
                'especialidad_id' => $data['especialidad_id'],
                'user_id'         => $request->user()->id,
                'date'            => $data['date'],
                'start_time'      => $data['start_time'],
                'end_time'        => $endTime,
                'price'           => $price,
                'notes'           => $data['notes'] ?? null,
                // Siempre pending_payment — queda confirmed al aprobarse el pago
                'state'           => BookingPendingPayment::$name,
            ]);

            $booking->load('therapist.user', 'especialidad');

            // Crear la transacción vinculada a la reserva
            $booking->transaction()->create([
                'client_id'    => $request->user()->id,
                'therapist_id' => $booking->therapist->user->id,
                'amount'       => $price,
                'currency'     => 'ARS',
                'status'       => TransactionStatus::PENDING,
                'description'  => 'Seña — ' . ($especialidad->nombre ?? 'BodyFix'),
            ]);

            // Crear preference de Mercado Pago
            $mpService = app(MercadoPagoService::class);
            $initPoint = $mpService->createPreference($booking);

            return response()->json([
                'data'    => BookingResource::make($booking),
                'payment' => [
                    'requires_payment' => true,
                    'init_point'       => $initPoint,
                ],
            ], 201);
        });
    }
}
