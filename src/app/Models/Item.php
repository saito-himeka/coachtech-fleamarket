<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    /**
     * 一括割り当て可能な属性
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'condition_id',
        'name',
        'description',
        'price',
        'brand',
        'image_paths',
    ];

    /**
     * 属性に対するキャスト
     *
     * @var array
     */
    protected $casts = [
        'image_paths' => 'array', // JSON型を自動的にPHPの配列として扱う
    ];

    // 出品者（ユーザー）とのリレーション
    public function user() {
        return $this->belongsTo(User::class);
    }

    // 商品の状態（Conditionモデル）とのリレーション
    public function condition() {
        return $this->belongsTo(Condition::class);
    }

    // カテゴリ（多対多）
    public function categories() {
        return $this->belongsToMany(Category::class, 'category_items');
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    // コメント（1対多）
    public function comments() {
        return $this->hasMany(Comment::class);
    }

    // 購入情報（1対1）
    public function purchase() {
        return $this->hasOne(Purchase::class);
    }


    public function isFavoritedBy($user): bool
    {
        if (!$user) {
            return false;
        }
        // 中間テーブル favorites に、このユーザーとこの商品の組み合わせがあるか確認
        return $this->favorites()->where('user_id', $user->id)->exists();
    }
}