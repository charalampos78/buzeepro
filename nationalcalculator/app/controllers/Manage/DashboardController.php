<?php

namespace Controller\Manage;

class DashboardController extends BaseManageController {

	/**
	 * Display a listing of the resource.
	 * GET /manage/dashboard
	 *
	 * @return \Response
	 */
	public function getIndex()
	{

        $title = "Dashboard Index";
        $this->layout->nest('body', 'manage.dashboard')->with(compact('title'));

        //$this->layout->title = "Dashboard Index";
        //$this->layout->content = \View::make('manage.dashboard');
    }

}