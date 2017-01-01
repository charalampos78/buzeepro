<?php

namespace Controller\Manage;

use Controller\BaseController;

class BaseManageController extends BaseController {

    /**
     * @var \Illuminate\View\View $layout
     */
    public $layout = "manage.application";

    protected function setupLayout() {
        parent::setupLayout();

        $this->layout->nest('navtop', 'manage.navtop');
        //$this->layout->navtop = "manage.navtop";

        $this->layout->title = "Manage - " . \Config::get('app.name');

        \Assets::reset();
        \Assets::add('base.pre');
        \Assets::add('manage.pre');
        \Assets::add('base.post');
        \Assets::add('manage.post');

        \Assets::add('js/microJS/controllers/manage.js');

    }

}
