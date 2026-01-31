<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    // 購入者
    public function user() {
        return $this->belongsTo(User::class);
    }

    // 購入された商品
    public function item() {
        return $this->belongsTo(Item::class);
    }

    protected $fillable = [
        'item_id',
        'user_id',
        'payment_method',
        'post_code',
        'address',
        'building_name',
    ];

    // 商品とのリレーション
    /*public function item()
    {
        return $this->belongsTo(Item::class);
    }*/
}
