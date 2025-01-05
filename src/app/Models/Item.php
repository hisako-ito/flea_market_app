<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $guarded = [
        'id',
    ];

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_item', 'item_id', 'category_id',);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function checkCondition()
    {
        $condition_type = $this->condition;
        switch ($condition_type) {

            case "1":
                echo "良好";
                break;

            case "2":
                echo "目立った傷や汚れなし";
                break;

            case "3":
                echo "やや傷や汚れあり";
                break;

            case "4":
                echo "状態が悪い";
                break;
        }
    }

    public function favorites()
    {
        return $this->belongsToMany(User::class, 'favorites', 'item_id', 'user_id')->withTimestamps();
    }


    public function isFavoritedBy($user)
    {
        if (!$user) {
            return false;
        }

        return $this->favorites()->where('user_id', $user->id)->exists();
    }

    // public function comments()
    // {
    //     return $this->belongsToMany(User::class, 'comments', 'item_id', 'user_id')->withPivot('content');
    // }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
