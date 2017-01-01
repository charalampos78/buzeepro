<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| Local assets directories
	|--------------------------------------------------------------------------
	|
	| Override defaul prefix folder for local assets. They are relative to your
	| public folder. Don't use trailing slash!.
	|
	| Default for CSS: 'css'
	| Default for JS: 'js'
	*/

	'css_dir' => '/assets',
	'js_dir' => '/assets',

	/*
	|--------------------------------------------------------------------------
	| Assets collections
	|--------------------------------------------------------------------------
	|
	| Collections allow you to have named groups of assets (CSS or JavaScript files).
	|
	| If an asset has been loaded already it won't be added again. Collections may be
	| nested but please be careful to avoid recursive loops.
	|
	| To avoid conflicts with the autodetection of asset types make sure your
	| collections names don't end with ".js" or ".css".
	|
	|
	| Example:
	|	'collections' => array(
	|
	|		// jQuery (CDN)
	|		'jquery-cdn' => [
	|			'//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js
	|		'],
	|
	|		// Twitter Bootstrap (CDN)
	|		'bootstrap-cdn' => [
	|			'jquery-cdn',
	|			'//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css',
	|			'//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap-theme.min.css',
	|			'//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js'
	|		],
	|
	|		//Zurb Foundation (CDN)
	|		'foundation-cdn' => [
	|			'//cdn.jsdelivr.net/foundation/5.3.0/js/vendor/modernizr.js',
	|			'jquery2-cdn',
	|			'//cdn.jsdelivr.net/foundation/5.3.0/js/foundation.min.js',
	|			'app.js',
	|			'//cdn.jsdelivr.net/foundation/5.3.0/css/normalize.css',
	|			'//cdn.jsdelivr.net/foundation/5.3.0/css/foundation.min.css',
	|		],
	|	),
	*/

	'collections' => array(
        //base assets
        'base.pre' => [
            'js/libs/jquery-2.1.3/jquery-2.1.3.min.js',
            'css/vendor/bootstrap/javascripts/bootstrap.js',
            'js/libs/jquery-ui-1.11.0/jquery-ui.min.js',
            'js/libs/jquery-form-3.50/jquery.form.js',
            'js/libs/jquery-validation-1.13.0/jquery.validate.js',
            'js/libs/jquery-hideShowPassword-2.0.3/hideShowPassword.min.js',
			'select2',

            'css/vendor/bootstrap/bootstrap.css',
        ],
		'base.post' => [
			'js/microJS/microCore.js',
			'js/base.js',
		],
        'manage.pre' => [
            'js/libs/ckeditor-4.4.6-custom/ckeditor.js',
            'js/libs/ckeditor-4.4.6-custom/adapters/jquery.js',
            'js/libs/autosize-1.18.12/jquery.autosize.min.js',

            'dataTables',

			'css/manage.css',
        ],
		'manage.post' => [

		],
        'frontend' => [
			'css/site.css',
        ],


        /**
         * Plugin Specific
         */

        'dataTables' => [
            'js/libs/jquery-DataTables-1.10.2/media/js/jquery.dataTables.min.js',
            'js/libs/jquery-DataTables-1.10.2/extensions/TableTools/js/dataTables.tableTools.min.js',
            'js/libs/jquery-DataTables-1.10.2/extras/bootstrap/dataTables.bootstrap.js',

            //These css files are unnecessary with the dataTables bootstrap css
            //'js/libs/jquery-DataTables-1.10.2/media/css/jquery.dataTables.min.css',
            //'js/libs/jquery-DataTables-1.10.2/media/css/jquery.dataTables_themeroller.css',
            'js/libs/jquery-DataTables-1.10.2/extensions/TableTools/css/dataTables.tableTools.min.css',
            'js/libs/jquery-DataTables-1.10.2/extras/bootstrap/dataTables.bootstrap.css',
        ],
		'select2' => [
			'js/libs/jquery-select2-3.5.2/select2.min.js',
			'js/libs/jquery-select2-3.5.2/select2.css',
			'css/libs/select2-bootstrap.css',
		]
    ),

	/*
	|--------------------------------------------------------------------------
	| Preload assets
	|--------------------------------------------------------------------------
	|
	| Here you may set which assets (CSS files, JavaScript files or collections)
	| should be loaded by default even if you don't explicitly add them.
	|
	*/

	'autoload' => array(
        'base.pre',
        'base.post'
    ),

	/*
	|--------------------------------------------------------------------------
	| Assets pipeline
	|--------------------------------------------------------------------------
	|
	| When enabled, all your assets will be concatenated and minified to a sigle
	| file, improving load speed and reducing the number of requests that the
	| browser makes to render a web page.
	|
	| It's a good practice to enable it only on production environment.
	|
	| Use an integer value greather than 1 to append a timestamp to the URL.
	|
	| Default: false
	*/

	'pipeline' => false,

	/*
	|--------------------------------------------------------------------------
	| Pipelined assets directories
	|--------------------------------------------------------------------------
	|
	| Override defaul folder for storing pipelined assets. Relative to your
	| assets folder. Don't use trailing slash!.
	|
	| Default: 'min'
	*/

	'pipeline_dir' => 'min',

);
