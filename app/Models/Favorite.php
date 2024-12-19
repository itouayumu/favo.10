<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    use HasFactory;

    protected $table = 'favorite'; // テーブル名を指定
    protected $fillable = ['name', 'genre_id', 'introduction', 'image_1', 'image_2', 'image_3', 'image_4', 'user_id'];

    // Genreモデルとのリレーション (belongsTo)
    public function genre()
    {
        return $this->belongsTo(Genre::class, 'genre_id');
    }
}
