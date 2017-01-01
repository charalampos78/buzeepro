<?php

namespace Controller\Api;

use Auth;
use AuthHelper;
use Controller\Exceptions\InvalidObjectIdException;
use Controller\Exceptions\ValidationException;
use Config;
use DB;
use Datatables;
use Doctrine\DBAL\Query\QueryBuilder;
use Mockery\CountValidator\Exception;
use Models; //PHPStorm autocomplete wants this... :/
use Mail;
use Response;
use Input;
use Flash;
use Illuminate\Support\Arr;
use Swagger\Annotations as SWG;


/**
 * Class UserApi
 *
 * @SWG\Resource(
 *     resourcePath="/user",
 *     basePath="/api"
 * )
 *
 * @package Controller\Api
 */
class UserApi extends BaseApi {

	/**
	 * Display a listing of the resource.
	 *
     * @SWG\Api(
     *   path="/user",
     *   @SWG\Operation(
     *     method="GET",
     *     summary="Get logged in users data",
     *     type="User",
     *   )
     * )
     *
	 * @return Response
	 */
	public function getIndex()
	{
        /** @var Models\User $user */
        $user = Auth::user();
        return [
            'success'=>true,
            'results'=> [$user],
        ];

        //
	}


	/**
	 * Send dataTable data
	 *
	 * @return Response
	 */
	public function getDatatables()
	{
        $users = Models\User::select()->ModelJoin('profile');
        return $dataTables = Datatables::of($users)
            ->filterColumn('profile.last_name','where',\DB::raw('CONCAT(profile.last_name,\' \',profile.first_name)'),'LIKE','$1')
            ->filterColumn('created_at','where','users.created_at','LIKE','$1')
            ->editColumn('profile.last_name', '{{ $profile["first_name"]." ".$profile["last_name"] }}')
            ->editColumn('created_at', function($result_obj) {
                return $result_obj->created_at->format('d/m/Y - h:ia');
            })
            ->addColumn('manage', '{{ HTML::link("manage/user/edit/$id", "Edit", ["class"=>"btn btn-info"]) }}', 3)
            ->removeColumn('profile.photo_id')
            ->setIndexColumn('user-{{ $id }}')
            ->setRowData('user_id', '{{ $id }}')
            ->make();
	}

    /**
     * Create user
     *
     * @SWG\Api(
     *   path="/user",
     *   @SWG\Operation(
     *     method="POST",
     *     summary="Create user",
     *     type="User",
     *     authorizations="apiKey",
     *     @SWG\Parameter(
     *       name="body",
     *       description="user and profile data, all fields required",
     *       defaultValue="
{
    ""user"" : {
        ""username"": """",
        ""email"": """",
        ""password"": ""password"",
        ""profile"": {
            ""first_name"": """",
            ""last_name"": """"
        }
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
     */
    public function postIndex() {

        $user = new Models\User;
        //$profile = new Models\Profile;
        //$profile->user()->associate($user);
        //$user->profile()->save(new Models\Profile);

        return $this->credate($user);
    }

    /**
     * Update user
     *
     *
     * @SWG\Api(
     *   path="/user/{user_id}",
     *   @SWG\Operation(
     *     method="PUT",
     *     summary="Update user",
     *     type="User",
     *     authorizations="apiKey",
     *     @SWG\Parameter(
     *       name="user_id",
     *       description="ID of user to be updated",
     *       required=true,
     *       type="string",
     *       paramType="path",
     *     ),
     *     @SWG\Parameter(
     *       name="body",
     *       description="user and profile data, all fields optional",
     *       defaultValue="
{
    ""user"" : {
        ""username"": """",
        ""email"": """",
        ""password"": """",
        ""profile"": {
            ""first_name"": """",
            ""last_name"": """"
        }
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
     */
    public function putIndex($user) {

        if (!Auth::user()->hasRole('admin')) {
            $user = Auth::user();
        }

        return $this->credate($user);

    }

    /**
     * Method to both create and update user
     *
     * @param $user Models\User
     *
     * @return array
     */
    protected function credate($user) {
        DB::beginTransaction();

        $inputs = Input::get('user', []);
        if (empty($inputs['password'])) {
            unset($inputs['password']);
        }

        $newUser = !$user->exists;

        $user->fill($inputs);
		if ($user->email && !$user->username) {
			$user->username = $user->email;
		}

        if ($user->isInvalid()) {
            $this->setFormErrors('user', $user->getErrors());
        } else {

			if ($newUser && Config::get('auth.jwt.authed_as')) {
				$user->remote_created_flag = true;
			}
            //need to save early so id is available for $profile and $roles
            $user->save();
			if (Arr::get($inputs,'roles',false) && Auth::check() && Auth::user()->hasRole('admin')) {
				$user->updateRoles(Arr::get($inputs,'roles',[]));
			} elseif ($newUser) {
				$user->updateRoles([Models\Role::LOGIN]);
			}
        }

        if ($newUser && !$inputs['terms']) {
            $this->setFormErrors('user.terms', "Must agree to terms");
        }

        if ($user->profile) {
            $profile = $user->profile;
        } else {
            $profile = new Models\Profile;
        }

        $profile->fill(Arr::get($inputs, 'profile', []));

        if ($user->isValid()) {
            //$profile->user()->associate($user); //can't do this or infinite loop with $user->profile->user->profile->...etc when converting to array/json
            $user->profile()->save($profile);
            if (!$user->profile) {
                $user->setRelation('profile', $profile);
            }
        }

        if ($profile->isInvalid()) {
            $this->setFormErrors('user.profile', $profile->getErrors());
        }

        if (empty($this->formErrors)) {

            DB::commit();

			if (!Auth::check()) {
				//If user not logged in, then log them in.
				Auth::loginUsingId($user->id);
                AuthHelper::createDevice();
			}
			if (Auth::user()->hasRole('admin')) {
				$uri = \URL::action('Controller\Manage\UsersController@getIndex');
			} else {

                if ($newUser) {
                    Flash::success('Account created successfully!');

					Mail::send('emails.signup', ["user" => $user], function($message) use ($user)
					{
						$message
							->from(Config::get('app.contact'), Config::get('app.name'))
							->to($user->email, $user->profile->first_name)
							->subject("Welcome to National Calculator");

					});

				} else {
                    Flash::success('Account has been updated!');
                }

				$uri = \URL::route('memberIndex');
			}

            $auth_token = (isset(Auth::user()->current_device) ? Auth::user()->current_device->auth_token : null);

            return [
                'success' => true,
                //'flash_msg' => Flash::get_flash(),
                'auth_token' => $auth_token,
                'uri' => $uri,
                'results' => [$user],
            ];
        } else {
            DB::rollBack();
            throw new ValidationException(['errors' => $this->formErrors]);
        }
    }

    public function deleteIndex($id)
    {

    }

}
