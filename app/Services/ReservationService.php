<?php

namespace App\Services;

use App\Enums\ReservationStatus;
use App\Models\Guest;
use App\Models\Reservation;
use App\Models\Room;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ReservationService
{
    public function __construct(
        private readonly AvailabilityService $availability,
        private readonly PricingService $pricing,
    ) {}

    /** @param array<string, mixed> $data */
    public function create(array $data): Reservation
    {
        return DB::transaction(function () use ($data) {
            $room = Room::query()->with('roomType')->lockForUpdate()->findOrFail($data['room_id']);
            $guests = (int) $data['adults'] + (int) ($data['children'] ?? 0);

            if (! $this->availability->roomIsAvailable($room->id, $data['check_in'], $data['check_out'])) {
                throw ValidationException::withMessages([
                    'room_id' => 'This room is no longer available for the selected dates.',
                ]);
            }

            $quote = $this->pricing->quote(
                $room,
                $data['check_in'],
                $data['check_out'],
                $guests,
                (int) ($data['extra_mattresses'] ?? 0),
                $data['coupon_code'] ?? null,
            );

            $guest = Guest::query()->create($data['guest']);

            $reservation = Reservation::query()->create([
                'code' => $this->nextCode(),
                'guest_id' => $guest->id,
                'check_in' => $data['check_in'],
                'check_out' => $data['check_out'],
                'adults' => $data['adults'],
                'children' => $data['children'] ?? 0,
                'extra_guests' => max(0, $guests - $room->roomType->base_capacity),
                'extra_mattresses' => $data['extra_mattresses'] ?? 0,
                'subtotal' => $quote['subtotal'],
                'discount_total' => $quote['discount_total'],
                'tax_total' => $quote['tax_total'],
                'grand_total' => $quote['grand_total'],
                'status' => ReservationStatus::Pending,
                'source' => $data['source'] ?? 'online',
                'coupon_code' => $data['coupon_code'] ?? null,
                'special_requests' => $data['special_requests'] ?? null,
            ]);

            $reservation->reservationRooms()->create([
                'room_id' => $room->id,
                'room_type_id' => $room->room_type_id,
                'nightly_rate' => $quote['nightly_rate'],
                'line_total' => $quote['grand_total'],
            ]);

            return $reservation->load(['guest', 'reservationRooms.room.roomType']);
        });
    }

    private function nextCode(): string
    {
        return 'BSR-'.now()->format('ymd').'-'.str_pad((string) (Reservation::query()->whereDate('created_at', today())->count() + 1), 4, '0', STR_PAD_LEFT);
    }
}
