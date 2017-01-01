<?php

namespace Controller;

use App, Config, Input, Lang, Mail, Response, Redirect, View;
use AuthHelper;


/**
 * UsersController Class
 *
 * Implements actions regarding user management
 */
class UsersController extends \Controller {

    /**
     * Displays the form for account creation
     * @return  \Illuminate\Http\Response
     */
    public function getCreate()
    {
        return View::make(Config::get('confide::signup_form'));
    }

    public function getIndex() {
        if (AuthHelper::user()) {
            return AuthHelper::user()->toArray();
        } else {
            return Response::json([
                'error' => 'not logged in',
            ]);
        }
    }


    /**
     * Stores new account
     * @return  \Illuminate\Http\Response
     */
    public function postIndex()
    {
        /** @var \UserRepository $repo */
        $repo = App::make('UserRepository');
        $user = $repo->signup(Input::all());

        if ($user->id)
        {
            Mail::send(Config::get('confide::email_account_confirmation'), compact('user'), function($message) use ($user) {
                $message
                    ->to($user->email, $user->username)
                    ->subject(Lang::get('confide::confide.email.account_confirmation.subject'));
            });

            return Redirect::action('UsersController@getLogin')
                ->with( 'notice', Lang::get('confide::confide.alerts.account_created') );
        }
        else
        {
            $error = $user->errors()->all(':message');

            return Redirect::action('UsersController@getCreate')
                ->withInput(Input::except('password'))
                ->with( 'error', $error );
        }
    }

    /**
     * Displays the login form
     * @return  \Illuminate\Http\Response
     */
    public function getLogin()
    {
        if( AuthHelper::user() )
        {
            return Redirect::to('/');
        }
        else
        {
            return View::make(Config::get('confide::login_form'));
        }
    }

    /**
     * Attempt to do login
     * @return  \Illuminate\Http\Response
     */
    public function postLogin()
    {

        /** @var \UserRepository $repo */
        $repo = App::make('UserRepository');
        $input = Input::all();

        if ($repo->login($input))
        {
            return Redirect::intended('/');
        }
        else
        {
            if ($repo->isThrottled($input))
            {
                $err_msg = Lang::get('confide::confide.alerts.too_many_attempts');
            }
            elseif ($repo->existsButNotConfirmed($input))
            {
                $err_msg = Lang::get('confide::confide.alerts.not_confirmed');
            }
            else
            {
                $err_msg = Lang::get('confide::confide.alerts.wrong_credentials');
            }

            return Redirect::action('UsersController@getLogin')
                ->withInput(Input::except('password'))
                ->with( 'error', $err_msg );
        }
    }

    /**
     * Attempt to confirm account with code
     * @param    string  $code
     * @return  Illuminate\Http\Response
     */
    public function getConfirm( $code = null )
    {
        if ( AuthHelper::confirm( $code ) )
        {
            $notice_msg = Lang::get('confide::confide.alerts.confirmation');
            return Redirect::action('UsersController@getLogin')
                ->with( 'notice', $notice_msg );
        }
        else
        {
            $error_msg = Lang::get('confide::confide.alerts.wrong_confirmation');
            return Redirect::action('UsersController@getLogin')
                ->with( 'error', $error_msg );
        }
    }

    /**
     * Displays the forgot password form
     * @return  \Illuminate\Http\Response
     */
    public function getForgot()
    {
        return View::make(Config::get('confide::forgot_password_form'));
    }

    /**
     * Attempt to send change password link to the given email
     * @return  \Illuminate\Http\Response
     */
    public function postForgot()
    {
        if( AuthHelper::forgotPassword( Input::get( 'email' ) ) )
        {
            $notice_msg = Lang::get('confide::confide.alerts.password_forgot');
            return Redirect::action('UsersController@getLogin')
                ->with( 'notice', $notice_msg );
        }
        else
        {
            $error_msg = Lang::get('confide::confide.alerts.wrong_password_forgot');
            return Redirect::action('UsersController@getForgot')
                ->withInput()
                ->with( 'error', $error_msg );
        }
    }

    /**
     * Shows the change password form with the given token
     * @return  \Illuminate\Http\Response
     */
    public function getReset( $token = null )
    {
        return View::make(Config::get('confide::reset_password_form'))
                ->with('token', $token);
    }

    /**
     * Attempt change password of the user
     * @return  \Illuminate\Http\Response
     */
    public function postReset()
    {
        /** @var \UserRepository $repo */
        $repo = App::make('UserRepository');
        $input = array(
            'token'                 =>Input::get( 'token' ),
            'password'              =>Input::get( 'password' ),
//            'password_confirmation' =>Input::get( 'password_confirmation' ),
        );

        // By passing an array with the token, password and confirmation
        if( $repo->resetPassword( $input ) )
        {
            $notice_msg = Lang::get('confide::confide.alerts.password_reset');
            return Redirect::action('UsersController@getLogin')
                ->with( 'notice', $notice_msg );
        }
        else
        {
            $error_msg = Lang::get('confide::confide.alerts.wrong_password_reset');
            return Redirect::action('UsersController@getReset', array('token'=>$input['token']))
                ->withInput()
                ->with( 'error', $error_msg );
        }
    }

    /**
     * Log the user out of the application.
     * @return  \Illuminate\Http\Response
     */
    public function getLogout()
    {
        AuthHelper::logout();

        return Redirect::to('/');
    }

}
