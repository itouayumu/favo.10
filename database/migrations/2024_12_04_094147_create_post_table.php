<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('post', function (Blueprint $table) {
            $table->id();  // 投稿ID (主キー、自動インクリメント)
            $table->unsignedBigInteger('user_id');  // ユーザーID (外部キー)
            $table->unsignedBigInteger('favorite_id');  // 推しID (外部キー)
            $table->unsignedBigInteger('schedule_id');  // スケジュールID (外部キー)
            $table->text('post');  // 投稿内容
            $table->binary('image')->nullable();  // 画像 (バイナリ)
            $table->timestamps();  // 作成日と更新日
            $table->boolean('delete_flag')->default(0);  // 削除フラグ (デフォルト: 0)

            // 外部キー制約
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('favorite_id')->references('id')->on('favorite')->onDelete('cascade');
            $table->foreign('schedule_id')->references('id')->on('schedules')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post');
    }
};
