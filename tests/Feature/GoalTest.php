<?php

namespace Tests\Feature;

use App\Models\Goal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class GoalTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_unauthenticated_user_cannot_access_goals(): void
    {
        $this->getJson('/api/v1/goals')
            ->assertUnauthorized();
    }

    public function test_user_can_list_own_goals(): void
    {
        Goal::factory()->count(3)->create(['user_id' => $this->user->id]);
        Goal::factory()->create(); // autre utilisateur

        Sanctum::actingAs($this->user);

        $this->getJson('/api/v1/goals')
            ->assertOk()
            ->assertJsonCount(3, 'data');
    }

    public function test_user_can_create_a_goal(): void
    {
        Sanctum::actingAs($this->user);

        $this->postJson('/api/v1/goals', [
            'title'    => 'Apprendre Vue.js',
            'deadline' => now()->addMonths(2)->toDateString(),
        ])
        ->assertCreated()
        ->assertJsonPath('data.title', 'Apprendre Vue.js');

        $this->assertDatabaseHas('goals', [
            'title'   => 'Apprendre Vue.js',
            'user_id' => $this->user->id,
        ]);
    }

    public function test_goal_requires_title(): void
    {
        Sanctum::actingAs($this->user);

        $this->postJson('/api/v1/goals', ['description' => 'Sans titre'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['title']);
    }

    public function test_user_can_update_own_goal(): void
    {
        $goal = Goal::factory()->create(['user_id' => $this->user->id, 'title' => 'Ancien titre']);

        Sanctum::actingAs($this->user);

        $this->patchJson("/api/v1/goals/{$goal->id}", ['title' => 'Nouveau titre'])
            ->assertOk()
            ->assertJsonPath('data.title', 'Nouveau titre');
    }

    public function test_user_cannot_update_another_users_goal(): void
    {
        $otherGoal = Goal::factory()->create();

        Sanctum::actingAs($this->user);

        $this->patchJson("/api/v1/goals/{$otherGoal->id}", ['title' => 'Hack'])
            ->assertForbidden();
    }

    public function test_user_can_delete_own_goal(): void
    {
        $goal = Goal::factory()->create(['user_id' => $this->user->id]);

        Sanctum::actingAs($this->user);

        $this->deleteJson("/api/v1/goals/{$goal->id}")
            ->assertNoContent();

        $this->assertDatabaseMissing('goals', ['id' => $goal->id]);
    }

    public function test_user_cannot_delete_another_users_goal(): void
    {
        $otherGoal = Goal::factory()->create();

        Sanctum::actingAs($this->user);

        $this->deleteJson("/api/v1/goals/{$otherGoal->id}")
            ->assertForbidden();
    }

    public function test_goal_stats_return_correct_counts(): void
    {
        $goal = Goal::factory()->create(['user_id' => $this->user->id]);
        $goal->tasks()->createMany([
            ['title' => 't1', 'status' => 'done', 'user_id' => $this->user->id, 'priority' => 'medium'],
            ['title' => 't2', 'status' => 'done', 'user_id' => $this->user->id, 'priority' => 'medium'],
            ['title' => 't3', 'status' => 'todo', 'user_id' => $this->user->id, 'priority' => 'medium'],
        ]);

        Sanctum::actingAs($this->user);

        $this->getJson("/api/v1/goals/{$goal->id}/stats")
            ->assertOk()
            ->assertJson([
                'total_tasks'     => 3,
                'completed_tasks' => 2,
                'completion_rate' => 67,
            ]);
    }
}
