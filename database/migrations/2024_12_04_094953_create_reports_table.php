<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id(); // 通報ID
            $table->foreignId('user_id')->constrained('users'); // ユーザーID（users テーブルとの外部キー）
            $table->foreignId('admin_id')->constrained('admin'); // 管理者ID（admin テーブルとの外部キー）
            $table->string('report_code'); // 通報コード
            $table->string('comment'); // コメント
            $table->timestamp('created_at')->nullable(); // 作成日
            $table->boolean('delete_flag')->default(0); // 削除フラグ
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reports');
    }
}
