<?php

namespace App\Providers;

use App\Jobs\PostCreate;
use App\Jobs\PostDelete;
use App\Jobs\TestJob;
use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        App::bindMethod(TestJob::class, function ($job) {
            return $job->handle();
        });

        App::bindMethod(PostCreate::class, function ($job) {
            return $job->handle();
        });

        App::bindMethod(PostDelete::class, function ($job) {
            return $job->handle();
        });
    }
}
