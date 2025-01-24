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
use App\Http\Controllers\OshiController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\searchcontroller;
use App\Http\Controllers\FavoriteController;


// ホームページ
Route::get('/', [UserMainController::class, 'index'])->name('home');

// 認証関連
Auth::routes();


// ユーザー関連
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);
Route::post('/login', [LoginController::class, 'login']); // ログイン処理
Route::post('/logout', [LoginController::class, 'logout'])->name('logout'); // ログアウト処理
// ログイン中ユーザーのプロフィール表示
Route::get('/profile', [ProfileController::class, 'show'])
    ->name('profile.show')
    ->middleware('auth');

// 他ユーザーのプロフィール表示
Route::get('/profile/{id}', [ProfileController::class, 'showUser'])
    ->name('profile.showUser')
    ->middleware('auth');

    Route::get('/profile/{id}', [ProfileController::class, 'showUser'])->name('profile.showUser')->middleware('auth');



Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');

// 公開タグの表示
Route::get('/tags', [TagController::class, 'publicTags'])->name('users.tags.public');

// タグクリックカウント
Route::post('/tags/{tagId}/count', [TagController::class, 'incrementClickCount']);
Route::get('/tags/increment/{tagId}', [TagController::class, 'incrementClickCount'])->name('tags.incrementClickCount');

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
Route::get('/home', [ScheduleController::class, 'schedule']);

// プロフィール編集ページ
Route::get('/users/{user}/profile/edit', [TagController::class, 'profileEdit'])->name('users.profile.edit');

// タグの紐づけ
Route::post('/users/{user}/tags', [TagController::class, 'attachTag'])->name('users.tags.attach');

//timeline関係
// タイムラインのページを表示
Route::get('/timeline', [TimelineController::class, 'index'])->name('timeline.index');
Route::get('/timeline/fetch-timeline', [TimelineController::class, 'fetchTimeline']);
Route::post('/store', [TimelineController::class, 'store'])->name('timeline.store');
Route::get('/posts/search', [TimelineController::class, 'search'])->name('timeline.search');
Route::post('/timeline/store', [TimelineController::class, 'store'])->name('timeline.store');



//返信機能
Route::post('/replies/store', [ReplyController::class, 'store'])->name('reply.store');
Route::get('/replies/{post}', [ReplyController::class, 'fetch'])->name('reply.fetch');
Route::get('/reply/fetch-new-replies', [ReplyController::class, 'fetchNewReplies']);
Route::get('/replies/{post}', [ReplyController::class, 'fetch'])->name('replies.index');
// 返信一覧を取得
Route::get('/reply/fetch/{post_id}', [ReplyController::class, 'fetch'])->name('reply.fetch');

// 全ての返信を取得 (もし必要なら)
Route::get('/replies/{post}', [ReplyController::class, 'fetch'])->name('replies.index');




//検索機能
Route::get('/posts/search', [TimelineController::class, 'search']);
Route::get('/favorites', [searchcontroller::class, 'index'])->name('favorites.index'); // 一覧表示
Route::get('/favorites/search', [searchcontroller::class, 'searchAjax'])->name('favorites.search.ajax'); // 非同期検索

// あいまい検索API
Route::get('/favorites/search', [ScheduleController::class, 'searchFavorites']);

//新規登録
Route::get('/recommends/create', [FavoriteController::class, 'create'])->name('recommends.create'); // 新規登録フォーム
Route::post('/recommends', [FavoriteController::class, 'store'])->name('recommends.store'); // 新規登録処理

Route::get('/api/fetch-timeline', [TimelineController::class, 'fetchTimeline'])->name('fetchTimeline');
//おすすめ機能
Route::middleware(['auth'])->group(function () {
    Route::get('/recommend', [OshiController::class, 'recommend'])->name('recommend');
    Route::post('/recommend/favorite/{oshiId}', [OshiController::class, 'addFavorite'])->name('addFavorite');
    Route::get('/recommend/next', [OshiController::class, 'nextRecommended'])->name('nextRecommended');
});

Route::get('/api/user-schedules', [ScheduleController::class, 'getUserSchedules'])->name('user-schedules');
