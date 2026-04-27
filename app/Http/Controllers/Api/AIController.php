<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\GeneratePlanningJob;
use App\Services\AIService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AIController extends Controller
{
    public function __construct(private AIService $ai) {}

    public function generatePlanning(Request $request): JsonResponse
    {
        $data = $request->validate([
            'goal_id'                 => ['required', 'integer', 'exists:goals,id'],
            'available_hours_per_day' => ['required', 'numeric', 'min:0.5', 'max:16'],
        ]);

        // Dispatch to queue — heavy operation, don't block the request
        GeneratePlanningJob::dispatch(
            auth()->id(),
            $data['goal_id'],
            $data['available_hours_per_day'],
        );

        return response()->json([
            'message' => 'Génération du planning en cours. Les tâches seront mises à jour sous peu.',
        ], 202);
    }

    public function suggestTimeSlots(Request $request): JsonResponse
    {
        $data = $request->validate([
            'task_id' => ['required', 'integer', 'exists:tasks,id'],
        ]);

        $slots = $this->ai->suggestTimeSlots(auth()->id(), $data['task_id']);

        return response()->json(['slots' => $slots]);
    }

    public function analyzeAndOptimize(): JsonResponse
    {
        $analysis = $this->ai->analyzeAndOptimize(auth()->id());

        return response()->json($analysis);
    }

    public function chat(Request $request): JsonResponse
    {
        $data = $request->validate([
            'messages'             => ['required', 'array', 'min:1', 'max:20'],
            'messages.*.role'      => ['required', 'in:user,assistant'],
            'messages.*.content'   => ['required', 'string', 'max:2000'],
        ]);

        $reply = $this->ai->chat(auth()->id(), $data['messages']);

        return response()->json(['reply' => $reply]);
    }
}
