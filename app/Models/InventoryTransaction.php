<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class InventoryTransaction extends Model
{
    protected $fillable = [
        'inventory_item_id',
        'user_id',
        'transaction_type',
        'quantity',
        'unit_cost',
        'total_cost',
        'reference_type',
        'reference_id',
        'notes',
        'transaction_date',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'transaction_date' => 'datetime',
    ];

    /**
     * Transaction type constants
     */
    const TYPE_IN = 'in';        // Stock received/purchased
    const TYPE_OUT = 'out';      // Stock used/sold
    const TYPE_ADJUSTMENT = 'adjustment'; // Manual adjustment
    const TYPE_LOSS = 'loss';    // Damaged/expired/lost
    const TYPE_TRANSFER = 'transfer'; // Transfer between locations

    /**
     * Get the inventory item
     */
    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItem::class);
    }

    /**
     * Get the user who performed the transaction
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the reference model (polymorphic)
     */
    public function reference()
    {
        return $this->morphTo();
    }

    /**
     * Scope for incoming transactions
     */
    public function scopeIncoming($query)
    {
        return $query->whereIn('transaction_type', [self::TYPE_IN, self::TYPE_ADJUSTMENT])
                    ->where('quantity', '>', 0);
    }

    /**
     * Scope for outgoing transactions
     */
    public function scopeOutgoing($query)
    {
        return $query->whereIn('transaction_type', [self::TYPE_OUT, self::TYPE_LOSS, self::TYPE_TRANSFER])
                    ->orWhere(function($q) {
                        $q->where('transaction_type', self::TYPE_ADJUSTMENT)
                          ->where('quantity', '<', 0);
                    });
    }

    /**
     * Scope for today's transactions
     */
    public function scopeToday($query)
    {
        return $query->whereDate('transaction_date', Carbon::today());
    }

    /**
     * Scope for this month's transactions
     */
    public function scopeThisMonth($query)
    {
        return $query->whereMonth('transaction_date', Carbon::now()->month)
                    ->whereYear('transaction_date', Carbon::now()->year);
    }

    /**
     * Get transaction type display name
     */
    public function getTransactionTypeDisplayAttribute()
    {
        return match($this->transaction_type) {
            self::TYPE_IN => 'Stock In',
            self::TYPE_OUT => 'Stock Out',
            self::TYPE_ADJUSTMENT => 'Adjustment',
            self::TYPE_LOSS => 'Loss/Damage',
            self::TYPE_TRANSFER => 'Transfer',
            default => 'Unknown'
        };
    }

    /**
     * Get transaction color for UI
     */
    public function getTransactionColorAttribute()
    {
        if ($this->quantity > 0) {
            return 'green'; // Incoming
        } else {
            return match($this->transaction_type) {
                self::TYPE_OUT => 'blue',
                self::TYPE_LOSS => 'red',
                self::TYPE_TRANSFER => 'purple',
                default => 'gray'
            };
        }
    }

    /**
     * Check if transaction increases stock
     */
    public function increasesStock(): bool
    {
        return $this->quantity > 0 && in_array($this->transaction_type, [
            self::TYPE_IN, 
            self::TYPE_ADJUSTMENT
        ]);
    }

    /**
     * Check if transaction decreases stock
     */
    public function decreasesStock(): bool
    {
        return $this->quantity < 0 || in_array($this->transaction_type, [
            self::TYPE_OUT,
            self::TYPE_LOSS,
            self::TYPE_TRANSFER
        ]);
    }
}