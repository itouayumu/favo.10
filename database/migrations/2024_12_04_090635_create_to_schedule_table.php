<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * マイグレーションを実行
     */
    public function up()
    {
        Schema::create('to_schedule', function (Blueprint $table) {
            $table->id(); // スケジュール中間ID (主キー)

            // ユーザーID（外部キー）
            $table->unsignedBigInteger('user_id'); // ユーザーID

            // スケジュールID（外部キー）
            $table->unsignedBigInteger('schedule_id'); // スケジュールID

            // 削除フラグ
            $table->boolean('delete_flag')->default(0); // 削除フラグ (デフォルト: 0)

            // 外部キー制約
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade'); // ユーザーIDに関連する外部キー
            $table->foreign('schedule_id')->references('id')->on('schedules')->onDelete('cascade'); // スケジュールIDに関連する外部キー

            $table->timestamps(); // created_at, updated_at
        });
    }

    /**
     * マイグレーションをロールバック
     */
    public function down(): void
    {
        Schema::dropIfExists('to_schedule'); // テーブル削除
    }
};
