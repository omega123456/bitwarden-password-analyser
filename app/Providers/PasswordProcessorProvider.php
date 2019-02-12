<?php

namespace App\Providers;

use App\Processors\PasswordProcessor;
use Illuminate\Support\ServiceProvider;

class PasswordProcessorProvider extends ServiceProvider
{
    protected $defer = true;

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            PasswordProcessor::class,
            PasswordProcessor::getClass(request()->post('password_manager_type'))
        );
    }

    public function provides()
    {
        return [
            PasswordProcessor::class
        ];
    }
}
