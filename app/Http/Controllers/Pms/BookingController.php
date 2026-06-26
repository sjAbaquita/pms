<?php

namespace App\Http\Controllers\Pms;

use App\Http\Controllers\Controller;
use App\Http\Requests\Booking\AvailabilitySearchRequest;
use App\Models\Reservation;
use App\Models\RoomType;
use App\Services\AvailabilityService;
use App\Services\PricingService;
use Inertia\Inertia;
use Inertia\Response;

class BookingController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('booking/Index', [
            'roomTypes' => RoomType::query()->with('amenities')->where('is_active', true)->orderBy('base_rate')->get(),
        ]);
    }

    public function search(AvailabilitySearchRequest $request, AvailabilityService $availability, PricingService $pricing): Response
    {
        $data = $request->validated();
        $rooms = $availability->availableRooms($data['check_in'], $data['check_out'], (int) $data['guests'])
            ->map(function ($room) use ($data, $pricing) {
                return [
                    'id' => $room->id,
                    'number' => $room->number,
                    'floor' => $room->floor,
                    'status' => $room->status->value,
                    'room_type' => $room->roomType,
                    'quote' => $pricing->quote($room, $data['check_in'], $data['check_out'], (int) $data['guests']),
                ];
            })
            ->values();

        return Inertia::render('booking/Available', [
            'filters' => $data,
            'rooms' => $rooms,
        ]);
    }

    public function confirmation(Reservation $reservation): Response
    {
        return Inertia::render('booking/Confirmation', [
            'reservation' => $reservation->load(['guest', 'reservationRooms.room.roomType']),
        ]);
    }
}
