<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| Application Debug Mode
	|--------------------------------------------------------------------------
	|
	| When your application is in debug mode, detailed error messages with
	| stack traces will be shown on every error that occurs within your
	| application. If disabled, a simple generic error page is shown.
	|
	*/

	'debug' => true,

	# https://github.com/barryvdh/laravel-ide-helper/issues/26#issuecomment-33502166
	# https://github.com/laravel/framework/issues/3327
	'providers' => append_config(array(
		'Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider',
		'Barryvdh\Debugbar\ServiceProvider',
		'Way\Generators\GeneratorsServiceProvider',
		'Xethron\MigrationsGenerator\MigrationsGeneratorServiceProvider',
	)),

	'aliases' => array(
		'Debugbar' => 'Barryvdh\Debugbar\Facade',
	),

);
