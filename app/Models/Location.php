<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'city',
        'state_province',
        'country',
        'country_code',
        'latitude',
        'longitude',
        'type',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
        ];
    }

    public function originShipments(): HasMany
    {
        return $this->hasMany(Shipment::class, 'origin_location_id');
    }

    public function destinationShipments(): HasMany
    {
        return $this->hasMany(Shipment::class, 'destination_location_id');
    }

    public function currentShipments(): HasMany
    {
        return $this->hasMany(Shipment::class, 'current_location_id');
    }

    public function trackingEvents(): HasMany
    {
        return $this->hasMany(TrackingEvent::class);
    }

    public function getDisplayNameAttribute(): string
    {
        $parts = [$this->city];
        if ($this->state_province) {
            $parts[] = $this->state_province;
        }
        $parts[] = $this->country_code;
        return implode(', ', $parts);
    }
}
