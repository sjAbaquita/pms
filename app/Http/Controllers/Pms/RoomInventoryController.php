<?php

namespace App\Http\Controllers\Pms;

use App\Http\Controllers\Controller;
use App\Models\RoomType;
use Inertia\Inertia;
use Inertia\Response;

class RoomInventoryController extends Controller
{
    public function __invoke(): Response
    {
        return Inertia::render('rooms/Index', [
            'roomTypes' => RoomType::query()
                ->with(['amenities', 'rooms' => fn ($query) => $query->orderBy('number')])
                ->withCount('rooms')
                ->orderBy('base_rate')
                ->get(),
        ]);
    }
}
