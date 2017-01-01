<?php

namespace Controller\Api;

use App, Auth, DB, Config, Hash, Input;
use AuthHelper;
use Controller\Exceptions\CustomException;
use Controller\Exceptions\ValidationException;
use Flash;
use Password;
use Swagger\Annotations as SWG;
use Illuminate\Auth\Reminders\DatabaseReminderRepository;
use Models;

/**
 * Class LoginApi
 *
 * @SWG\Resource(
 *     resourcePath="/login",
 *     basePath="/api",
 *     authorizations="apiKey",
 *     @SWG\Produces("application/json"),
 *     @SWG\Consumes("application/json")
 * )
 *
 * @package Controller\Api
 */
class LoginApi extends BaseApi {

	/**
	 * Displays logged in users info
	 *
	 * @return \Response
	 */
	public function getIndex()
	{
        if (Auth::check()) {
            return Auth::user();
        } else {
            App::abort(403, 'User not found');
        }
	}


	/**
	 * Creates user login
	 *
     * @SWG\Api(
     *   path="/login",
     *   @SWG\Operation(
     *     method="POST",
     *     summary="Login user",
     *     type="User",
     *     authorizations="apiKey",
     *     @SWG\Parameter(
     *       name="body",
     *       description="login and device info",
     *       defaultValue="
{
    ""login"" : {
        ""username"": ""username"",
        ""password"": ""password""
    },
    ""device"" : {
        ""device_number"" : ""test-device"",
        ""type"" : ""android,ios,windows"",
        ""push_token"" : """"
    }
}",
     *       required=true,
     *       type="json",
     *       paramType="body",
     *       allowMultiple=false
     *     )
     *   )
     * )
     *
	 * @return \Response
	 */
	public function postIndex()
	{
        $input = Input::get('login', []);
        if ( AuthHelper::logAttempt($input, Config::get('confide::signup_confirm')) ) {
            return [
                'success' => true,
                'user' => Auth::user(),
                'auth_token' => Auth::user()->current_device->auth_token,
                'uri' => \Session::pull('url.intended', function() { return Auth::user()->getDefaultUrl(); } ),
            ];
        } else {

            if ( AuthHelper::isThrottled($input)) {
                throw new CustomException(["exceptionName"=>CustomException::THROTTLE], 429, "Too many login attempts, please try again in ". Config::get('confide::throttle_time_period'). " minutes");
            } else {
                $user = AuthHelper::getUserByEmailOrUsername($input);
                $user && $correctPassword = Hash::check( isset($input['password']) ? $input['password'] : false, $user->password );
                if ($user && $correctPassword && !$user->confirmed) {
                    $message = "You must verify your account before logging in";
                } else {
                    $message = "Invalid username or password";
                }
            }

            throw new ValidationException(['errors' => ['login'=>['email'=>$message]]]);

        }

	}


	/**
	 * Searches for email/username and creates recovery token
	 *
     * @SWG\Api(
     *   path="/login/forgot",
     *   @SWG\Operation(
     *     method="POST",
     *     summary="generate recovery token that's emailed to the user",
     *     @SWG\Produces("application/json"),
     *     @SWG\Parameter(
     *       name="body",
     *       description="email or username of account to add",
     *       defaultValue="
{
""login"" : {
    ""email"" : """"
}",
     *       required=true,
     *       type="json",
     *       paramType="body",
     *       allowMultiple=false
     *     )
     *   )
     * )
     *
	 * @return \Response
	 */
	public function postForgot()
	{
		$mailCallback = function($message, $user, $token) {
			$message
				->from(Config::get('app.contact'), Config::get('app.name'))
				->to($user->email, $user->profile->first_name)
				->subject("National Calculator Password Recover");
		};


		/** @var \My\AuthHelper\AuthHelper $authHelper */
		$response = Password::remind(["email" => Input::get('login.email')], $mailCallback);
		if ($response == Password::INVALID_USER) {
			//try by username instead
			$response = Password::remind(["username" => Input::get('login.email')], $mailCallback);
		}
		if ($response == Password::REMINDER_SENT) {
            return [
                'success' => true,
            ];
        } else {
            throw new ValidationException(['errors' => ['login'=>['email'=>"Sorry, couldn't find a matching account in the system"]]]);
        }
	}


	/**
	 * Searches for recovery token and resets password
	 *
     * @param  string $token
	 * @return \Response
	 */
	public function postRecover()
	{
		$inputs = Input::get('recover');

		$expires = Config::get('auth.reminder.expire');
		$reminder = Models\PasswordReminder::whereToken($inputs['token'])->where("created_at", ">", DB::raw("NOW() - INTERVAL $expires MINUTE"))->first();
		if (!$reminder) {
			$this->setFormErrors("recover.password", "Sorry, invalid or expired token");
		}

		if (empty($this->formErrors)) {
			$user = Models\User::whereEmail($reminder->email)->first();
			if (!$user) {
				$this->setFormErrors("recover.password", "Can't seem to find your email in the system");
			}
		}

		if (empty($this->formErrors)) {
			$user->password = $inputs['password'];
			if ($user->isInvalid()) {
				$this->setFormErrors('recover', $user->getErrors());
			} else {
				$user->save();
			}
		}

		if (empty($this->formErrors)) {

			Models\PasswordReminder::whereEmail($user->email)->delete();

			Flash::success('Your password has been successfully updated!');

			return [
				'success' => true,
			];
		} else {
			throw new ValidationException(['errors' => $this->formErrors]);
		}

	}


	/**
	 * Logout
	 *
     * @SWG\Api(
     *   path="/login/destroy",
     *   @SWG\Operation(
     *     method="DELETE",
     *     summary="Logout user",
     *     @SWG\Produces("application/json"),
     *   )
     * )
     *
	 * @return \Response
	 */
	public function anyDestroy()
	{
		Auth::logout();
        return [
            'success' => true
        ];
	}


}
