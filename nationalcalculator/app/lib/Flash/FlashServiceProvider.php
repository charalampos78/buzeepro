<?php

namespace My\Flash;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;

class FlashServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {

        // Register 'flash' instance container to our Flash object
        $this->app['flash'] = $this->app->share(function($app)
        {
            return \App::make('My\Flash\Flash');
            //return new Flash($app['session']);
        });


        // Shortcut so developers don't need to add an Alias in app/config/app.php
        $this->app->booting(function()
        {
            $loader = AliasLoader::getInstance();
            $loader->alias('Flash', 'My\Flash\Facade\Flash');
        });
    }
}