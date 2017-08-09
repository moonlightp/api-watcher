<?php

namespace App\Console;

use App\Console\Commands\Tester;
use App\Models\ScheduleRule;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Tester::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        foreach (ScheduleRule::get() as $rule) {
            $schedule
                ->command("watcher:execute {$rule->api_group_id}")
                ->cron($rule->cron_expression)
                ->when(function () use ($rule) {
                    return parse_schedule_condition($rule->cron_condition);
                })
                ->runInBackground();
        }
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
