<?php

namespace My\MForm;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;

class MFormServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {

        // Register 'mform' instance container to our MForm object
//        $this->app['mform'] = $this->app->share(function($app)
//        {
//            return \App::make('My\MForm');
//        });

        $this->app->bindShared('mform', function($app)
        {
            $mform = new MForm($app['html'], $app['url'], $app['session.store']->getToken());

            return $mform->setSessionStore($app['session.store']);
        });


        // Shortcut so developers don't need to add an Alias in app/config/app.php
        $this->app->booting(function()
        {
            $loader = AliasLoader::getInstance();
            $loader->alias('MForm', 'My\MForm\Facade\MForm');
        });
    }
}