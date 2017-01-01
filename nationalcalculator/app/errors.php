<?php

use Controller\Exceptions\RedirectException;

//App::missing(function($exception)
//{
//    return Response::view('layouts.errors.404', array(), 404);
//});

App::error(function(RedirectException $e, $code)
{
    return Redirect::to($e->url);
});

App::error(function($exception, $code)
{
    if (!Request::ajax() && !Request::wantsJson()) {

        switch ($code)
        {
            case 404:
                if (!Request::ajax() && !Request::wantsJson()) {
                    Assets::add('frontend');
                    return Response::view('layouts.errors.404', compact('exception', 'code'), 404);
                }
                break;
        }
        if ($exception instanceof \Controller\Exceptions\InvalidObjectIdException) {
            if ($url = $exception->data['url']) {
                Flash::warning($exception->getMessage());
                return Redirect::to($url);
            }
        }
//        if (App::environment() == "production") {
//            return Response::view('layouts.errors.error', array(), 500);
//        }
    }

    if ($exception instanceof \Controller\Exceptions\CustomException) {
        if (App::bound('whoops')) {
            $whoopsHandler = App::make('whoops.handler');

            if ($whoopsHandler instanceof \Whoops\Handler\JsonResponseHandler) {
                $whoops = App::make('whoops');
                $data = json_decode($whoops->handleException($exception), true);
                $data['error']['data'] = $exception->data;
                $data['success'] = $exception->success;
                $data['flash_msg'] = $exception->flash;
                if ($exception->exceptionName) {
                    $data['error']['type'] = $exception->exceptionName;
                }
                if (App::environment('production')) {
                    unset($data['error']['file']);
                    unset($data['error']['line']);
                }

                return Response::json($data, $code);
            }

            if($whoopsHandler instanceof \Whoops\Handler\PrettyPageHandler) {

                // Set the "open:" link for files to our editor of choice:
                $whoopsHandler->setEditor("sublime");
                $whoopsHandler->addDataTable("Extra Data", $exception->data);
                $whoopsHandler->addDataTable("Flash", $exception->flash);

            }
        }
    }
});

//
