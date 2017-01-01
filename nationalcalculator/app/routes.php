<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

use Controller\Exceptions\RedirectException;

/*
| Bind models first.  Binded models are global, so don't need to be within a group
*/
foreach (['user', 'content', 'state', 'zip', 'county', 'notebook'] as $model) {
    Route::model($model, 'Models\\' . ucwords($model), function () use ($model) {

        $data = [];
        $object_id = Route::input($model);
        if ($object_id) {
            $data['id'] = $object_id;
        }

        if (!Auth::check()) {
            //this binding is run before before-filters since it has to compile route first
            //so need to validate Authentication here as well
            throw new \Controller\Exceptions\AuthenticationException;
        } elseif (Auth::user()->hasRole('admin')) {
            $pluralModel = Str::plural($model);
            try {
                $data['url'] = URL::action('Controller\Manage\\'.ucwords($pluralModel).'Controller@getIndex');
            } catch (\Exception $e) {
                try {
                    $data['url'] = URL::action('Controller\Manage\\DataController@get'.ucwords($pluralModel));
                } catch (\Exception $e) {}
            }
        }
        throw new \Controller\Exceptions\InvalidObjectIdException($data);
    });
}


Route::group(['namespace' => 'Controller\Frontend'], function() {
    Route::get('/', ['as'=>'home', 'uses'=>'PageController@home']);
    Route::get('/about', ['as'=>'about', 'uses'=>'PageController@about']);
    Route::get('/calculators', ['as'=>'calculators', 'uses'=>'PageController@calculators']);
    Route::get('/membership', ['as'=>'membership', 'uses'=>'PageController@membership']);
    Route::get('/contact', ['as'=>'contact', 'uses'=>'PageController@contact']);
    Route::get('/registered', ['as'=>'registered', 'uses'=>'PageController@registered']);
	Route::get('/page/{page}', ['as'=>'page', 'uses'=>'PageController@page']);
});


Route::group(['before'=>['authorization']], function() {

    Route::get('elfinder', 'Barryvdh\Elfinder\ElfinderController@showIndex');
    Route::any('elfinder/connector', 'Barryvdh\Elfinder\ElfinderController@showConnector');
    Route::get('elfinder/ckeditor4', 'Barryvdh\Elfinder\ElfinderController@showCKeditor4');

    Route::group(['namespace' => 'Controller'], function() {
        Route::get('login/recover/{token?}', ['as'=>'recover', 'uses'=> 'AccountController@recover']);
        Route::get('login', ['as'=>'login', 'uses'=> 'AccountController@login']);
        Route::get('jwt', ['as'=>'getJWT', 'uses'=> 'AccountController@getJWT']);
        Route::get('postman', ['as'=>'getPostman', 'uses'=> 'AccountController@getPostman']);
        Route::get('logout', ['as'=>'logout', 'uses'=> 'AccountController@logout']);
        Route::get('register', ['as'=>'register', 'uses'=> 'AccountController@register']);
    });

    Route::group(['before'=>['csrf'], 'after'=>'badRequest', 'prefix'=>'api', 'namespace' => 'Controller\Api'], function() {


		Route::post('/county/copy/{county}', 'CountyApi@postCopy');

		//Place all API items in foreach array
        foreach (['user', 'content', 'state', 'zip', 'county', 'notebook'] as $model) {
            Route::put('/'.$model.'/{'.$model.'}', ucwords($model).'Api@putIndex');
            Route::delete('/'.$model.'/{'.$model.'}', ucwords($model).'Api@deleteIndex');
            Route::controller($model, ucwords($model).'Api');
        }

		Route::get('export/{notebook}/{output}', 'ExportApi@getIndex');
		Route::post('export/{notebook}', 'ExportApi@postIndex');
		Route::get('export/FDF', 'ExportApi@getFDF');

        Route::controller('contact', "ContactApi");
        Route::controller('subscribe', "SubscribeApi");

        //Route::controller('calculator', 'CalculateApi');
        Route::controller('login', 'LoginApi');

    });

    Route::group(['before'=>['subscription'], 'prefix'=>'members', 'namespace' => 'Controller\Frontend'], function() {

        Route::get('/', ['as'=>'memberIndex', 'uses'=>'MemberController@index']);
        Route::get('/account', ['as'=>'memberAccount', 'uses'=>'MemberController@account']);
        Route::get('/subscribe/invoice/{invoice_id}', ['as'=>'memberSubscribeInvoice', 'uses'=>'MemberController@subscribe_invoice']);
        Route::get('/subscribe', ['as'=>'memberSubscribe', 'uses'=>'MemberController@subscribe']);
        Route::get('/notebook', ['as'=>'memberNotebook', 'uses'=>'MemberController@notebook']);
		Route::get('/calculated/{notebook}', ['as'=>'memberCalculated', 'uses'=>'MemberController@calculated']);
        Route::get('/calculator/{notebook?}', ['as'=>'memberCalculator', 'uses'=>'MemberController@calculator']);
        Route::get('/calculator/docs/{county}', ['uses'=>'MemberController@calcDocs']);
        Route::get('/calculator/note/{county}', ['uses'=>'MemberController@calcNote']);
        Route::get('/calculator/endorsements/{state}', ['uses'=>'MemberController@calcEndorsements']);
        Route::get('/calculator/misc/{state}', ['uses'=>'MemberController@calcMisc']);

    });

    Route::group(['prefix'=>'manage', 'namespace' => 'Controller\Manage'], function() {

        Route::get('/', 'DashboardController@getIndex');
        Route::controller('/dashboard', 'DashboardController');
        Route::controller('/import', 'ImportController');

        Route::get('/data/state-edit/{state}', 'DataController@getStateEdit');
        Route::get('/data/county-edit/{county}', 'DataController@getCountyEdit');
        Route::get('/data/zip-edit/{zip}', 'DataController@getZipEdit');
        Route::controller('/data', 'DataController');

		//Place all CRUD items in foreach array
        foreach (['user', 'content'] as $model) {
            Route::get('/'.$model.'/edit/{'.$model.'}', ucwords($model).'sController@getEdit');
            Route::get('/'.$model.'/add', ucwords($model).'sController@getAdd');
            Route::get('/'.$model.'/edit', function() { return Redirect::action('Controller\Manage\\'.ucwords($model).'sController@getIndex'); });
            Route::get('/'.$model.'', ucwords($model).'sController@getIndex');
        }

    });

});

