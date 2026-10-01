<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'tgl_kerjasama',
        'alamat',
        'latitude',
        'longitude',
        'penanggung_jawab',
    ];

    protected $casts = [
        'tgl_kerjasama' => 'date',
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    public function getHasCoordinatesAttribute(): bool
    {
        return !is_null($this->latitude) && !is_null($this->longitude) && ($this->latitude != 0 || $this->longitude != 0);
    }

    public function getGoogleMapsUrlAttribute(): string
    {
        if ($this->has_coordinates) {
            return "https://www.google.com/maps/dir/?api=1&destination={$this->latitude},{$this->longitude}";
        }
        if ($this->alamat) {
            return "https://www.google.com/maps/search/?api=1&query=" . urlencode($this->name . ' ' . $this->alamat);
        }
        return "https://www.google.com/maps/search/?api=1&query=" . urlencode($this->name);
    }

    public function getWazeUrlAttribute(): ?string
    {
        if ($this->has_coordinates) {
            return "https://waze.com/ul?ll={$this->latitude},{$this->longitude}&navigate=yes";
        }
        return null;
    }

    public function coffeePrices()
    {
        return $this->hasMany(StoreCoffeePrice::class);
    }

    public function stockBatches()
    {
        return $this->hasMany(StockBatch::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function financeReports()
    {
        return $this->hasMany(FinanceReport::class);
    }

    public function stockOpnames()
    {
        return $this->hasMany(StockOpname::class);
    }
}
