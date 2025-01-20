<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ToFavorite extends Model
{
    use HasFactory;

    protected $table = 'to_favorite'; // 中間テーブル名を指定

    protected $fillable = [
        'user_id',
        'favorite_id',
        'sort_id',
        'favorite_flag',
        'delete_flag',
    ];

    /**
     * Favoriteテーブルとのリレーション
     */
    public function favorite()
    {
        return $this->belongsTo(Favorite::class, 'favorite_id');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'favorite_tag'); // 推しタグとのリレーション
    }
}
