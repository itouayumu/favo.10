<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'oshiname', // これを追加
        'title',
        'thumbnail', 
        'start_date',
        'start_time',
        'end_date',
        'end_time',
        'url',
    ];
}
