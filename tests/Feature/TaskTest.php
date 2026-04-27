<?php

namespace Tests\Feature;

use App\Models\Goal;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_user_can_list_own_tasks(): void
    {
        Task::factory()->count(4)->create(['user_id' => $this->user->id]);
        Task::factory()->count(2)->create(); // autre utilisateur

        Sanctum::actingAs($this->user);

        $this->getJson('/api/v1/tasks')
            ->assertOk()
            ->assertJsonCount(4, 'data');
    }

    public function test_tasks_are_filterable_by_status(): void
    {
        Task::factory()->create(['user_id' => $this->user->id, 'status' => 'done']);
        Task::factory()->create(['user_id' => $this->user->id, 'status' => 'todo']);
        Task::factory()->create(['user_id' => $this->user->id, 'status' => 'todo']);

        Sanctum::actingAs($this->user);

        $this->getJson('/api/v1/tasks?status=todo')
            ->assertOk()
            ->assertJsonCount(2, 'data');
    }

    public function test_user_can_create_task(): void
    {
        Sanctum::actingAs($this->user);

        $this->postJson('/api/v1/tasks', [
            'title'    => 'Rédiger les specs',
            'priority' => 'high',
        ])
        ->assertCreated()
        ->assertJsonPath('data.title', 'Rédiger les specs')
        ->assertJsonPath('data.priority', 'high');
    }

    public function test_task_cannot_be_linked_to_another_users_goal(): void
    {
        $otherGoal = Goal::factory()->create();

        Sanctum::actingAs($this->user);

        $this->postJson('/api/v1/tasks', [
            'title'   => 'Tâche piratée',
            'goal_id' => $otherGoal->id,
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['goal_id']);
    }

    public function test_user_can_mark_task_as_done(): void
    {
        $task = Task::factory()->create(['user_id' => $this->user->id, 'status' => 'todo']);

        Sanctum::actingAs($this->user);

        $this->patchJson("/api/v1/tasks/{$task->id}", ['status' => 'done'])
            ->assertOk()
            ->assertJsonPath('data.status', 'done');
    }

    public function test_user_cannot_access_another_users_task(): void
    {
        $otherTask = Task::factory()->create();

        Sanctum::actingAs($this->user);

        $this->getJson("/api/v1/tasks/{$otherTask->id}")
            ->assertForbidden();
    }

    public function test_user_can_delete_task(): void
    {
        $task = Task::factory()->create(['user_id' => $this->user->id]);

        Sanctum::actingAs($this->user);

        $this->deleteJson("/api/v1/tasks/{$task->id}")
            ->assertNoContent();

        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }
}
