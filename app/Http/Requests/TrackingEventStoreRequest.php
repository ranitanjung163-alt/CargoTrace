<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TrackingEventStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && ($this->user()->isAdmin() || $this->user()->isOperator());
    }

    public function rules(): array
    {
        return [
            'event_type' => ['required', 'string', 'max:50'],
            'event_time' => ['required', 'date'],
            'location_id' => ['nullable', 'exists:locations,id'],
            'location_name' => ['required', 'string', 'max:150'],
            'latitude' => ['nullable', 'numeric', 'between:-90.0,90.0'],
            'longitude' => ['nullable', 'numeric', 'between:-180.0,180.0'],
            'status_after_event' => ['nullable', 'string', 'in:CREATED,BOOKED,PICKED_UP,IN_TRANSIT,DELIVERED'],
            'description' => ['required', 'string', 'max:1000'],
            'is_exception' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'latitude.between' => 'Worldwide latitude must be between -90.0 and 90.0 degrees.',
            'longitude.between' => 'Worldwide longitude must be between -180.0 and 180.0 degrees.',
        ];
    }
}

