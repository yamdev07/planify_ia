<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Goal\StoreGoalRequest;
use App\Http\Requests\Goal\UpdateGoalRequest;
use App\Http\Resources\GoalResource;
use App\Models\Goal;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class GoalController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $goals = Goal::where('user_id', auth()->id())
            ->withCount('tasks')
            ->withCount(['tasks as completed_tasks_count' => fn($q) => $q->where('status', 'done')])
            ->latest()
            ->get();

        return GoalResource::collection($goals);
    }

    public function store(StoreGoalRequest $request): \Illuminate\Http\JsonResponse
    {
        $goal = Goal::create([...$request->validated(), 'user_id' => auth()->id()]);

        return (new GoalResource($goal))->response()->setStatusCode(201);
    }

    public function show(Goal $goal): GoalResource
    {
        $this->authorize('view', $goal);

        return new GoalResource($goal->load('tasks'));
    }

    public function update(UpdateGoalRequest $request, Goal $goal): GoalResource
    {
        $this->authorize('update', $goal);

        $goal->update($request->validated());

        return new GoalResource($goal);
    }

    public function destroy(Goal $goal): JsonResponse
    {
        $this->authorize('delete', $goal);

        $goal->delete();

        return response()->json(null, 204);
    }

    public function stats(Goal $goal): JsonResponse
    {
        $this->authorize('view', $goal);

        $total     = $goal->tasks()->count();
        $completed = $goal->tasks()->where('status', 'done')->count();

        return response()->json([
            'total_tasks'     => $total,
            'completed_tasks' => $completed,
            'completion_rate' => $total > 0 ? round($completed / $total * 100) : 0,
        ]);
    }
}
