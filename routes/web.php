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
use App\Http\Controllers\OshiTagController;

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


// プロフィール編集ページ
Route::get('/users/{user}/profile/edit', [TagController::class, 'profileEdit'])->name('users.profile.edit');

Route::get('/profile/edit', [OshiController::class, 'editProfile'])->name('profile.edit');
Route::post('/favorite/remove/{id}', [OshiController::class, 'removeFavorite'])->name('favorite.remove');

Route::post('/oshi/{favorite}/toggleVisibility', [OshiController::class, 'toggleVisibility'])->name('oshi.toggleVisibility');

// タグの紐づけ
Route::post('/users/{user}/tags', [TagController::class, 'attachTag'])->name('users.tags.attach');

Route::post('favorites/{favorite_id}/tags', [OshiTagController::class, 'createTag'])->name('oshi.createTag');;

Route::post('oshi/{favoriteId}/tag/{tagId}/toggleVisibility', [OshiTagController::class, 'toggleTagVisibility'])->name('oshi.toggleTagVisibility');
Route::get('/oshiTag/increment/{favoriteId}/{tagId}', [OshiTagController::class, 'incrementTagCount'])->name('oshiTag.increment');
Route::post('oshi/{favoriteId}/tag/{tagId}/delete', [OshiTagController::class, 'deleteTag'])->name('oshi.deleteTag');



//timeline関係
// タイムラインのページを表示
Route::get('/timeline', [TimelineController::class, 'index'])->name('timeline.index');
Route::get('/timeline/fetch-timeline', [TimelineController::class, 'fetchTimeline']);
Route::post('/store', [TimelineController::class, 'store'])->name('timeline.store');
Route::get('/posts/search', [TimelineController::class, 'search'])->name('timeline.search');

// 新しい返信を取得するルート
Route::post('/replies/fetch-new', [ReplyController::class, 'fetchNewReplies']);

// 他のルート
Route::get('/replies/{post_id}', [ReplyController::class, 'fetch']);
Route::post('/replies/fetch', [ReplyController::class, 'fetchReplies']);



//返信機能
Route::post('/reply/store', [ReplyController::class, 'store'])->name('reply.store');
Route::get('/reply/fetch/{post_id}', [ReplyController::class, 'fetch'])->name('reply.fetch');
Route::get('/reply/fetch-new-replies', [ReplyController::class, 'fetchNewReplies']);


//検索機能
Route::get('/posts/search', [TimelineController::class, 'search']);
Route::get('/favorites', [searchcontroller::class, 'index'])->name('favorites.index'); // 一覧表示
Route::get('/favorites/search', [searchcontroller::class, 'searchAjax'])->name('favorites.search.ajax'); // 非同期検索

// あいまい検索API
Route::get('/favorites/search', [ScheduleController::class, 'searchFavorites']);

//新規登録
Route::get('/favorites/create', [FavoriteController::class, 'create'])->name('favorites.create'); // 新規登録フォーム
Route::post('/favorites', [FavoriteController::class, 'store'])->name('favorites.store'); // 新規登録処理

//おすすめ機能
Route::middleware(['auth'])->group(function () {
    Route::get('/recommend', [OshiController::class, 'recommend'])->name('recommend');
    Route::post('/recommend/favorite/{oshiId}', [OshiController::class, 'addFavorite'])->name('addFavorite');
    Route::get('/recommend/next', [OshiController::class, 'nextRecommended'])->name('nextRecommended');
});

// ユーザー詳細ページ
Route::get('/user/{id}/profile', [ProfileController::class, 'showUser'])->name('user.profile');