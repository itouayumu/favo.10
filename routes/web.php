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
use App\Http\Controllers\searchcontroller;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\OshiController;
use App\Http\Controllers\OshiTagController;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\OGPController;

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
    
    Route::get('/prof/{id}', [ProfileController::class, 'showUser'])
        ->name('user.profile')
        ->middleware('auth');

Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');

// 公開タグの表示
Route::get('/tags', [TagController::class, 'publicTags'])->name('users.tags.public');

// タグクリックカウント
Route::post('/tags/{tagId}/count', [TagController::class, 'incrementClickCount']);
Route::get('/tags/increment/{tagId}/{userId}', [TagController::class, 'incrementClickCount'])->name('tags.incrementClickCount');

//タグ作成
Route::post('/tags/create', [TagController::class, 'create'])->name('tags.create');

//タグ削除
Route::post('/tags/{tagId}/delete', [TagController::class, 'delete'])->name('tags.delete');

//タグ公開・非公開
Route::get('/tags/public', [TagController::class, 'publicTags'])->name('tags.publicTags');
Route::post('/tags/{tagId}/visibility', [TagController::class, 'toggleVisibility'])->name('tags.toggleVisibility');



// スケジュール関連
// Route::get('/schedules', [ScheduleController::class, 'schedule']);
Route::get('/schedules/create', [ScheduleController::class, 'create']);
Route::post('/schedules', [ScheduleController::class, 'store']);
Route::get('/schedules/{id}', [ScheduleController::class, 'show']);
Route::get('/schedules/{id}/edit', [ScheduleController::class, 'edit'])->name('schedules.edit');
Route::put('/schedules/{schedule}', [ScheduleController::class, 'update'])->name('schedules.update');
Route::delete('/schedules/{schedule}', [ScheduleController::class, 'destroy'])->name('schedules.destroy');
Route::get('/profile/user/{user}', [ProfileController::class, 'show'])->name('profile.showUser');
Route::get('/profile/user/{id}', [ProfileController::class, 'showUser'])
    ->name('profile.showUser')
    ->middleware('auth');
 Route::post('/register-schedule', [ScheduleController::class, 'registerSchedule'])->name('register.schedule');
    

// ホーム画面（デフォルトのリダイレクト先
Route::get('/home', [ScheduleController::class, 'schedule']);

// プロフィール編集ページ
Route::get('/users/{user}/profile/edit', [TagController::class, 'profileEdit'])->name('users.profile.edit');

Route::get('/profile/edit', [OshiController::class, 'editProfile'])->name('profile.edit');
Route::post('/favorite/remove/{id}', [OshiController::class, 'removeFavorite'])->name('favorite.remove');

Route::post('/oshi/{favorite}/toggleVisibility', [OshiController::class, 'toggleVisibility'])->name('oshi.toggleVisibility');

// タグの紐づけ
Route::post('/users/{user}/tags', [TagController::class, 'attachTag'])->name('users.tags.attach');

Route::post('favorites/{favorite_id}/tags', [OshiTagController::class, 'createTag'])->name('oshi.createTag');;
Route::get('oshi/{id}', [OshiController::class, 'show'])->name('oshi.show');
Route::get('oshi/{id}/edit', [OshiController::class, 'edit'])->name('oshi.edit');
Route::post('oshi/{id}/update', [OshiController::class, 'update'])->name('oshi.update');
Route::post('oshi/{favoriteId}/tag/{tagId}/toggleVisibility', [OshiTagController::class, 'toggleTagVisibility'])->name('oshi.toggleTagVisibility');
Route::get('/oshiTag/increment/{favoriteId}/{tagId}', [OshiTagController::class, 'incrementTagCount'])->name('oshiTag.increment');
Route::post('oshi/{favoriteId}/tag/{tagId}/delete', [OshiTagController::class, 'deleteTag'])->name('oshi.deleteTag');



//timeline関係
// タイムラインのページを表示
Route::get('/timeline', [TimelineController::class, 'index'])->name('timeline.index');
Route::get('/timeline/fetch-timeline', [TimelineController::class, 'fetchTimeline']);
Route::post('/store', [TimelineController::class, 'store'])->name('timeline.store');
Route::get('/posts/search', [TimelineController::class, 'search'])->name('timeline.search');
Route::post('/timeline/store', [TimelineController::class, 'store'])->name('timeline.store');
Route::get('/schedules', [ScheduleController::class, 'fetchSchedules'])->name('schedules.fetch');



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

//おすすめ機能
Route::middleware(['auth'])->group(function () {
    Route::get('/recommend', [OshiController::class, 'recommend'])->name('recommend');
    Route::post('/recommend/favorite/{oshiId}', [OshiController::class, 'addFavorite'])->name('addFavorite');
    Route::get('/recommend/next', [OshiController::class, 'nextRecommended'])->name('nextRecommended');
});

Route::post('/reply/store', [TimelineController::class, 'storeReply'])->name('reply.store');
Route::get('/reply/fetch/{postId}', [TimelineController::class, 'fetchReplies']);

Route::get('/user/{id}/profile', [ProfileController::class, 'showUser'])->name('user.profile');

// 推しの新規登録フォームの表示
Route::get('/recommends/create', [FavoriteController::class, 'create'])->name('recommends.create');

// 推しの新規登録処理
Route::post('/recommends/store', [FavoriteController::class, 'store'])->name('recommends.store');

//url確認ページ
Route::get('/confirm', function (Illuminate\Http\Request $request) {
    $url = $request->query('url'); // クエリパラメータからURLを取得
    if (!$url) {
        return redirect()->back()->with('error', 'リンク先が指定されていません。');
    }
    return view('confirm', ['url' => $url]);
})->name('confirm');

Route::get('/fetch-ogp', [OGPController::class, 'fetchOGP']);
Route::get('/timeline/new-posts', [TimelineController::class, 'fetchTimeline']);

Route::get('/search', [TimelineController::class, 'search']);

Route::middleware(['web'])->group(function () {
    Route::get('/timeline/new-posts', [TimelineController::class, 'getNewPosts']);
});
Route::get('/api/schedules', [ScheduleController::class, 'getSchedules']);

Route::middleware('auth:sanctum')->get('/schedules', [ScheduleController::class, 'fetchSchedules']);

Route::post('/register-schedule', [ScheduleController::class, 'registerSchedule']);