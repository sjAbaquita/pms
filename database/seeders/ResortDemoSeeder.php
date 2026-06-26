<?php

namespace Database\Seeders;

use App\Models\Amenity;
use App\Models\Coupon;
use App\Models\PaymentMethod;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\SeasonRate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ResortDemoSeeder extends Seeder
{
    public function run(): void
    {
        $amenities = collect(['Wifi', 'TV', 'Mini Refrigerator', 'Aircon', 'Hot Shower', 'Breakfast', 'Ocean View'])
            ->mapWithKeys(fn (string $name) => [$name => Amenity::query()->firstOrCreate(['name' => $name])]);

        $roomTypes = [
            [
                'name' => 'Standard Room',
                'description' => 'Comfortable room for two guests with essential beach resort amenities.',
                'base_capacity' => 2,
                'max_guests' => 3,
                'base_rate' => 3300,
                'allows_extra_guests' => true,
                'max_extra_mattresses' => 0,
                'extra_guest_rate' => 650,
                'extra_mattress_rate' => 0,
                'amenities' => ['Wifi', 'TV', 'Aircon', 'Hot Shower'],
                'rooms' => ['101', '102', '103', '104'],
            ],
            [
                'name' => 'Standard Deluxe Ground Floor',
                'description' => 'Ground floor deluxe room with flexible mattress options.',
                'base_capacity' => 2,
                'max_guests' => 4,
                'base_rate' => 3850,
                'allows_extra_guests' => true,
                'max_extra_mattresses' => 2,
                'extra_guest_rate' => 650,
                'extra_mattress_rate' => 900,
                'amenities' => ['Wifi', 'TV', 'Mini Refrigerator', 'Aircon', 'Hot Shower'],
                'rooms' => ['201', '202', '203'],
            ],
            [
                'name' => 'Standard Deluxe Ocean View',
                'description' => 'Deluxe room with ocean-facing views and expanded amenities.',
                'base_capacity' => 2,
                'max_guests' => 4,
                'base_rate' => 4400,
                'allows_extra_guests' => true,
                'max_extra_mattresses' => 2,
                'extra_guest_rate' => 650,
                'extra_mattress_rate' => 900,
                'amenities' => ['Wifi', 'TV', 'Mini Refrigerator', 'Aircon', 'Hot Shower', 'Ocean View'],
                'rooms' => ['301', '302', '303'],
            ],
            [
                'name' => 'Family Room',
                'description' => 'Larger room for family stays with up to two extra mattresses.',
                'base_capacity' => 4,
                'max_guests' => 6,
                'base_rate' => 6380,
                'allows_extra_guests' => true,
                'max_extra_mattresses' => 2,
                'extra_guest_rate' => 650,
                'extra_mattress_rate' => 900,
                'amenities' => ['Wifi', 'TV', 'Mini Refrigerator', 'Aircon', 'Hot Shower', 'Breakfast'],
                'rooms' => ['401', '402'],
            ],
            [
                'name' => 'Villa Room',
                'description' => 'Premium villa stay for guests who want a quieter resort experience.',
                'base_capacity' => 2,
                'max_guests' => 4,
                'base_rate' => 6930,
                'allows_extra_guests' => true,
                'max_extra_mattresses' => 2,
                'extra_guest_rate' => 650,
                'extra_mattress_rate' => 900,
                'amenities' => ['Wifi', 'TV', 'Mini Refrigerator', 'Aircon', 'Hot Shower', 'Breakfast', 'Ocean View'],
                'rooms' => ['V1', 'V2'],
            ],
        ];

        foreach ($roomTypes as $definition) {
            $type = RoomType::query()->updateOrCreate(
                ['slug' => Str::slug($definition['name'])],
                collect($definition)->except(['amenities', 'rooms'])->toArray() + ['is_active' => true]
            );

            $type->amenities()->sync(collect($definition['amenities'])->map(fn ($name) => $amenities[$name]->id));

            foreach ($definition['rooms'] as $number) {
                Room::query()->firstOrCreate(
                    ['number' => $number],
                    ['room_type_id' => $type->id, 'floor' => str_starts_with($number, 'V') ? 'Villa' : substr($number, 0, 1)]
                );
            }
        }

        foreach (['Cash', 'Bank Transfer', 'GCash'] as $method) {
            PaymentMethod::query()->firstOrCreate(['name' => $method], ['requires_manual_verification' => $method !== 'Cash']);
        }

        Coupon::query()->firstOrCreate(['code' => 'BUGNAW500'], ['type' => 'fixed', 'value' => 500, 'is_active' => true]);

        $summerStart = now()->year.'-04-01';
        $summerEnd = now()->year.'-05-31';
        SeasonRate::query()->firstOrCreate(
            ['name' => 'Summer Peak', 'starts_at' => $summerStart, 'ends_at' => $summerEnd],
            ['multiplier' => 1.15, 'is_active' => true]
        );
    }
}
