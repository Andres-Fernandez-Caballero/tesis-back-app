<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AvailabilityRequest;
use App\Http\Requests\Therapists\StoreTherapistRequest;
use App\Http\Resources\DayAvailabilityResource;
use App\Models\Therapists\Announcement;
use App\Models\Therapists\FactoryTherapist;
use App\Services\AvailabilityService;
use App\Services\TherapistManagementService;
use Illuminate\Http\Request;

class AvailabilityController extends Controller {
    public function __construct(
        private AvailabilityService $availabilityService
    ) {}

    /**
     * GET /api/therapists/{announcement}/availability
     */
    public function list(AvailabilityRequest $request, Announcement $announcement)
    {
        $validated = $request->validated();

        $daysAhead = $validated['days_ahead'] ?? 14;

        $availability = $this->availabilityService->getAvailableDays(
            therapistId: $announcement->therapist_id,
            serviceDurationMinutes: $announcement->duration_in_minutes,
            daysAhead: $daysAhead
        );

        return DayAvailabilityResource::collection($availability);
    }
}