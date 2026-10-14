<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'user_id',
        'type',
        'quantity',
        'reference_number',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    /**
     * Transaction types.
     */
    public const TYPE_STOCK_IN = 'stock_in';
    public const TYPE_STOCK_OUT = 'stock_out';
    public const TYPE_ADJUSTMENT = 'adjustment';

    public static function types(): array
    {
        return [
            self::TYPE_STOCK_IN => 'Stock In',
            self::TYPE_STOCK_OUT => 'Stock Out',
            self::TYPE_ADJUSTMENT => 'Adjustment',
        ];
    }

    /**
     * Relationships
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scopes
     */
    public function scopeStockIn($query)
    {
        return $query->where('type', self::TYPE_STOCK_IN);
    }

    public function scopeStockOut($query)
    {
        return $query->where('type', self::TYPE_STOCK_OUT);
    }

    public function scopeRecent($query)
    {
        return $query->orderByDesc('created_at');
    }

    /**
     * Accessors
     */
    public function getTypeLabelAttribute(): string
    {
        return self::types()[$this->type] ?? ucfirst(str_replace('_', ' ', $this->type));
    }

    public function getSignedQuantityAttribute(): int
    {
        if ($this->type === self::TYPE_STOCK_OUT) {
            return -abs($this->quantity);
        }
        // stock_in and adjustment: quantity can be positive or negative for adjustment
        if ($this->type === self::TYPE_ADJUSTMENT) {
            return $this->quantity; // already signed
        }
        return abs($this->quantity);
    }

    public function getBadgeClassAttribute(): string
    {
        return match ($this->type) {
            self::TYPE_STOCK_IN => 'bg-success',
            self::TYPE_STOCK_OUT => 'bg-danger',
            self::TYPE_ADJUSTMENT => 'bg-warning text-dark',
            default => 'bg-secondary',
        };
    }
}
