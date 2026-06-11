<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Reviews\StoreReviewRequest;
use App\Http\Resources\ReviewResource;
use App\Models\Review;
use App\Models\Therapists\Booking;
use App\Models\Therapists\States\Booking\BookingCompleted;
use Illuminate\Http\JsonResponse;

class ReviewController extends Controller
{
    public function store(StoreReviewRequest $request, Booking $booking): JsonResponse
    {
        // Solo el dueño del turno puede calificarlo
        if ($booking->user_id !== $request->user()->id) {
            abort(403, 'No tenés permiso para calificar este turno.');
        }

        // Solo turnos finalizados
        if (! ($booking->state instanceof BookingCompleted)) {
            return response()->json(['message' => 'Solo se pueden calificar turnos finalizados.'], 422);
        }

        // Solo una calificación por turno
        if ($booking->review()->exists()) {
            return response()->json(['message' => 'Este turno ya fue calificado.'], 422);
        }

        $review = Review::create([
            'booking_id'      => $booking->id,
            'user_id'         => $request->user()->id,
            'local_id'        => $booking->local_id,
            'therapist_id'    => $booking->therapist_id,
            'local_score'     => $request->local_score,
            'therapist_score' => $request->therapist_score,
            'comment'         => $request->comment,
        ]);

        return response()->json(new ReviewResource($review), 201);
    }
}
