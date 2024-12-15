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
}
