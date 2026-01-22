<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BookingCreateRequest;
use App\Http\Resources\BookingResource;
use App\Services\BookingService;
use Illuminate\Http\Request;

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
        
        return response()->json($booking, 201);
    }

    public function index(Request $request)
    {
        $pagination = $request->query('pagination', 10);
        $bookings = $this->service->getAllBookings((int)$pagination);
        return response()->json($bookings);
    }

    public function showClientBookings(Request $request)
    {
        $client = $request->user();
        $pagination = $request->query('pagination', 10);
        $bookings = $this->service->getClientBookings($client, (int)$pagination);
        return BookingResource::collection($bookings);
    }
}
