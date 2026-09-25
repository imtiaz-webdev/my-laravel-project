<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'farmer_profile_id',
        'category_id',
        'name',
        'slug',
        'unit',
        'price',
        'zero_waste_price',
        'is_zero_waste_active',
        'stock_quantity',
        'harvest_date',
        'harvest_notes',
        'co2_savings_kg',
        'description',
        'badge_label',
        'status',
        'image_url',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'float',
            'zero_waste_price' => 'float',
            'is_zero_waste_active' => 'boolean',
            'stock_quantity' => 'integer',
            'harvest_date' => 'datetime',
            'co2_savings_kg' => 'float',
        ];
    }

    public function farmerProfile(): BelongsTo
    {
        return $this->belongsTo(FarmerProfile::class, 'farmer_profile_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'product_id');
    }

    public function getEffectivePriceAttribute(): float
    {
        if ($this->is_zero_waste_active && $this->zero_waste_price && $this->zero_waste_price > 0) {
            return (float) $this->zero_waste_price;
        }
        return (float) $this->price;
    }

    public function getDiscountPercentAttribute(): int
    {
        if ($this->is_zero_waste_active && $this->zero_waste_price && $this->price > 0) {
            $diff = $this->price - $this->zero_waste_price;
            return (int) round(($diff / $this->price) * 100);
        }
        return 0;
    }

    public function getHarvestStatusStringAttribute(): string
    {
        if (!$this->harvest_date) {
            return "Fresh Morning Harvest";
        }

        $now = Carbon::now();
        if ($this->harvest_date->isFuture()) {
            return "Scheduled harvest in " . $this->harvest_date->diffForHumans(null, true);
        }

        $diffHours = $this->harvest_date->diffInHours($now);
        if ($diffHours < 1) {
            return "Harvested just moments ago";
        } elseif ($diffHours < 24) {
            return "Harvested {$diffHours} hours ago";
        } else {
            $days = $this->harvest_date->diffInDays($now);
            return "Harvested {$days} days ago";
        }
    }

    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('status', '!=', 'sold_out')->where('stock_quantity', '>', 0);
    }

    public function scopeZeroWaste(Builder $query): Builder
    {
        return $query->where('is_zero_waste_active', true);
    }
}
