<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'introduction',
        'image',
        'point',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
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
        return $this->belongsToMany(Tag::class)->withPivot('hidden_flag', 'count');
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
