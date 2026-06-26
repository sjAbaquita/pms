<?php

namespace App\Models;

use App\Enums\ReservationStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'code', 'guest_id', 'check_in', 'check_out', 'adults', 'children', 'extra_guests',
    'extra_mattresses', 'subtotal', 'discount_total', 'tax_total', 'grand_total',
    'status', 'source', 'coupon_code', 'special_requests',
])]
class Reservation extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'check_in' => 'date',
            'check_out' => 'date',
            'subtotal' => 'decimal:2',
            'discount_total' => 'decimal:2',
            'tax_total' => 'decimal:2',
            'grand_total' => 'decimal:2',
            'status' => ReservationStatus::class,
        ];
    }

    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class);
    }

    public function reservationRooms(): HasMany
    {
        return $this->hasMany(ReservationRoom::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
