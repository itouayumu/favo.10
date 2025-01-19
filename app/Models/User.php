<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'image', // ユーザーアイコン用フィールド
    ];

    protected $appends = ['icon_url']; // JSONにアクセサを含める

    public function getIconUrlAttribute()
    {
        return $this->image 
            ? Storage::url($this->image) 
            : asset('images/default_icon.png'); // デフォルトアイコン
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'user_tag', 'user_id', 'tags_id')
                    ->withPivot('sort_id', 'count', 'hidden_flag', 'delete_flag')
                    ->withTimestamps();
    }

    // お気に入りの推し (Favorite モデルとのリレーション)
    public function favorites()
    {
        return $this->hasMany(Favorite::class, 'user_id'); // `favorite` テーブルとのリレーション
    }

    // お気に入りの推し (ToFavorite モデルとのリレーション)
    public function toFavorites()
    {
        return $this->hasMany(ToFavorite::class, 'user_id'); // `to_favorite` テーブルとのリレーション
    }
}
