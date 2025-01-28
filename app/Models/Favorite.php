<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    use HasFactory;

    protected $table = 'favorite'; // テーブル名を指定

    protected $fillable = [
        'user_id',
        'genre_id',
        'name',
        'introduction',
        'image_1',
        'image_2',
        'image_3',
        'image_4',
        'favorite_count',
        'hidden_flag',
    ];

    // 多対多リレーション
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'favorite_tag', 'favorite_id', 'tags_id')
                    ->withPivot('sort_id', 'count', 'hidden_flag', 'delete_flag')
                    ->wherePivot('delete_flag', 0); // 削除されていないタグのみ
    }

    public function toFavorites()
    {
        return $this->hasMany(ToFavorite::class, 'favorite_id');
    }

    public function followers()
    {
        return $this->belongsToMany(User::class, 'to_favorite')
                    ->withPivot('hidden_flag', 'favorite_flag')
                    ->withTimestamps();
    }
}
