<?php

use App\Enums\ReservationStatus;
use App\Models\Amenity;
use App\Models\Room;
use App\Models\RoomType;
use App\Services\AvailabilityService;
use App\Services\PricingService;
use App\Services\ReservationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

function makeRoomForBooking(): Room
{
    $type = RoomType::query()->create([
        'name' => 'Standard Deluxe Ocean View',
        'slug' => 'standard-deluxe-ocean-view',
        'description' => 'Ocean view test room.',
        'base_capacity' => 2,
        'max_guests' => 4,
        'base_rate' => 4400,
        'allows_extra_guests' => true,
        'max_extra_mattresses' => 2,
        'extra_guest_rate' => 650,
        'extra_mattress_rate' => 900,
        'is_active' => true,
    ]);

    $type->amenities()->attach(Amenity::query()->create(['name' => 'Wifi']));

    return Room::query()->create([
        'room_type_id' => $type->id,
        'number' => '301',
        'floor' => '3',
    ]);
}

it('calculates room, extra guest, and mattress pricing from database rules', function () {
    $room = makeRoomForBooking();

    $quote = app(PricingService::class)->quote(
        room: $room,
        checkIn: now()->addDay()->toDateString(),
        checkOut: now()->addDays(3)->toDateString(),
        guests: 3,
        extraMattresses: 1,
    );

    expect($quote['nights'])->toBe(2)
        ->and($quote['room_total'])->toBe(8800.0)
        ->and($quote['extra_guest_total'])->toBe(1300.0)
        ->and($quote['extra_mattress_total'])->toBe(1800.0)
        ->and($quote['grand_total'])->toBe(11900.0);
});

it('prevents overlapping reservations for the same room', function () {
    $room = makeRoomForBooking();
    $service = app(ReservationService::class);
    $checkIn = now()->addDay()->toDateString();
    $checkOut = now()->addDays(3)->toDateString();

    $service->create([
        'room_id' => $room->id,
        'check_in' => $checkIn,
        'check_out' => $checkOut,
        'adults' => 2,
        'children' => 0,
        'guest' => [
            'first_name' => 'Ana',
            'last_name' => 'Reyes',
            'email' => 'ana@example.test',
        ],
    ]);

    expect(app(AvailabilityService::class)->availableRooms($checkIn, $checkOut, 2))->toHaveCount(0);

    $service->create([
        'room_id' => $room->id,
        'check_in' => $checkIn,
        'check_out' => $checkOut,
        'adults' => 2,
        'children' => 0,
        'guest' => [
            'first_name' => 'Ben',
            'last_name' => 'Santos',
            'email' => 'ben@example.test',
        ],
    ]);
})->throws(ValidationException::class);

it('allows a new reservation after a cancelled overlapping booking', function () {
    $room = makeRoomForBooking();
    $service = app(ReservationService::class);
    $checkIn = now()->addDay()->toDateString();
    $checkOut = now()->addDays(3)->toDateString();

    $reservation = $service->create([
        'room_id' => $room->id,
        'check_in' => $checkIn,
        'check_out' => $checkOut,
        'adults' => 2,
        'children' => 0,
        'guest' => [
            'first_name' => 'Cara',
            'last_name' => 'Lim',
            'email' => 'cara@example.test',
        ],
    ]);

    $reservation->update(['status' => ReservationStatus::Cancelled]);

    $next = $service->create([
        'room_id' => $room->id,
        'check_in' => $checkIn,
        'check_out' => $checkOut,
        'adults' => 2,
        'children' => 0,
        'guest' => [
            'first_name' => 'Dina',
            'last_name' => 'Cruz',
            'email' => 'dina@example.test',
        ],
    ]);

    expect($next->code)->toStartWith('BSR-');
});
