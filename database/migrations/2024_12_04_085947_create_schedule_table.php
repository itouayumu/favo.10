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
        Schema::create('schedules', function (Blueprint $table) {
            $table->id(); // スケジュールID (主キー)
            
            // 外部キー
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // ユーザーID (外部キー)
            $table->foreignId('favorite_id')->constrained('favorite')->onDelete('cascade'); // 推しID (外部キー)
            
            // その他のカラム
            $table->string('title'); // タイトル
            $table->text('image')->nullable(); // 画像 (バイナリデータ格納)
            $table->text('content');// 内容
            $table->date('start_date'); // 開始日
            $table->time('start_time')->nullable(); // 開始時間
            $table->date('end_date')->nullable(); // 終了日
            $table->time('end_time')->nullable(); // 終了時間
            $table->text('url')->nullable(); // URL
            $table->boolean('share_flag')->default(0); // 共有フラグ (デフォルト: 0)
            $table->boolean('delete_flag')->default(0); // 削除フラグ (デフォルト: 0)

            $table->timestamps(); // created_at, updated_at
        });
    }

    /**
     * マイグレーションをロールバック
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules'); // テーブル削除
    }
};
