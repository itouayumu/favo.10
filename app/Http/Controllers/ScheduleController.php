<?php 

namespace App\Http\Controllers;

use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\ToSchedule;
use App\Models\ToFavorite;
use Illuminate\Support\Facades\Auth;

class ScheduleController extends Controller
{
    // スケジュール表示
    public function schedule(Request $request)
    {
        $currentDate = Carbon::now()->locale('ja');
        if ($request->has('month')) {
            $currentDate = Carbon::createFromFormat('Y-m', $request->input('month'))->locale('ja');
        }
    
        $currentMonth = $currentDate->format('Y年n月');
        
        // 中間テーブルを通じてスケジュールと推し情報を取得
// ScheduleController.php

// 中間テーブルを通じてスケジュールと推し情報を取得
$schedules = ToSchedule::where('user_id', auth()->user()->id) // ログイン中のユーザーのIDに絞り込む
    ->with(['schedule.favorite']) // スケジュールと推しの詳細をロード
    ->get()
    ->pluck('schedule'); 
// 実際のスケジュールデータを抽出

        $previousMonth = $currentDate->copy()->subMonth()->format('Y-m');
        $nextMonth = $currentDate->copy()->addMonth()->format('Y-m');
    
        return view('schedules.schedule', compact('schedules', 'currentMonth', 'currentDate', 'previousMonth', 'nextMonth'));
    }
    

    // スケジュール詳細表示
    public function show($id)
    {
        $schedule = Schedule::findOrFail($id);
        return view('schedules.show', compact('schedule'));
    }

    // スケジュール作成画面表示
    public function create()
    {
        return view('schedules.create');
    }

// 推しの名前検索
public function searchFavorites(Request $request)
{
    $query = $request->input('query');

    // ログインしているユーザーのIDを取得
    $userId = auth()->id();

    // ログインユーザーのToFavoriteから関連するFavoriteを検索
    $favorites = ToFavorite::where('user_id', $userId) // ユーザーIDで絞り込み
        ->whereHas('favorite', function ($q) use ($query) {
            $q->where('name', 'LIKE', "%{$query}%"); // 名前で検索
        })
        ->with('favorite:id,name') // 必要なカラムのみ取得
        ->get()
        ->pluck('favorite'); // favoriteのみ抽出

    return response()->json($favorites);
}


  // スケジュールの保存
public function store(Request $request)
{
    // バリデーション
    $request->validate([
        'title' => 'required|string|max:255',
        'content' => 'nullable|string',
        'oshiname' => 'required|integer|exists:favorite,id',
        'start_date' => 'required|date',
        'start_time' => 'required|date_format:H:i',
        'end_date' => 'required|date',
        'end_time' => 'required|date_format:H:i',
    ]);

    $imagePath = null;

    if ($request->hasFile('image')) {
        $imagePath = $request->file('image')->store('images', 'public');
    }

    // スケジュールを作成
    $schedule = Schedule::create([
        'user_id' => auth()->user()->id,
        'title' => $request->input('title'),
        'content' => $request->input('content', ''),
        'start_date' => $request->input('start_date'),
        'start_time' => $request->input('start_time'),
        'end_date' => $request->input('end_date'),
        'end_time' => $request->input('end_time'),
        'favorite_id' => $request->input('oshiname'),
        'image' => $imagePath,
    ]);

    // ToScheduleテーブルに登録
    ToSchedule::create([
        'user_id' => auth()->user()->id,
        'schedule_id' => $schedule->id, // 作成したスケジュールのIDを使用
        'favorite_id' => $schedule->favorite_id, // 作成したスケジュールの推しIDを使用
        'delete_flag' => false, // 必要に応じて適切な値を設定
    ]);

    return redirect('/home')->with('success', 'スケジュールが作成されました');
}


    // スケジュール編集画面表示   
    
    public function edit($id)
    {
        $schedule = Schedule::findOrFail($id);
        return view('schedules.edit', compact('schedule'));
    }

    // スケジュール更新処理
    public function update(Request $request, $id)
    {
        $schedule = Schedule::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'oshiname' => 'required|integer|exists:favorite,id',
            'start_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_date' => 'required|date',
            'end_time' => 'required|date_format:H:i',
        ]);

        if ($request->hasFile('image')) {
            if ($schedule->image) {
                Storage::disk('public')->delete($schedule->image);
            }
            $imagePath = $request->file('image')->store('images', 'public');
            $schedule->image = $imagePath;
        }

        $schedule->update([
            'title' => $request->input('title'),
            'content' => $request->input('content', ''),
            'start_date' => $request->input('start_date'),
            'start_time' => $request->input('start_time'),
            'end_date' => $request->input('end_date'),
            'end_time' => $request->input('end_time'),
            'favorite_id' => $request->input('oshiname'),
        ]);

        return redirect()->route('schedules.edit', $id)->with('success', 'スケジュールが更新されました');
    }

    // スケジュール削除処理
    public function destroy($id)
    {
        $schedule = Schedule::findOrFail($id);

        if ($schedule->image) {
            Storage::disk('public')->delete($schedule->image);
        }

        $schedule->delete();

        return redirect('/home')->with('success', 'スケジュールが削除されました');
    }
    public function fetchSchedules()
{
    $userId = Auth::id(); // ログインユーザーのIDを取得

    // ログインユーザーの予定を取得
    $schedules = Schedule::where('user_id', $userId)
                         ->orderBy('start_date', 'asc')
                         ->get();

    return response()->json($schedules);
}

public function registerSchedule(Request $request)
{
    // バリデーション
    $validated = $request->validate([
        'schedule_id' => 'required|exists:schedules,id',
    ]);

    // 現在のユーザーIDを取得
    $userId = auth()->id();

    // スケジュール情報を取得
    $schedule = Schedule::find($validated['schedule_id']);

    if (!$schedule) {
        return response()->json([
            'message' => '指定されたスケジュールが存在しません。',
        ], 404);
    }

    // スケジュールからfavorite_idを取得
    $favoriteId = $schedule->favorite_id;

    // データの保存（favorite_idも一緒に保存）
    $toSchedule = ToSchedule::create([
        'user_id' => $userId,
        'schedule_id' => $validated['schedule_id'],
        'favorite_id' => $favoriteId, // favorite_idを保存
        'delete_flag' => false,
    ]);

    // レスポンスを返す
    return response()->json([
        'message' => 'スケジュールが登録されました。',
        'data' => $toSchedule,
    ]);
}
}