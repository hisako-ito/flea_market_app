<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $guarded = [
        'id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function getPaymentMethodLabelAttribute()
    {
        return match ($this->payment_method) {
            1 => 'コンビニ払い',
            2 => 'カード払い',
        };
    }
}
