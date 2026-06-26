<?php

namespace App\Http\Controllers\Pms;

use App\Enums\ReservationStatus;
use App\Enums\RoomStatus;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Reservation;
use App\Models\Room;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(): Response
    {
        $today = today();
        $monthExpression = DB::connection()->getDriverName() === 'sqlite'
            ? "strftime('%Y-%m', check_in)"
            : "DATE_FORMAT(check_in, '%Y-%m')";

        return Inertia::render('Dashboard', [
            'metrics' => [
                'arrivalsToday' => Reservation::query()->whereDate('check_in', $today)->whereIn('status', [ReservationStatus::Confirmed->value, ReservationStatus::Pending->value])->count(),
                'departuresToday' => Reservation::query()->whereDate('check_out', $today)->where('status', ReservationStatus::CheckedIn->value)->count(),
                'occupiedRooms' => Room::query()->where('status', RoomStatus::Occupied->value)->count(),
                'availableRooms' => Room::query()->where('status', RoomStatus::Available->value)->count(),
                'pendingReservations' => Reservation::query()->where('status', ReservationStatus::Pending->value)->count(),
                'revenueToday' => (float) Payment::query()->whereDate('created_at', $today)->sum('amount'),
                'revenueMonth' => (float) Payment::query()->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->sum('amount'),
            ],
            'upcomingReservations' => Reservation::query()
                ->with(['guest', 'reservationRooms.room.roomType'])
                ->whereDate('check_in', '>=', $today)
                ->orderBy('check_in')
                ->limit(8)
                ->get(),
            'monthlyBookings' => Reservation::query()
                ->selectRaw("{$monthExpression} as month, count(*) as total")
                ->where('check_in', '>=', now()->subMonths(6)->startOfMonth())
                ->groupBy('month')
                ->orderBy('month')
                ->get(),
        ]);
    }
}
