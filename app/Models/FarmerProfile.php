<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FarmerProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'stall_name',
        'slug',
        'market_id',
        'stall_number',
        'bio',
        'lat',
        'long',
        'farm_origin',
        'certifications',
        'carbon_score_rating',
        'phone_contact',
        'is_approved',
    ];

    protected function casts(): array
    {
        return [
            'lat' => 'float',
            'long' => 'float',
            'carbon_score_rating' => 'float',
            'is_approved' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function market(): BelongsTo
    {
        return $this->belongsTo(Market::class, 'market_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'farmer_profile_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'farmer_profile_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'farmer_profile_id');
    }

    public function favoritedBy(): HasMany
    {
        return $this->hasMany(FavoriteFarmer::class, 'farmer_profile_id');
    }

    public function getAverageRatingAttribute(): float
    {
        return round($this->reviews()->avg('rating') ?? 4.9, 1);
    }
}
