<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ToSchedule extends Model
{
    use HasFactory;

    // テーブル名がデフォルトでない場合（例: 'to_schedules' としている場合）指定します
    protected $table = 'to_schedules';

    // マスアサインメント可能なカラムを指定
    protected $fillable = [
        'user_id',
        'schedule_id',
        'delete_flag',
    ];

    // リレーション: ToSchedule は Schedule に属する
    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    // リレーション: ToSchedule は User に属する
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
