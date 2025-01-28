<?php
namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\Favorite; // 推しモデル
use App\Models\ToSchedule; // 中間テーブルモデル
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    // スケジュールの月ごとの表示
    public function schedule(Request $request)
    {
        $currentDate = Carbon::now()->locale('ja');
        if ($request->has('month')) {
            $currentDate = Carbon::createFromFormat('Y-m', $request->input('month'))->locale('ja');
        }

        $currentMonth = $currentDate->format('Y年n月');
        $schedules = Schedule::whereMonth('start_date', $currentDate->month)
                             ->whereYear('start_date', $currentDate->year)
                             ->get();

        $previousMonth = $currentDate->copy()->subMonth()->format('Y-m');
        $nextMonth = $currentDate->copy()->addMonth()->format('Y-m');

        return view('schedules.schedule', compact('schedules', 'currentMonth', 'currentDate', 'previousMonth', 'nextMonth'));
    }

    // スケジュール作成画面表示
    public function create()
    {
        // ログイン中のユーザーがフォローしている推しを取得
        $favorites = Favorite::whereHas('followers', function ($query) {
            $query->where('user_id', auth()->id());
        })->get();

        return view('schedules.create', compact('favorites'));
    }

    // スケジュールの保存
    public function store(Request $request)
    {
        // フォロー中の推しのみ許可
        $request->validate([
            'oshiname' => ['required', 'exists:favorites,id'], // 推しIDが存在し、正しいか検証
            'title' => 'required|string|max:255',
            'start_date' => 'required|date',
            'start_time' => 'required',
            'end_date' => 'required|date|after_or_equal:start_date',
            'end_time' => 'required',
            'image' => 'nullable|image|max:2048',
            'content' => 'required|string',
        ]);

        // 予定作成（ユーザーID、推しIDとともに）
        $schedule = new ToSchedule();
        $schedule->user_id = auth()->id();
        $schedule->favorite_id = $request->oshiname; // 推しIDを保存
        $schedule->title = $request->title;
        $schedule->start_date = $request->start_date;
        $schedule->start_time = $request->start_time;
        $schedule->end_date = $request->end_date;
        $schedule->end_time = $request->end_time;
        $schedule->content = $request->content;

        // 画像の保存
        if ($request->hasFile('image')) {
            $schedule->image = $request->file('image')->store('schedules', 'public');
        }

        $schedule->save(); // 中間テーブル（ToSchedule）への保存

        return redirect('/schedules')->with('success', '予定が作成されました！');
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
            'oshiname' => 'required|integer|exists:favorites,id',
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

        // スケジュールの更新
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

        return redirect('/schedules')->with('success', 'スケジュールが削除されました');
    }

    // ユーザーのスケジュールを取得
    public function fetchSchedules()
    {
        $userId = Auth::id();

        $schedules = Schedule::where('user_id', $userId)
                             ->orderBy('start_date', 'asc')
                             ->get();

        return response()->json($schedules);
    }

    // スケジュールを登録
    public function registerSchedule(Request $request)
    {
        $validated = $request->validate([
            'schedule_id' => 'required|exists:schedules,id',
        ]);

        $userId = auth()->id();

        $toSchedule = ToSchedule::create([
            'user_id' => $userId,
            'schedule_id' => $validated['schedule_id'],
            'delete_flag' => false,
        ]);

        return response()->json([
            'message' => 'スケジュールが登録されました。',
            'data' => $toSchedule,
        ]);
    }
}
