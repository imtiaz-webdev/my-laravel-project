<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Market extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'address',
        'city',
        'latitude',
        'longitude',
        'operating_days',
        'timing',
        'description',
        'status',
        'stall_capacity',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
            'stall_capacity' => 'integer',
        ];
    }

    public function farmerProfiles(): HasMany
    {
        return $this->hasMany(FarmerProfile::class, 'market_id');
    }

    public function products(): HasManyThrough
    {
        return $this->hasManyThrough(Product::class, FarmerProfile::class, 'market_id', 'farmer_profile_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'market_id');
    }
}
