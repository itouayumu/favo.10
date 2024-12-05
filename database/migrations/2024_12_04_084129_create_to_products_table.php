<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateToProductsTable extends Migration
{
    /**
     * マイグレーションを実行
     *
     * @return void
     */
    public function up()
    {
        Schema::create('to_products', function (Blueprint $table) {
            $table->id();  // 商品中間ID (主キー、自動インクリメント)
            $table->foreignId('user_id')->constrained()->onDelete('cascade');  // ユーザーID (外部キー)
            $table->foreignId('products_id')->constrained()->onDelete('cascade');  // 商品ID (外部キー)
            $table->boolean('delete_flag')->default(0);  // 削除フラグ (デフォルト: 0)

            $table->timestamps();  // created_at, updated_at
        });
    }

    /**
     * マイグレーションをロールバック
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('to_products');
    }
}
