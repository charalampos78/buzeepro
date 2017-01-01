<?php

namespace My\AuthHelper;

use Config;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Auth, Confide, Input, Models, Session;
use Request;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\ValidationData;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Signer\Rsa\Sha256;

/**
 * Class AuthHelper
 * This method is to extend Confide methods and add more functionality to Auth
 * Can't just extend confide because it has a lot of specific IoC DI stuff
 *
 * @package My
 *
 */
class AuthHelper {

    /**
     * @var Confide
     */
    public $confide;

    public function __construct()
    {
        $this->confide = Confide::getFacadeRoot();
    }

    public function loginStateless($token)
    {
        /** @var Models\Device $device */
        $device = Models\Device::where('auth_token', $token)->first();
        if (!$device) {
            return false;
        }

        $user_id = $device->user_id;

        $return = Auth::onceUsingId($user_id);

        if ($return && Auth::check()) {
            $user = Auth::user();
            $user->current_device = $device;
        }

        return $return;

    }

    public function createJWT() {

        $privateKey = new Key(file_get_contents(app_path('data/nc_remote_private.pem')));
        $signer = new Sha256();

        $token = new Builder();
        $token = $token->set('email', Auth::user()->email)
                                ->setId(uniqid(Auth::user()->id))
                                ->setIssuedAt(time())
                                ->setExpiration(time() + 60 * 60 * 2) // 2 hours
                                ->sign($signer, $privateKey)
                                ->getToken();
        return $token;
    }

    public function loginStatelessJWT()
    {
        $publicKey = new Key(file_get_contents(app_path('data/nc_remote_public.pem')));
        $signer = new Sha256();

        $jwt = null;
        if ($authHeader = Request::header('authorization')) {
			 //Extract the jwt from the Bearer
            list($jwt) = sscanf( $authHeader, 'Bearer %s');
        } elseif($authHeader = Input::get('jwt_token',null))  {
            $jwt  = $authHeader;
        }
        if (!$jwt) { return false; }

        try {
            $token = (new Parser())->parse($jwt);

            $validationData = new ValidationData();

            if (!$token->validate($validationData) || !$token->verify($signer, $publicKey)) {
                return false;
            }

        } catch (\Exception $exception) {
            return false;
        }

        $email = $token->getClaim('email');

        if ($email) {
            $user = Models\User::whereEmail($email)->first();
            if (!$user) {
                return false;
            }
            return Auth::onceUsingId($user->id);
        }

        return true;

    }

    public function logAttempt(array $input, $mustBeConfirmed = true)
    {

        $return = $this->confide->logAttempt($input, $mustBeConfirmed);

        if ($return) {
            $this->createDevice();
        }

        return $return;
    }

    public function createDevice($device_data = [])
    {

        if (empty($device_data)) {
            $device_data = Input::get('device', []);
        }

        /** @var Models\User $user */
        /** @var Models\Device $device */

        $user = Auth::user();

        $device_number = Arr::get($device_data, 'device_number');
        $device = $user->devices()->where('devices.device_number', $device_number)->first();
        if (!$device) $device = new Models\Device();
        $device->fill($device_data);

        if (empty($device_data['device_number'])) {
            $user->devices()->where('devices.device_number','LIKE',"session-%")->delete();
            $device->device_number = "session-".Session::getId();
        }
        if (empty($device_data['user_agent'])) {
            $device->user_agent = $_SERVER['HTTP_USER_AGENT'];
        }

        $device->user_id = $user->id;
        $device->auth_token = Str::random(60);

        if ($device->device_number) {
            $device->save();
        }

        $user->current_device = $device;

    }



    /**
     * Forward methods that aren't overwritten here to Confide
     * @param $method
     * @param $args
     *
     * @return mixed
     */
    public function __call($method, $args)
    {

        if (!method_exists($this, $method) && method_exists($this->confide, $method)) {
            return call_user_func_array(array($this->confide,$method), $args);
        }
        return call_user_func_array(array($this,$method), $args);

    }

    /**
     * Forward properties that aren't overwritten here to Confide
     * @param $name
     *
     * @return mixed
     */
    public function __get($name) {
        if (!property_exists($this, $name) && property_exists($this->confide, $name)) {
            return $this->confide->$name;
        }
        return $this->$name;
    }


}

class AuthHelperException extends \Exception {}