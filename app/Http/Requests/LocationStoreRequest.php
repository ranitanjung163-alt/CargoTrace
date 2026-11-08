<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LocationStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isAdmin();
    }

    public function rules(): array
    {
        $id = $this->route('location') ? $this->route('location')->id : null;

        return [
            'code' => ['required', 'string', 'max:30', 'unique:locations,code,' . $id],
            'name' => ['required', 'string', 'max:150'],
            'city' => ['required', 'string', 'max:100'],
            'state_province' => ['nullable', 'string', 'max:100'],
            'country' => ['required', 'string', 'max:100'],
            'country_code' => ['required', 'string', 'max:10'],
            'latitude' => ['required', 'numeric', 'between:-90.0,90.0'],
            'longitude' => ['required', 'numeric', 'between:-180.0,180.0'],
            'type' => ['required', 'string', 'in:PORT,AIRPORT,WAREHOUSE,DISTRIBUTION_CENTER,CUSTOMS'],
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

