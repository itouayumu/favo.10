<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $table = 'post';

    protected $fillable = [
        'user_id',
        'favorite_id',
        'schedule_id',
        'post',
        'image',
        'delete_flag',
    ];

    // ユーザーとのリレーション
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // 返信とのリレーション
    public function replies()
    {
        return $this->hasMany(Reply::class, 'post_id');
    }

    // 推し（Favorite）とのリレーション
    public function favorite()
    {
        return $this->belongsTo(Favorite::class, 'favorite_id');  // favorite_idをキーにFavoriteモデルと関連付け
    }
    // App\Models\Post
    public function schedule()
    {
        return $this->belongsTo(Schedule::class, 'schedule_id', 'id'); // 'schedule_id' が正しい外部キーであることを指定
    }
    


}

