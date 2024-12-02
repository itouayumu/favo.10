<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    public function schedule(Request $request)
    {
        // 現在の月またはリクエストされた月を取得
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

    public function show($id)
    {
        $schedule = Schedule::findOrFail($id);
        return view('schedules.show', compact('schedule'));
    }

    public function create()
    {
        return view('schedules.create');
    }

    public function store(Request $request)
    {
        // バリデーション
        $request->validate([
            'title' => 'required|string|max:255',
            'start_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_date' => 'required|date',
            'end_time' => 'required|date_format:H:i',
            'oshiname' => 'nullable|string|max:255',
            'thumbnail' => 'nullable|image|max:2048', // サムネイル画像のバリデーション
        ]);
    
        $thumbnailPath = null;
    
        // サムネイル画像がアップロードされた場合の処理
        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('thumbnails', 'public');
        }
    
        // スケジュールの作成
        Schedule::create([
            'title' => $request->title,
            'start_date' => $request->start_date,
            'start_time' => $request->start_time,
            'end_date' => $request->end_date,
            'end_time' => $request->end_time,
            'oshiname' => $request->oshiname,
            'thumbnail' => $thumbnailPath, // サムネイル画像のパスを保存
        ]);
    
        return redirect('/schedules');
    }
    public function edit($id)
    {
        $schedule = Schedule::findOrFail($id);
        return view('schedules.edit', compact('schedule'));
    }

    public function update(Request $request, $id)
    {
        $schedule = Schedule::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'start_date' => 'nullable|date',
            'start_time' => 'nullable|date_format:H:i',
            'end_date' => 'nullable|date',
            'end_time' => 'nullable|date_format:H:i',
            'url' => 'nullable|url',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($schedule->image) {
                Storage::delete('public/' . $schedule->image);
            }
            $validated['image'] = $request->file('image')->store('images', 'public');
        }

        $schedule->update($validated);
        return redirect('/schedules');
    }

    public function destroy($id)
    {
        $schedule = Schedule::findOrFail($id);

        if ($schedule->image) {
            Storage::delete('public/' . $schedule->image);
        }

        $schedule->delete();
        return redirect('/schedules');
    }
}
