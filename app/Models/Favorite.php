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
                    ->withTimestamps();
    }

}
