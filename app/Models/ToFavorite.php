<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ToFavorite extends Model
{
    use HasFactory;

    // テーブル名がデフォルトでない場合（例: 'to_favorites' としている場合）指定します
    protected $table = 'to_favorites';

    // マスアサインメント可能なカラムを指定
    protected $fillable = [
        'user_id',
        'favorite_id',
        'sort_id',
        'favorite_flag',
        'delete_flag',
        'hidden_flag',
    ];

    // リレーション: ToFavorite は User に属する
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // リレーション: ToFavorite は Favorite に属する
    public function favorite()
    {
        return $this->belongsTo(Favorite::class);
    }
}
