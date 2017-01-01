<?php

/*
|--------------------------------------------------------------------------
| Application & Route Filters
|--------------------------------------------------------------------------
|
| Below you will find the "before" and "after" events for the application
| which may be used to do any work before or after a request into your
| application. Here you may also register your custom route filters.
|
*/

use Controller\Exceptions\AuthenticationException;
use Controller\Exceptions\AccessDeniedException;
use Controller\Exceptions\CustomException;

App::before(function($request)
{
	//
    $token = $request->header('X-Auth-Token') ?: Input::get('auth_token');

    if ($token) {
        AuthHelper::loginStateless($token);
    }

});


App::after(function($request, $response)
{
	//
});


/**
 * This will log the user in statelessly via a JWT token
 */
Route::filter('authJWT', function($route, $request) {
    if (!AuthHelper::loginStatelessJWT()) {
        if ($route->getAction()['as'] === "loginJWT") {
            Flash::info("Issue with remote login, please login here");
            return Redirect::route('login');
        }
        throw new CustomException(["exceptionName"=>CustomException::JWT], 401, "Invalid JWT token");
    }

    Config::set('auth.jwt.authed_as', true);

});

/*
|--------------------------------------------------------------------------
| Authentication Filters
|--------------------------------------------------------------------------
|
| The following filters are used to verify that the user of the current
| session is logged into this application. The "basic" filter easily
| integrates HTTP Basic authentication for quick, simple checking.
|
*/


Route::filter('authorization', function($route, $request)
{
    $authRules = new \My\AuthRules($route);
    //$authRules = App::make('AuthRules');


    if (!$authRules->authorized()) {

        if ($authRules->message) {
            Flash::info($authRules->message);
        }

        if (!$request->ajax() && strpos($route->getUri(), "api") !== 0) {
            return Redirect::guest($authRules->redirectUri);
        } else {
            if (Auth::check()) {
                //logged in but denied
                throw new AccessDeniedException([
                                                    'flash_msg' => Flash::get_flash(),
                                                    'uri' => $authRules->redirectUri,
                                                ]);
            } else {
                //not logged in and denied
                throw new AuthenticationException([
                                                      'flash_msg' => Flash::get_flash(),
                                                      'uri' => '/login',
                                                  ]);
            }
        }
    }

});


//
//Route::filter('auth.basic', function()
//{
//	return Auth::basic();
//});

/*
|--------------------------------------------------------------------------
| Guest Filter
|--------------------------------------------------------------------------
|
| The "guest" filter is the counterpart of the authentication filters as
| it simply checks that the current user is not logged in. A redirect
| response will be issued if they are, which you may freely change.
|
*/

Route::filter('guest', function()
{
	if (Auth::check()) return Redirect::to('/');
});

/*
|--------------------------------------------------------------------------
| CSRF Protection Filter
|--------------------------------------------------------------------------
|
| The CSRF filter is responsible for protecting your application against
| cross-site request forgery attacks. If this special token in a user
| session does not match the one given in this request, we'll bail.
|
*/

Route::filter('csrf', function($route, $request) {

    if (   strtoupper($request->getMethod()) === 'GET'
        || App::environment('local')
    )
    {
        return; // get requests are not CSRF protected
    }

    $token = $request->header('X-CSRF-Token') ?: Input::get('_token');

    if (Session::token() != $token)
    {
        throw new CustomException(["exceptionName"=>CustomException::CSRF], 409, "Invalid CSRF token, please refresh page and try again");
    }

});


Route::filter('subscription', function($route, $request) {

    if (!Auth::user()->hasRole('admin') && !Auth::user()->subscribed()
        && $route->getActionName() != 'Controller\Frontend\MemberController@subscribe_invoice'
        && $route->getActionName() != 'Controller\Frontend\MemberController@subscribe'
        && $route->getActionName() != 'Controller\Frontend\MemberController@account'
        && $route->getActionName() != 'Controller\Frontend\MemberController@index'
    ) {
        Flash::notice("You must subscribe before you can use the rest of the site!");
        return Redirect::action('memberSubscribe');
    }

});

Route::filter('badRequest', function($route, $request, $response) {

    if ($response instanceof Illuminate\Http\Response) {
        $data = $response->getOriginalContent();

        if (is_array($data) && isset($data['success']) && $data['success'] == false) {
            $response->setStatusCode(400);
        }
    }

});

Route::filter('noDebug', function() {
        //disabled debug bar when in swagger
        Config::set('laravel-debugbar::config.enabled', false);
});
