<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\Room;
use App\Models\RoomType;
use Carbon\CarbonInterface;
use Carbon\CarbonPeriod;
use Illuminate\Support\Carbon;
use InvalidArgumentException;

class PricingService
{
    /**
     * @return array{subtotal: float, discount_total: float, tax_total: float, grand_total: float, nights: int, nightly_rate: float, room_total: float, extra_guest_total: float, extra_mattress_total: float, coupon?: array<string, mixed>}
     */
    public function quote(Room $room, string $checkIn, string $checkOut, int $guests, int $extraMattresses = 0, ?string $couponCode = null): array
    {
        $room->loadMissing('roomType.seasonRates');
        $roomType = $room->roomType;
        $nights = (int) Carbon::parse($checkIn)->diffInDays(Carbon::parse($checkOut));

        if ($nights < 1) {
            throw new InvalidArgumentException('Check-out must be after check-in.');
        }

        $extraGuests = max(0, $guests - $roomType->base_capacity);

        if ($extraGuests > 0 && ! $roomType->allows_extra_guests) {
            throw new InvalidArgumentException('This room type does not allow extra guests.');
        }

        if ($guests > $roomType->max_guests) {
            throw new InvalidArgumentException('Guest count exceeds this room type capacity.');
        }

        if ($extraMattresses > $roomType->max_extra_mattresses) {
            throw new InvalidArgumentException('Extra mattresses exceed this room type limit.');
        }

        $roomTotal = 0.0;
        foreach (CarbonPeriod::create($checkIn, $checkOut)->excludeEndDate() as $night) {
            $roomTotal += $this->rateForNight($roomType, $night);
        }

        $extraGuestTotal = (float) $roomType->extra_guest_rate * $extraGuests * $nights;
        $extraMattressTotal = (float) $roomType->extra_mattress_rate * $extraMattresses * $nights;
        $subtotal = $roomTotal + $extraGuestTotal + $extraMattressTotal;
        $discountTotal = $this->discountForCoupon($couponCode, $subtotal);
        $grandTotal = max(0, $subtotal - $discountTotal);

        return [
            'subtotal' => round($subtotal, 2),
            'discount_total' => round($discountTotal, 2),
            'tax_total' => 0.0,
            'grand_total' => round($grandTotal, 2),
            'nights' => $nights,
            'nightly_rate' => round($roomTotal / $nights, 2),
            'room_total' => round($roomTotal, 2),
            'extra_guest_total' => round($extraGuestTotal, 2),
            'extra_mattress_total' => round($extraMattressTotal, 2),
        ];
    }

    private function rateForNight(RoomType $roomType, CarbonInterface $night): float
    {
        $season = $roomType->seasonRates
            ->where('is_active', true)
            ->first(fn ($rate) => $rate->starts_at->lte($night) && $rate->ends_at->gte($night));

        if ($season?->rate !== null) {
            return (float) $season->rate;
        }

        if ($season?->multiplier !== null) {
            return (float) $roomType->base_rate * (float) $season->multiplier;
        }

        return (float) $roomType->base_rate;
    }

    private function discountForCoupon(?string $couponCode, float $subtotal): float
    {
        if ($couponCode === null || $couponCode === '') {
            return 0.0;
        }

        $coupon = Coupon::query()->where('code', strtoupper($couponCode))->first();

        if (! $coupon?->isRedeemable()) {
            return 0.0;
        }

        if ($coupon->type === 'percentage') {
            return min($subtotal, $subtotal * ((float) $coupon->value / 100));
        }

        return min($subtotal, (float) $coupon->value);
    }
}
