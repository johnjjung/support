<?php

/**
 * @package     Support
 * @author      Ian Olson <me@ianolson.io>
 * @license     MIT
 * @copyright   2015, Ian Olson
 */

namespace IanOlson\Support\Providers;

use IanOlson\Support\Commands\GenerateModel;
use Illuminate\Support\ServiceProvider;

class SupportServiceProvider extends ServiceProvider
{
    /**
     * {@inheritDoc}
     */
    public function boot()
    {
        // Publish migrations.
        $migrations = realpath(__DIR__ . '/../Database/Migrations');

        $this->publishes([
          $migrations => database_path('/migrations'),
        ], 'migrations');
    }

    /**
     * {@inheritDoc}
     */
    public function register()
    {
        $this->setupConsoleCommands();
    }

    /**
     * Register the console commands to the application.
     *
     * Register the following commands:
     * - iolson:model
     */
    protected function setupConsoleCommands()
    {
        // Share iolson:model command with the application.
        $this->app['iolson::model'] = $this->app->share(function () {
            return new GenerateModel();
        });

        // Adds iolson:model to the console kernel.
        $this->commands('iolson::model');
    }
}