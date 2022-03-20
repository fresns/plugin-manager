<?php

namespace $NAMESPACE$;

use Illuminate\Support\Str;
use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Scheduling\Schedule;

class ConsoleServiceProvider extends ServiceProvider
{
    /**
     * The available commands.
     *
     * @var array
     */
    protected array $commands = [
        //
    ];

    /**
     * Register any services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->resolving(Schedule::class, function ($schedule) {
            $this->schedule($schedule);
        });
    }

    /**
     * Prepare schedule from tasks.
     *
     * @param  Schedule  $schedule
     */
    public function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
    }

    public function register(): void
    {
        $this->commands($this->commands);
    }
}
