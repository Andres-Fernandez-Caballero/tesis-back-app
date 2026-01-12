<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AnnoucementResource;
use App\Services\AnnouncementService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class AnnoucementController extends Controller
{
    public function __construct(private AnnouncementService $announcementService)
    {
        //
    }
    /**
     * Display a listing of the resource.
     */
    public function index():JsonResponse
    {
        $announcements = $this->announcementService->getAll(10);
        return AnnoucementResource::collection($announcements)->response()  ;
    }

    public function destacates():JsonResponse
    {
        $announcements = $this->announcementService->getDestacates();
        return AnnoucementResource::collection($announcements)->response()  ;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $announcement = $this->announcementService->findById($id);
        return AnnoucementResource::make($announcement)->response();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
