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
}
