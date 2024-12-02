<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->string('oshiname')->nullable(); // 推しの名前
            $table->string('title'); // 配信タイトル
            $table->string('thumbnail')->nullable(); // 配信サムネイル
            $table->date('start_date'); // 開始日
            $table->time('start_time')->nullable(); // 開始時間
            $table->date('end_date')->nullable(); // 終了日
            $table->time('end_time')->nullable(); // 終了時間
            $table->string('url')->nullable(); // URL（必要に応じて）
            $table->timestamps(); // 作成日と更新日
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules'); // テーブル名を修正
    }
};
