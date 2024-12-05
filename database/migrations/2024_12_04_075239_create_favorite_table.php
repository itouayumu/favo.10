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
        Schema::create('favorite', function (Blueprint $table) {
            $table->id(); // 推しID（主キー）
            $table->unsignedBigInteger('user_id'); // ユーザーID
            $table->unsignedBigInteger('genre_id'); // ジャンルID
            $table->string('name'); // 推し名
            $table->text('introduction')->nullable(); // 推し紹介
            $table->binary('image_1')->nullable(); // 画像1
            $table->binary('image_2')->nullable(); // 画像2
            $table->binary('image_3')->nullable(); // 画像3
            $table->binary('image_4')->nullable(); // 画像4
            $table->integer('favorite_count')->default(0); // 推しカウント
            $table->timestamps(); // 作成日・更新日
            $table->boolean('hidden_flag')->default(false); // 表示フラグ（デフォルト: 0）

            // 外部キー制約を設定
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('genre_id')->references('id')->on('genre')->onDelete('cascade'); // 修正: `genre`
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('favorite');
    }
};
