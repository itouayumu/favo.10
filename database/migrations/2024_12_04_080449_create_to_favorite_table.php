<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateToFavoriteTable extends Migration
{
    /**
     * テーブルの作成
     *
     * @return void
     */
    public function up()
    {
        Schema::create('to_favorite', function (Blueprint $table) {
            $table->id('id');  // 推し中間ID (自動インクリメント)
            $table->foreignId('user_id')->constrained()->onDelete('cascade');  // ユーザーID (外部キー)
            $table->foreignId('favorite_id')->constrained('favorite')->onDelete('cascade');// 推しID (外部キー)
            $table->integer('sort_id')->nullable();  // ソートID
            $table->boolean('favorite_flag')->default(0);  // 推しフラグ (デフォルト: 0)
            $table->boolean('delete_flag')->default(0);  // 削除フラグ (デフォルト: 0)
            $table->boolean('hidden_flag')->default(1);
            $table->timestamps();  // created_at, updated_at

            // インデックス（必要に応じて追加）
            $table->unique(['user_id', 'favorite_id']);  // user_id と favorite_id の組み合わせがユニーク
        });
    }

    /**
     * テーブルの削除
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('to_favorite');
    }
}