/**
 * Routes validated with JWT for remote access to system
 */
//Route::group(['before'=>['authJWT']], function() {
//
//    Route::group(['namespace' => 'Controller'], function() {
//        Route::get('login-remote/{notebook_id?}', ['as'=>'loginJWT', 'uses'=> 'AccountController@loginJWT']);
//    });
//
//    Route::group(['after'=>'badRequest', 'prefix'=>'api-jwt', 'namespace' => 'Controller\Api'], function() {
//
//        //create user
//        Route::post('user', 'UserApi@postIndex');
//
//        //create & update notebooks
//        Route::post('notebook', 'NotebookApi@postIndex');
//        Route::put('notebook/{notebook}', 'NotebookApi@putIndex');
//        Route::get('notebook', 'NotebookApi@getIndex');
//
//        Route::post('documents', 'CountyApi@postDocuments');
//        Route::post('endorsements', 'StateApi@postEndorsements');
//
//    });
//
//});



Route::post(
    'webhook/stripe', Laravel\Cashier\Http\Controllers\WebhookController::class.'@handleWebhook'
);

Route::when('api-docs*', 'noDebug');


// Confide RESTful route
Route::get('users/confirm/{code}',          'Controller\UsersController@getConfirm');

Route::controller( 'users', Controller\UsersController::class);




//
//Route::get('/admin/{controller?}/{action?}', function($controller='Index', $action='index'){
//    $controller = ucfirst($controller);
//    $action = $action . 'Action';
//    return App::make("Admin\\{$controller}Controller")->$action();
//});
//


//Route::get('/account/test', 'Controller\AccountController@test');
//
//Route::group(['before'=>['authorization']], function() {
//
//    //generic catch all controller for "Action" methods
//    Route::get('/{controller?}/{action?}', function ($controller = 'Index', $action = 'index') {
//        $controller = ucfirst($controller);
//        $action = $action . 'Action';
//
//        return App::make("Controller\\{$controller}Controller")->callAction($action, Route::input(null));
//    });
//
//});