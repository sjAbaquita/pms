<?php

namespace App\Http\Controllers\Pms;

use App\Http\Controllers\Controller;
use App\Http\Requests\Booking\StoreReservationRequest;
use App\Models\Reservation;
use App\Services\ReservationService;
use Inertia\Inertia;
use Inertia\Response;

class ReservationController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('reservations/Index', [
            'reservations' => Reservation::query()
                ->with(['guest', 'reservationRooms.room.roomType'])
                ->latest()
                ->paginate(12)
                ->withQueryString(),
        ]);
    }

    public function store(StoreReservationRequest $request, ReservationService $reservations)
    {
        $reservation = $reservations->create($request->validated());

        return redirect()->route('booking.confirmation', $reservation)->with('success', "Reservation {$reservation->code} was created.");
    }
}
