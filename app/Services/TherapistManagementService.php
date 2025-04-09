<?php

namespace App\Services;

use App\Http\Requests\Therapists\StoreTherapistRequest;
use App\Models\Therapists\FactoryTherapist;
use App\Models\Therapists\Therapist;
use App\Repositories\TherapistRepository;

class TherapistManagementService
{
    public function __construct(
        protected readonly TherapistRepository $therapistRepository,
        protected readonly FileStorageService $fileStorageService,
    ){}

    public function create(StoreTherapistRequest $request): Therapist
    {
        try{
            $certificate_file =$this->fileStorageService->storeFile(
                $request->file('certificate_file'),
                'certificates'
            );

            $data = [
                ...$request->validated(),
                'certificate_file' => $certificate_file,
            ];

            return $this->therapistRepository->create(
                FactoryTherapist::make($data)
            );
        } catch (\Exception $e) {
            $this->fileStorageService->deleteFile($certificate_file);
            throw new \RuntimeException('Failed to create therapist: ' . $e->getMessage());
        }
    }

    public function getAll()
    {
        return $this->therapistRepository->getAll();
    }

    public function getAllMassageTherapists()
    {
        return $this->therapistRepository->getAllMassageTherapists();
    }
}