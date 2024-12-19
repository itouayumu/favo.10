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
        Schema::create('replies', function (Blueprint $table) {
            $table->id();  // リプライID (主キー、自動インクリメント)
            $table->unsignedBigInteger('user_id');  // ユーザーID (外部キー)
            $table->unsignedBigInteger('post_id');  // 投稿ID (外部キー)
            $table->text('image')->nullable();  // 画像 (バイナリ)
            $table->string('comment');  // コメント (VARCHAR)
            $table->timestamps();  // 作成日と更新日
            $table->boolean('delete_flag')->default(0);  // 削除フラグ (デフォルト: 0)

            // 外部キー制約
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('post_id')->references('id')->on('post')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('replies');
    }
};
