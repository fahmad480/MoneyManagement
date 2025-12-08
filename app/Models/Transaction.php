<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'bank_id',
        'card_id',
        'category_id',
        'type',
        'amount',
        'payment_method',
        'source',
        'to_bank_id',
        'reference_number',
        'description',
        'notes',
        'transaction_date',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'transaction_date' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }

    public function toBank()
    {
        return $this->belongsTo(Bank::class, 'to_bank_id');
    }

    public function card()
    {
        return $this->belongsTo(Card::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function charges()
    {
        return $this->hasMany(TransactionCharge::class);
    }

    public function getTotalAmountAttribute()
    {
        $chargesTotal = $this->charges()->sum('amount');
        return $this->amount + $chargesTotal;
    }
}
