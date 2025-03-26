<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
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

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}
