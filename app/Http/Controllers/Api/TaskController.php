<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Task\StoreTaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TaskController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Task::where('user_id', auth()->id())->with('goal');

        $query->when($request->filled('status'),   fn($q) => $q->where('status', $request->status));
        $query->when($request->filled('priority'), fn($q) => $q->where('priority', $request->priority));
        $query->when($request->filled('category'), fn($q) => $q->whereRaw('LOWER(category) LIKE ?', ['%' . strtolower($request->category) . '%']));
        $query->when($request->filled('goal_id'),  fn($q) => $q->where('goal_id', $request->goal_id));
        $query->when($request->filled('date'),     fn($q) => $q->whereDate('scheduled_at', $request->date));

        return TaskResource::collection($query->latest()->get());
    }

    public function store(StoreTaskRequest $request): \Illuminate\Http\JsonResponse
    {
        $task = Task::create([...$request->validated(), 'user_id' => auth()->id()]);

        return (new TaskResource($task->load('goal')))->response()->setStatusCode(201);
    }

    public function show(Task $task): TaskResource
    {
        $this->authorize('view', $task);

        return new TaskResource($task->load('goal'));
    }

    public function update(UpdateTaskRequest $request, Task $task): TaskResource
    {
        $this->authorize('update', $task);

        $task->update($request->validated());

        return new TaskResource($task->load('goal'));
    }

    public function destroy(Task $task): JsonResponse
    {
        $this->authorize('delete', $task);

        $task->delete();

        return response()->json(null, 204);
    }
}
