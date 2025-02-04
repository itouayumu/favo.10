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

    /**
     * 推し情報（Favorite）とのリレーション
     */
// Schedule.php モデル
public function favorite()
{
    return $this->belongsTo(Favorite::class, 'favorite_id'); // 'favorite_id' は外部キー
}


    /**
     * 関連する投稿（Post）とのリレーション
     */
    public function posts()
    {
        return $this->hasMany(Post::class, 'schedule_id');
    }
    
}
