<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFavoriteHistoryTable extends Migration
{
    /**
     * テーブルの作成
     *
     * @return void
     */
    public function up()
    {
        Schema::create('favorite_history', function (Blueprint $table) {
            $table->id('id');  // 推し編集履歴ID (自動インクリメント)
            $table->unsignedBigInteger('favorite_id');  // 推しID (外部キー)
            $table->foreign('favorite_id')->references('id')->on('favorite')->onDelete('cascade');  // 外部キー制約
            $table->foreignId('genre_id')->constrained('genre')->onDelete('cascade');  // ジャンルID (外部キー)
            $table->string('name');  // 推し名
            $table->text('introduction')->nullable();  // 推し紹介
            $table->binary('image_1')->nullable();  // 画像1
            $table->binary('image_2')->nullable();  // 画像2
            $table->binary('image_3')->nullable();  // 画像3
            $table->binary('image_4')->nullable();  // 画像4
            $table->binary('edited_by')->nullable();  // 更新者
            $table->timestamps();  // created_at, updated_at
            $table->boolean('delete_flag')->default(false);  // 削除フラグ (デフォルト: 0)

            // インデックス（必要に応じて追加）
            $table->unique(['favorite_id', 'genre_id']);  // 推しIDとジャンルIDの組み合わせがユニーク
        });
    }

    /**
     * テーブルの削除
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('favorite_history');
    }
}
