<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reply extends Model
{
    use HasFactory;

    protected $table = 'replies';

    protected $fillable = [
        'user_id',
        'post_id',
        'comment',
        'image',
        'delete_flag',
    ];
}
