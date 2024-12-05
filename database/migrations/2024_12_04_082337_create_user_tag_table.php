<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserTagTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_tag', function (Blueprint $table) {
            $table->id();  // ユーザータグID (主キー、自動インクリメント)
            $table->unsignedBigInteger('user_id');  // ユーザーID (外部キー)
            $table->unsignedBigInteger('tags_id');  // タグID (外部キー)
            $table->integer('sort_id')->nullable();  // ソートID
            $table->integer('count')->default(0);  // カウント (デフォルト: 0)
            $table->boolean('hidden_flag')->default(0);  // 表示フラグ (デフォルト: 0)
            $table->boolean('delete_flag')->default(0);  // 削除フラグ (デフォルト: 0)
            $table->timestamps();  // created_at, updated_at

            // 外部キー制約
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('tags_id')->references('id')->on('tags')->onDelete('cascade');

            // インデックス（必要に応じて追加）
            $table->unique(['user_id', 'tags_id']);  // user_id と tags_id の組み合わせがユニーク
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_tag');
    }
}
