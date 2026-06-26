<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'name', 'slug', 'description', 'base_capacity', 'max_guests', 'base_rate',
    'max_extra_mattresses', 'extra_guest_rate', 'extra_mattress_rate',
    'allows_extra_guests', 'is_active', 'gallery',
])]
class RoomType extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'base_rate' => 'decimal:2',
            'extra_guest_rate' => 'decimal:2',
            'extra_mattress_rate' => 'decimal:2',
            'allows_extra_guests' => 'boolean',
            'is_active' => 'boolean',
            'gallery' => 'array',
        ];
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }

    public function amenities(): BelongsToMany
    {
        return $this->belongsToMany(Amenity::class);
    }

    public function seasonRates(): HasMany
    {
        return $this->hasMany(SeasonRate::class);
    }
}
