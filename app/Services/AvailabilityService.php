<?php

namespace App\Services;

use App\Enums\ReservationStatus;
use App\Enums\RoomStatus;
use App\Models\Room;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Collection;

class AvailabilityService
{
    /** @return Collection<int, Room> */
    public function availableRooms(CarbonInterface|string $checkIn, CarbonInterface|string $checkOut, int $guests = 1): Collection
    {
        return Room::query()
            ->with(['roomType.amenities'])
            ->whereNotIn('status', [RoomStatus::Maintenance->value, RoomStatus::Occupied->value])
            ->whereHas('roomType', fn ($query) => $query
                ->where('is_active', true)
                ->where('max_guests', '>=', $guests))
            ->whereDoesntHave('reservationRooms.reservation', function ($query) use ($checkIn, $checkOut) {
                $query->whereNotIn('status', [ReservationStatus::Cancelled->value, ReservationStatus::NoShow->value])
                    ->where('check_in', '<', $checkOut)
                    ->where('check_out', '>', $checkIn);
            })
            ->orderBy('number')
            ->get();
    }

    public function roomIsAvailable(int $roomId, CarbonInterface|string $checkIn, CarbonInterface|string $checkOut): bool
    {
        return $this->availableRooms($checkIn, $checkOut, 1)->contains('id', $roomId);
    }
}
