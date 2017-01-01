<?php

namespace Controller;

use View;

class BaseController extends \Controller {

	/**
	 * @var \Illuminate\View\View
	 */
    protected $layout = "layouts.application";

//
//    /**
//     * Runs before call to action
//     */
//    protected function before() {
//
//    }
//
//    /**
//     * Runs after call to action
//     * @param $result what the method has returned
//     */
//    protected function after($response) {
//
//    }
//
	/**
	 * Setup the layout used by the controller.
	 *
	 * @return void
	 */
	protected function setupLayout()
	{
		if ( ! is_null($this->layout))
		{
            $view = View::make($this->layout);
			$this->layout = $view;
		}
	}

//    /**
//     * Execute an action on the controller.
//     *
//     * @param  string  $method
//     * @param  array   $parameters
//     * @return \Symfony\Component\HttpFoundation\Response
//     */
//    public function callAction($method, $parameters)
//    {
//        $this->setupLayout();
//
//        $this->before();
//
//        $response = call_user_func_array(array($this, $method), $parameters);
//
//        // If no response is returned from the controller action and a layout is being
//        // used we will assume we want to just return the layout view as any nested
//        // views were probably bound on this view during this controller actions.
//        if (is_null($response) && ! is_null($this->layout))
//        {
//            $response = $this->layout;
//        }
//
//        $this->after($response);
//
//        return $response;
//    }

}
