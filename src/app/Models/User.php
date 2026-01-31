<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * プロフィールとの1対1リレーション
     */
    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    /**
     * 出品した商品（1対多）
     */
    public function items()
    {
        return $this->hasMany(Item::class);
    }

    /**
     * 購入した履歴（1対多）
     */
    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    /**
     * お気に入り登録（1対多）
     */
    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    /**
     * お気に入りした「商品」一覧を直接取得する（追加：多対多）
     * これがあると $user->favoriteItems で商品データがすぐ取れます
     */
    public function favoriteItems()
    {
        return $this->belongsToMany(Item::class, 'favorites', 'user_id', 'item_id');
    }

    /**
     * 商品へのコメント（1対多）
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
