<?php

namespace Controller\Manage;

use Illuminate\Support\Facades\Form;
use My\Mform;

class ContentsController extends BaseManageController {

    /**
     * Display a listing of the resource.
     * GET /manage/dashboard
     *
     * @return \Response
     */
    public function getIndex()
    {
        $this->layout->nest('body', 'manage.contents');
    }

    public function getAdd()
    {
        $content = new \Models\Content;
        $this->layout->nest('body', 'manage.contentsAddit', compact('content'));
    }

    public function getEdit($content)
    {
//        if (!$content) {
//            \Flash::info('Content not found');
//            return \Redirect::action('Controller\Manage\ContentsController@getIndex');
//        }
        $this->layout->nest('body', 'manage.contentsAddit', compact('content'));
    }

}