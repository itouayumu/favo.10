<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\UserMainController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\TimelineController;
use App\Http\Controllers\ReplyController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\OshiController;

// ホームページ
Route::get('/', [UserMainController::class, 'index'])->name('home');

// 認証関連
Auth::routes();

// ユーザー関連
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// ログイン中ユーザーのプロフィール表示
Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show')->middleware('auth');
Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');

// タグ関連
Route::resource('tags', TagController::class); // RESTfulなタグ管理

// スケジュール関連
Route::resource('schedules', ScheduleController::class);

// タイムライン関連
Route::prefix('timeline')->group(function () {
    Route::get('/', [TimelineController::class, 'index'])->name('timeline.index'); // タイムラインの表示
    Route::get('/latest', [TimelineController::class, 'fetchTimeline'])->name('timeline.latest'); // 新しい投稿の取得
    Route::post('/store', [TimelineController::class, 'store'])->name('timeline.store'); // 投稿の作成
    Route::get('/post/{id}', [TimelineController::class, 'fetchTimeline'])->name('timeline.fetchPost'); // 特定の投稿を取得
    Route::get('/search', [TimelineController::class, 'search'])->name('timeline.search'); // タイムライン検索
});

// リプライ関連
Route::prefix('replies')->group(function () {
    Route::post('/store', [ReplyController::class, 'store'])->name('replies.store'); // リプライの投稿
    Route::get('/fetch/{postId}', [ReplyController::class, 'fetchReplies'])->name('replies.fetch'); // 特定の投稿に紐付くリプライの取得
    Route::get('/fetch-new', [ReplyController::class, 'fetchNewReplies'])->name('replies.fetchNew'); // 新しいリプライの取得
});



// お気に入り機能
Route::prefix('favorites')->group(function () {
    Route::get('/', [SearchController::class, 'index'])->name('favorites.index');
    Route::get('/search', [SearchController::class, 'searchAjax'])->name('favorites.search.ajax');
    Route::post('/', [FavoriteController::class, 'store'])->name('favorites.store');
    Route::get('/create', [FavoriteController::class, 'create'])->name('favorites.create');
    Route::get('/search', [ScheduleController::class, 'searchFavorites']);
});

// おすすめ機能（認証後のみ）
Route::middleware(['auth'])->group(function () {
    Route::get('/recommend', [OshiController::class, 'recommend'])->name('recommend');
    Route::post('/recommend/favorite/{oshiId}', [OshiController::class, 'addFavorite'])->name('addFavorite');
    Route::get('/recommend/next', [OshiController::class, 'nextRecommended'])->name('nextRecommended');
});

// ユーザー詳細ページ
Route::get('/user/{id}/profile', [ProfileController::class, 'showUser'])->name('user.profile');