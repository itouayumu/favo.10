<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReplyController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
    // 返信の保存
Route::post('/replies/store', [ReplyController::class, 'store'])->name('replies.store');

// 返信の取得
Route::get('/replies/fetch/{postId}', [ReplyController::class, 'fetch'])->name('replies.fetch');
});
