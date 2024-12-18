<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'create_user', 'delete_flag'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_tag', 'tags_id', 'user_id')
                    ->withPivot('sort_id', 'count', 'hidden_flag', 'delete_flag')
                    ->withTimestamps();
    }
}