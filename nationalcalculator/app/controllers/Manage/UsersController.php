<?php

namespace Controller\Manage;

use Illuminate\Support\Facades\Form;
use My\Mform;

class UsersController extends BaseManageController {

    /**
     * Display a listing of the resource.
     * GET /manage/dashboard
     *
     * @return \Response
     */
    public function getIndex()
    {
        $this->layout->nest('body', 'manage.users');
    }

    public function getAdd()
    {
        $user = new \Models\User;
        $this->layout->nest('body', 'manage.usersAddit', compact('user'));
    }

    public function getEdit($user)
    {
//        if (!$user) {
//            \Flash::info('User not found');
//            return \Redirect::action('Controller\Manage\UsersController@getIndex');
//        }
        $this->layout->nest('body', 'manage.usersAddit', compact('user'));
    }

}