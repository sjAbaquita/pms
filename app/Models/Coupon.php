<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['code', 'type', 'value', 'starts_at', 'ends_at', 'max_redemptions', 'redemptions', 'is_active'])]
class Coupon extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'value' => 'decimal:2',
            'starts_at' => 'date',
            'ends_at' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function isRedeemable(): bool
    {
        $today = now()->toDateString();

        return $this->is_active
            && ($this->starts_at === null || $this->starts_at->toDateString() <= $today)
            && ($this->ends_at === null || $this->ends_at->toDateString() >= $today)
            && ($this->max_redemptions === null || $this->redemptions < $this->max_redemptions);
    }
}
