<?php 

namespace App\Http\Controllers;

use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

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
        $schedules = Schedule::whereMonth('start_date', $currentDate->month)
                             ->whereYear('start_date', $currentDate->year)
                             ->get();

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
        $favorites = DB::table('favorite')
            ->where('name', 'LIKE', "%{$query}%")
            ->select('id', 'name')
            ->get();

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


        Schedule::create([
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

        return redirect('/schedules')->with('success', 'スケジュールが作成されました');
    }
}    