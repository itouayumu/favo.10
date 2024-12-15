<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\UserMainController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\RecommendController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\TimelineController;
use App\Http\Controllers\ReplyController;
use App\Http\Controllers\UserProfileController;

// ホームページ
Route::get('/', [UserMainController::class, 'index'])->name('home');

// 認証関連
Auth::routes();


// ユーザー関連
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);
Route::post('/login', [LoginController::class, 'login']); // ログイン処理
Route::post('/logout', [LoginController::class, 'logout'])->name('logout'); // ログアウト処理
// ログイン中ユーザーのプロフィール表示ルート
Route::get('/profile', [UserProfileController::class, 'show'])
    ->name('profile.show')
    ->middleware('auth'); // 認証を必須にする

// スケジュール関連
Route::get('/schedules', [ScheduleController::class, 'schedule']);
Route::get('/schedules/create', [ScheduleController::class, 'create']);
Route::post('/schedules', [ScheduleController::class, 'store']);
Route::get('/schedules/{id}', [ScheduleController::class, 'show']);
Route::get('/schedules/{id}/edit', [ScheduleController::class, 'edit']);
Route::put('/schedules/{id}', [ScheduleController::class, 'update']);
Route::delete('/schedules/{id}', [ScheduleController::class, 'destroy'])->name('schedules.destroy');

// おすすめ関連
Route::get('/recommends', [RecommendController::class, 'index']);

// ホーム画面（デフォルトのリダイレクト先）
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// プロフィール編集ページ
Route::get('/users/{user}/profile/edit', [TagController::class, 'profileEdit'])->name('users.profile.edit');

// タグの紐づけ
Route::post('/users/{user}/tags', [TagController::class, 'attachTag'])->name('users.tags.attach');

//timeline関係
// タイムラインのページを表示
Route::get('/timeline', [TimelineController::class, 'index']);
Route::get('/fetch-timeline', [TimelineController::class, 'fetchTimeline'])->name('timeline.fetch');
Route::post('/store', [TimelineController::class, 'store'])->name('timeline.store');

//返信機能
Route::post('/reply/store', [ReplyController::class, 'store'])->name('reply.store');
Route::get('/reply/fetch/{post_id}', [ReplyController::class, 'fetch'])->name('reply.fetch');