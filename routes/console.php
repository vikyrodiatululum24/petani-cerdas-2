<?php

use App\Models\Subscription;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Symfony\Component\Console\Command\Command;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('subscriptions:expire {--dry-run : Simulate status sync without updating database}', function () {
    $today = now()->toDateString();

    $activateQuery = Subscription::query()
        ->where('status', 'pending')
        ->whereNotNull('start_date')
        ->whereDate('start_date', '<=', $today)
        ->where(function ($query) use ($today): void {
            $query->whereNull('end_date')
                ->orWhereDate('end_date', '>=', $today);
        });

    $expireQuery = Subscription::query()
        ->where('status', 'active')
        ->whereNotNull('end_date')
        ->whereDate('end_date', '<', $today);

    $pendingToActiveCount = (clone $activateQuery)->count();
    $activeToInactiveCount = (clone $expireQuery)->count();

    if ((bool) $this->option('dry-run')) {
        $this->info("[DRY-RUN] {$pendingToActiveCount} subscription(s) will be set to active.");
        $this->info("[DRY-RUN] {$activeToInactiveCount} subscription(s) will be set to inactive.");

        return Command::SUCCESS;
    }

    $activated = $activateQuery->update([
        'status' => 'active',
        'updated_at' => now(),
    ]);

    $expired = $expireQuery->update([
        'status' => 'inactive',
        'updated_at' => now(),
    ]);

    $this->info("Done. {$activated} subscription(s) set to active.");
    $this->info("Done. {$expired} subscription(s) set to inactive.");

    return Command::SUCCESS;
})->purpose('Sync subscriptions status by date (pending->active, active->inactive)');

Schedule::command('subscriptions:expire')
    ->dailyAt('00:05')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping();
