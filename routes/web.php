<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\UserMainController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\RecommendController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;

use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\TagController;

// ホームページ
Route::get('/', [UserMainController::class, 'index'])->name('home');

// 認証関連
Auth::routes();


// ユーザー関連
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);
Route::post('/login', [LoginController::class, 'login']); // ログイン処理
Route::post('/logout', [LoginController::class, 'logout'])->name('logout'); // ログアウト処理
Route::get('/users/profile_edit', [UsersController::class, 'profile_edit']);
// ログイン中ユーザーのプロフィール表示ルート
Route::get('/profile', [UserProfileController::class, 'show'])
    ->name('profile.show')
    ->middleware('auth'); // 認証を必須にする

// 公開タグの表示
Route::get('/tags', [TagController::class, 'publicTags'])->name('users.tags.public');

// タグクリックカウント
Route::post('/tags/{tagId}/count', [TagController::class, 'incrementClickCount']);

//タグ作成
Route::post('/tags/create', [TagController::class, 'create'])->name('tags.create');

//タグ削除
Route::post('/tags/{tagId}/delete', [TagController::class, 'delete'])->name('tags.delete');

//タグ公開・非公開
Route::get('/tags/public', [TagController::class, 'publicTags'])->name('tags.publicTags');
Route::post('/tags/{tagId}/visibility', [TagController::class, 'toggleVisibility'])->name('tags.toggleVisibility');



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