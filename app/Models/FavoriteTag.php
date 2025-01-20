<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class FavoriteTag extends Pivot
{
    protected $table = 'favorite_tag';

    protected $fillable = [
        'favorite_id',
        'tags_id',
        'sort_id',
        'count',
        'hidden_flag',
        'delete_flag'
    ];

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'favorite_tag', 'favorite_id', 'tags_id')
                    ->withPivot('sort_id', 'count', 'hidden_flag', 'delete_flag')
                    ->withTimestamps();
    }
}
