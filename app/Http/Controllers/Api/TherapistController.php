<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Therapists\StoreTherapistRequest;
use App\Models\Therapists\FactoryTherapist;
use App\Services\TherapistManagementService;
use Illuminate\Http\Request;

class TherapistController extends Controller
{
    public function __construct(
        private readonly TherapistManagementService $therapistManagementService,
    ) {}
    
    public function store(StoreTherapistRequest $request)
    {   
        $request->validated();

        $therapist = $this->therapistManagementService->create($request);
        return response()->json($therapist, 201);
    }

    public function all()
    {
        return $this->therapistManagementService->getAll();
    }

    public function allTherapistsTags()
    {
        return $this->therapistManagementService->getAllTherapistsTags();
    }

    public function getAllMassageTherapists()
    {
        return $this->therapistManagementService->getAllMassageTherapists();
    }
}
