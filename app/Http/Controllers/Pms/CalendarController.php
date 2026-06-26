<?php

namespace App\Http\Controllers\Pms;

use App\Enums\ReservationStatus;
use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Room;
use Inertia\Inertia;
use Inertia\Response;

class CalendarController extends Controller
{
    public function __invoke(): Response
    {
        return Inertia::render('calendar/Index', [
            'rooms' => Room::query()->with('roomType')->orderBy('number')->get(),
            'reservations' => Reservation::query()
                ->with(['guest', 'reservationRooms.room.roomType'])
                ->whereNotIn('status', [ReservationStatus::Cancelled, ReservationStatus::NoShow])
                ->where('check_out', '>=', today()->subDays(7))
                ->where('check_in', '<=', today()->addDays(45))
                ->orderBy('check_in')
                ->get(),
        ]);
    }
}
