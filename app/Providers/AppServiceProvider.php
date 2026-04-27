<?php

namespace App\Providers;

use App\Models\Event;
use App\Models\Goal;
use App\Models\Task;
use App\Policies\EventPolicy;
use App\Policies\GoalPolicy;
use App\Policies\TaskPolicy;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);

        $this->registerPolicies();
        $this->registerRateLimiters();
    }

    private function registerPolicies(): void
    {
        Gate::policy(Goal::class,  GoalPolicy::class);
        Gate::policy(Task::class,  TaskPolicy::class);
        Gate::policy(Event::class, EventPolicy::class);
    }

    private function registerRateLimiters(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // AI calls are expensive — limit to 10/min per user
        RateLimiter::for('ai', function (Request $request) {
            return Limit::perMinute(10)->by($request->user()?->id ?: $request->ip())
                ->response(fn() => response()->json([
                    'message' => 'Trop de requêtes IA. Veuillez patienter.',
                ], 429));
        });
    }
}
