<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $guarded = [
        'id',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function review()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function isReviewed()
    {
        return $this->belongsTo(User::class, 'reviewed_id');
    }
}
