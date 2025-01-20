<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reply extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'post_id',
        'comment',
        'image',
        'delete_flag',
    ];

    /**
     * 投稿 (Post) とのリレーション
     */
    public function post()
    {
        return $this->belongsTo(Post::class, 'post_id'); // Replyは1つのPostに属する
    }

    /**
     * ユーザー (User) とのリレーション
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id'); // Replyは1人のUserに属する
    }
    
}
