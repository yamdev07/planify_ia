<?php

namespace App\Jobs;

use App\Models\Task;
use App\Services\AIService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GeneratePlanningJob implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    public int   $tries   = 3;
    public int   $timeout = 120;
    public int   $backoff = 10;

    public function __construct(
        public readonly int   $userId,
        public readonly int   $goalId,
        public readonly float $availableHoursPerDay,
    ) {}

    public function handle(AIService $ai): void
    {
        $planning = $ai->generatePlanning($this->userId, $this->goalId, $this->availableHoursPerDay);

        foreach ($planning as $item) {
            if (!isset($item['task_id'], $item['scheduled_at'])) continue;

            Task::where('id', $item['task_id'])
                ->where('user_id', $this->userId)
                ->update([
                    'scheduled_at'     => $item['scheduled_at'],
                    'duration_minutes' => $item['duration_minutes'] ?? null,
                ]);
        }

        Log::info('GeneratePlanningJob completed', [
            'user_id' => $this->userId,
            'goal_id' => $this->goalId,
            'tasks_scheduled' => count($planning),
        ]);
    }

    public function failed(\Throwable $e): void
    {
        Log::error('GeneratePlanningJob failed', [
            'user_id' => $this->userId,
            'goal_id' => $this->goalId,
            'error'   => $e->getMessage(),
        ]);
    }
}
