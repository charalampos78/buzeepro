<?php

namespace Controller\Api;

use Controller\BaseController;
use Illuminate\Support\Arr;
use Illuminate\Support\Contracts\MessageProviderInterface;
use Swagger\Annotations as SWG;

/**
 * Class BaseApi
 *
 * @SWG\Info(
 *   title="Rasterly App",
 *   description="This is the rasterly API",
 * )
 * @SWG\Authorization(
 *   type="apiKey",
 *   passAs="header",
 *   keyname="X-Auth-Token"
 * )
 *
 * @package Controller\Api
 */
class BaseApi extends BaseController
{
    protected $layout = null;

    protected $formErrors = [];

    protected function setFormErrors($key = null, $value = null) {
        if ($value instanceof MessageProviderInterface) {
            $value = $value->getMessages();
        }

//        $error = [];
//        Arr::set($error, $key, $value);
//        $this->formErrors = array_merge_recursive($this->formErrors, $error);

		Arr::set($this->formErrors, $key, $value);
    }

//    public function __construct() {
//
//        $this->afterFilter("@customFilter");
//
//    }
//
//    /**
//     * Checks if response should have a 400 status code
//     */
//    public function customFilter($route, $request, $response) {
//
//    }
//
//
//
//    /**
//     * Runs after call to action
//     * @param $result what the method has returned
//     */
//    protected function after($response) {
//
//    }

}