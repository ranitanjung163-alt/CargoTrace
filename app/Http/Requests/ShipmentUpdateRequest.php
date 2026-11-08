<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShipmentUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && ($this->user()->isAdmin() || $this->user()->isOperator());
    }

    public function rules(): array
    {
        $shipmentId = $this->route('shipment') ? $this->route('shipment')->id : $this->route('id');

        return [
            'carrier_id' => ['sometimes', 'required', 'exists:carriers,id'],
            'cargo_type' => ['sometimes', 'required', 'string', 'max:100'],
            'weight' => ['sometimes', 'required', 'numeric', 'min:0.1'],
            'volume' => ['sometimes', 'required', 'numeric', 'min:0.01'],
            'expected_departure' => ['sometimes', 'required', 'date'],
            'expected_arrival' => ['sometimes', 'required', 'date'],
            'estimated_arrival' => ['sometimes', 'required', 'date'],
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }
}

