<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethodModel extends Model
{
    use HasFactory;

    protected $table = 'payment_methods';

    protected $fillable = [
        'method_name',
        'description',
        'api_endpoint',
        'icon',
        'merchant_id',
        'api_key',
        'api_secret',
        'sandbox_merchant_id',
        'sandbox_api_key',
        'sandbox_api_secret',
        'display_order',
        'fee_percentage',
        'fee_fixed',
        'status',
    ];

    protected $casts = [
        'fee_percentage' => 'decimal:2',
        'fee_fixed' => 'decimal:2',
        'display_order' => 'integer',
    ];

    /**
     * Scope to get only active payment methods
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to get payment methods ordered by display order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order', 'asc');
    }
}
