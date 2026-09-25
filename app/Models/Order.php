<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'customer_id',
        'farmer_profile_id',
        'market_id',
        'total_amount',
        'pickup_slot',
        'pickup_date',
        'qr_code_hash',
        'pickup_token',
        'status',
        'payment_method',
        'payment_status',
        'notes',
        'co2_saved_total',
        'accepted_at',
        'ready_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'total_amount' => 'float',
            'co2_saved_total' => 'float',
            'pickup_date' => 'date',
            'accepted_at' => 'datetime',
            'ready_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function farmerProfile(): BelongsTo
    {
        return $this->belongsTo(FarmerProfile::class, 'farmer_profile_id');
    }

    public function market(): BelongsTo
    {
        return $this->belongsTo(Market::class, 'market_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class, 'order_id');
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'placed' => 'badge-placed',
            'accepted' => 'badge-accepted',
            'ready' => 'badge-ready',
            'completed' => 'badge-completed',
            'cancelled' => 'badge-cancelled',
            default => 'badge-secondary',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'placed' => 'Order Placed',
            'accepted' => 'Confirmed by Farmer',
            'ready' => 'Ready for Pickup',
            'completed' => 'Collected & Settled',
            'cancelled' => 'Cancelled',
            default => ucfirst($this->status),
        };
    }
}
