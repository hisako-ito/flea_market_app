<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = ['name','price','description','image','condition'];

    public function categories()
    {
        return $this->belongsToMany(Category::class,'category_item','item_id','category_id',);
    }
}
