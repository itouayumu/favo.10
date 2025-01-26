<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'favorite_id',
        'title',
        'image', 
        'content',
        'start_date',
        'start_time',
        'end_date',
        'end_time',
        'url',
    ];
    // Scheduleモデルに推し情報へのリレーション

public function favorite()
{
    return $this->belongsTo(Favorite::class, 'favorite_id');  // favorite_idをキーにFavoriteモデルと関連付け
}
public function posts()
{
    return $this->hasMany(Post::class, 'schedule_id', 'id'); // 'schedule_id' がリレーションキーであることを指定
}


}

