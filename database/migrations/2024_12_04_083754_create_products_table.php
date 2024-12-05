<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * マイグレーションを実行
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();  // 商品ID (主キー、自動インクリメント)
            $table->string('name');  // 商品名
            $table->integer('price');  // 価格
            $table->binary('image')->nullable();  // 画像 (BLOB)
            $table->boolean('get_flag')->default(0);  // 入手フラグ (デフォルト: 0)
            $table->boolean('public_flag')->default(0);  // 公開フラグ (デフォルト: 0)
            $table->string('product_code')->default('0');  // 商品コード (デフォルト: '0')
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
        Schema::dropIfExists('products');
    }
}
