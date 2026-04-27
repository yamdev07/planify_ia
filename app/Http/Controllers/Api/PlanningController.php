<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PlanningController extends Controller
{
    public function weekView(Request $request)
    {
        $date  = $request->filled('date') ? Carbon::parse($request->date) : now();
        $start = $date->copy()->startOfWeek(Carbon::MONDAY);
        $end   = $date->copy()->endOfWeek(Carbon::SUNDAY)->endOfDay();

        $tasks = Task::where('user_id', auth()->id())
            ->whereBetween('scheduled_at', [$start, $end])
            ->with('goal')
            ->get();

        $events = Event::where('user_id', auth()->id())
            ->where('start_at', '>=', $start)
            ->where('start_at', '<=', $end)
            ->get();

        return [
            'tasks'      => $tasks,
            'events'     => $events,
            'week_start' => $start->toIso8601String(),
            'week_end'   => $end->toIso8601String(),
        ];
    }
}
