<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Goal;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIService
{
    private const TIMEOUT         = 30;
    private const RETRY_TIMES     = 2;
    private const RETRY_SLEEP_MS  = 500;

    private string $model;
    private string $apiKey;
    private string $baseUrl = 'https://api.groq.com/openai/v1/chat/completions';

    public function __construct()
    {
        $this->model  = config('ai.model', 'llama-3.3-70b-versatile');
        $this->apiKey = config('ai.groq_api_key', '');
    }

    public function generatePlanning(int $userId, int $goalId, float $availableHoursPerDay): array
    {
        $goal  = Goal::with('tasks')->where('user_id', $userId)->findOrFail($goalId);
        $today = now()->toDateString();

        $tasks = $goal->tasks->map(fn($t) => [
            'id'               => $t->id,
            'title'            => $t->title,
            'priority'         => $t->priority,
            'status'           => $t->status,
            'duration_minutes' => $t->duration_minutes ?? 30,
        ]);

        $prompt = <<<PROMPT
        You are a productivity planner. Given the following goal and its tasks, generate a weekly planning
        that distributes the tasks across the coming days. Assign a scheduled_at (ISO 8601) to each task
        based on the available hours per day. Work hours are between 08:00 and 20:00.
        Return ONLY a valid JSON array — no markdown, no explanation:
        [{"task_id": 1, "scheduled_at": "2026-04-28T09:00:00", "duration_minutes": 60}, ...]

        Goal: {$goal->title}
        Deadline: {$goal->deadline}
        Available hours/day: {$availableHoursPerDay}
        Today: {$today}
        Tasks: {$tasks->toJson()}
        PROMPT;

        return $this->parseJsonArray($this->call($prompt));
    }

    public function suggestTimeSlots(int $userId, int $taskId): array
    {
        $task  = Task::where('user_id', $userId)->findOrFail($taskId);
        $start = now()->startOfWeek(Carbon::MONDAY);
        $end   = now()->endOfWeek(Carbon::SUNDAY)->endOfDay();

        $existingTasks  = Task::where('user_id', $userId)
            ->whereBetween('scheduled_at', [$start, $end])
            ->get(['title', 'scheduled_at', 'duration_minutes']);

        $existingEvents = Event::where('user_id', $userId)
            ->whereBetween('start_at', [$start, $end])
            ->get(['title', 'start_at', 'end_at']);

        $taskTitle    = $task->title;
        $taskPriority = $task->priority;
        $taskDuration = $task->duration_minutes ?? 30;
        $bookedTasks  = $existingTasks->toJson();
        $bookedEvents = $existingEvents->toJson();

        $prompt = <<<PROMPT
        You are a scheduling assistant. Suggest the 3 best time slots this week (Mon-Sun, 08:00-19:00)
        to schedule the given task. Avoid conflicts. Respect priority.
        Return ONLY a valid JSON array — no markdown, no explanation:
        [{"start": "2026-04-28T10:00:00", "end": "2026-04-28T11:00:00", "reason": "Morning focus time"}, ...]

        Task: {$taskTitle} | Priority: {$taskPriority} | Duration: {$taskDuration} min
        Booked tasks: {$bookedTasks}
        Booked events: {$bookedEvents}
        PROMPT;

        return $this->parseJsonArray($this->call($prompt));
    }

    public function analyzeAndOptimize(int $userId): array
    {
        $start = now()->startOfWeek(Carbon::MONDAY);
        $end   = now()->endOfWeek(Carbon::SUNDAY)->endOfDay();

        $tasks     = Task::where('user_id', $userId)->whereBetween('scheduled_at', [$start, $end])->with('goal')->get();
        $events    = Event::where('user_id', $userId)->whereBetween('start_at', [$start, $end])->get();
        $lateGoals = Goal::where('user_id', $userId)
            ->where('status', 'active')
            ->where('deadline', '<', now()->toDateString())
            ->get(['id', 'title', 'deadline']);

        $prompt = <<<PROMPT
        You are a productivity coach. Analyze the week and return insights.
        Return ONLY a valid JSON object — no markdown, no explanation:
        {
          "overloaded_days": ["2026-04-28"],
          "late_goals": [{"title": "...", "deadline": "..."}],
          "free_slots": [{"day": "2026-04-30", "hours": 3}],
          "recommendations": ["...", "..."]
        }

        Week tasks: {$tasks->toJson()}
        Week events: {$events->toJson()}
        Late goals: {$lateGoals->toJson()}
        PROMPT;

        return $this->parseJsonObject($this->call($prompt));
    }

    public function chat(int $userId, array $messages): string
    {
        $context = $this->buildUserContext($userId);

        $system = <<<TEXT
        You are Planify IA, an intelligent productivity assistant.
        Help users manage tasks, goals, and schedules.
        Always reply in the same language as the user.
        Be concise and action-oriented.
        User context: {$context}
        TEXT;

        return $this->call(messages: $messages, system: $system);
    }

    private function buildUserContext(int $userId): string
    {
        return json_encode([
            'active_goals'        => Goal::where('user_id', $userId)->where('status', 'active')->withCount('tasks')->get(['id', 'title', 'deadline']),
            'pending_tasks_count' => Task::where('user_id', $userId)->whereIn('status', ['todo', 'in_progress'])->count(),
            'today_tasks_count'   => Task::where('user_id', $userId)->whereDate('scheduled_at', today())->count(),
        ]);
    }

    private function call(string $userMessage = '', array $messages = [], string $system = ''): string
    {
        if (!$this->apiKey) {
            throw new \RuntimeException('GROQ_API_KEY non configurée.');
        }

        $systemContent = $system ?: 'You are a helpful assistant. Return only valid JSON when asked.';

        $payload = array_merge(
            [['role' => 'system', 'content' => $systemContent]],
            $userMessage ? [['role' => 'user', 'content' => $userMessage]] : $messages,
        );

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type'  => 'application/json',
        ])
        ->timeout(self::TIMEOUT)
        ->retry(self::RETRY_TIMES, self::RETRY_SLEEP_MS, fn($e) => !($e instanceof \Illuminate\Http\Client\ConnectionException))
        ->post($this->baseUrl, [
            'model'       => $this->model,
            'max_tokens'  => 1024,
            'temperature' => 0.5,
            'messages'    => $payload,
        ]);

        if ($response->failed()) {
            Log::error('Groq API error', ['status' => $response->status(), 'body' => $response->body()]);
            throw new \RuntimeException('Groq API error: ' . $response->status());
        }

        return $response->json('choices.0.message.content', '');
    }

    private function parseJsonArray(string $text): array
    {
        $data = $this->decodeJson($text);
        return is_array($data) && array_is_list($data) ? $data : [];
    }

    private function parseJsonObject(string $text): array
    {
        $data = $this->decodeJson($text);
        return is_array($data) && !array_is_list($data) ? $data : [];
    }

    private function decodeJson(string $text): mixed
    {
        // Strip markdown fences if present
        $clean = preg_replace('/^```(?:json)?\s*/m', '', $text);
        $clean = preg_replace('/```\s*$/m', '', $clean);

        $decoded = json_decode(trim($clean), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::warning('AIService: failed to parse JSON response', ['raw' => $text]);
            return [];
        }

        return $decoded;
    }
}
