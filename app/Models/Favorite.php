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
}
