<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recommend extends Model
{
    use HasFactory;

    protected $table = 'favorite'; // テーブル名を明示

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

    protected $casts = [
        'hidden_flag' => 'boolean',
    ];
}
