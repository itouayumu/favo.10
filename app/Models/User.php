<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'introduction',
        'image',
        'point',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'user_tag', 'user_id', 'tags_id')
                    ->withPivot('sort_id', 'count', 'hidden_flag', 'delete_flag')
                    ->withTimestamps();
    }

    public function publicTags()
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login')->with('error', 'セッションが切れました。再度ログインしてください。');
        }

        $tags = $user->tags()
                     ->wherePivot('hidden_flag', 0)
                     ->wherePivot('delete_flag', 0)
                     ->get();

        return view('tags.public_tags', compact('user', 'tags'));
    }
}
