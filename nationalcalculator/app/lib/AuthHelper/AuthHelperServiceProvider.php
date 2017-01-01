<?php

namespace My\AuthHelper;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;

class AuthHelperServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {

        // Register 'authHelper' instance container to our AuthHelper object
        $this->app['authHelper'] = $this->app->share(function($app)
        {
            //return \App::make('My\AuthHelper');
            return new AuthHelper();
        });


        // Shortcut so developers don't need to add an Alias in app/config/app.php
        $this->app->booting(function()
        {
            $loader = AliasLoader::getInstance();
            $loader->alias('AuthHelper', 'My\AuthHelper\Facade\AuthHelper');
        });
    }
}