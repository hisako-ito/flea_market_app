<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use HasFactory;

    protected $fillable = ['name','brand','price','description','image','condition'];

    public function categories()
    {
        return $this->belongsToMany(Category::class,'category_item','item_id','category_id',);
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

    public function scopeKeywordSearch($query, $keyword)
    {
        if (!empty($keyword)) {
            $query->where('name', 'like', '%' . $keyword . '%');
        }
    }
}
