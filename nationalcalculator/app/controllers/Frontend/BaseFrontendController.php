<?php

namespace Controller\Frontend;

use Controller\BaseController;

class BaseFrontendController extends BaseController {

    public $layout = "frontend.application";

	protected function setupLayout() {
		parent::setupLayout();

		\Assets::add('frontend');
		$this->layout->nest('content_wrapper', 'frontend.content_wrapper');


	}
}
