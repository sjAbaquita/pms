<?php

namespace App\Http\Requests\Booking;

use Illuminate\Foundation\Http\FormRequest;

class StoreReservationRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'room_id' => ['required', 'exists:rooms,id'],
            'check_in' => ['required', 'date', 'after_or_equal:today'],
            'check_out' => ['required', 'date', 'after:check_in'],
            'adults' => ['required', 'integer', 'min:1', 'max:12'],
            'children' => ['nullable', 'integer', 'min:0', 'max:12'],
            'extra_mattresses' => ['nullable', 'integer', 'min:0', 'max:4'],
            'coupon_code' => ['nullable', 'string', 'max:40'],
            'special_requests' => ['nullable', 'string', 'max:2000'],
            'source' => ['nullable', 'string', 'max:40'],
            'guest.first_name' => ['required', 'string', 'max:120'],
            'guest.last_name' => ['required', 'string', 'max:120'],
            'guest.email' => ['nullable', 'email', 'max:160'],
            'guest.phone' => ['nullable', 'string', 'max:80'],
            'guest.address' => ['nullable', 'string', 'max:500'],
            'guest.notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
