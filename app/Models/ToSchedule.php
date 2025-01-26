<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ToSchedule extends Model
{
    use HasFactory;

    protected $table = 'to_schedule'; // テーブル名を指定

    protected $fillable = [
        'user_id',
        'schedule_id',
        'delete_flag',
    ];

   public function user()
   {
       return $this->belongsTo(User::class, 'user_id');
   }

   /**
    * スケジュールリレーション
    */
   public function schedule()
   {
       return $this->belongsTo(Schedule::class, 'schedule_id');
   }
}


