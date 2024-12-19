<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Genre extends Model
{
    use HasFactory;

    // テーブル名
    protected $table = 'genre'; // テーブル名を指定

    // 主キー
    protected $primaryKey = 'id';

    // タイムスタンプを無効化
    public $timestamps = false;

    // 更新可能なカラム
    protected $fillable = [
        'genre_name',
        'delete_flag',
    ];
}
