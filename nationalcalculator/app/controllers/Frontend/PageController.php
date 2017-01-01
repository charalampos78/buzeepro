<?php

namespace Controller\Frontend;

use Assets;
use Illuminate\Support\Arr;
use Models;
use HTML;

class PageController extends BaseFrontendController {

	/*
	|--------------------------------------------------------------------------
	| Default Home Controller
	|--------------------------------------------------------------------------
	|
	| You may wish to use controllers instead of, or in addition to, Closure
	| based routes. That's great! Here is an example controller method to
	| get you started. To route to this controller, just add the route:
	|
	|	Route::get('/', 'HomeController@showWelcome');
	|
	*/

	public function home()
	{
//		$this->layout->nest('body', 'frontend.homepage');
//		$this->layout->body = \View::make('frontend.homepage', array());

		$html = HTML::content('home');
		$this->layout->nest('body', 'frontend.page', ['content'=>$html]);

	}

	public function about()
	{
		$html = HTML::content('about');
		$this->layout->nest('body', 'frontend.page', ['content'=>$html]);
	}

	public function membership()
	{
		$html = HTML::content('membership');
		$this->layout->nest('body', 'frontend.page', ['content'=>$html]);
	}

	public function calculators()
	{
		$html = HTML::content('calculators');
		$this->layout->nest('body', 'frontend.page', ['content'=>$html]);
	}

	public function registered()
	{
		$this->layout->nest('body', 'frontend.registered');
	}

	public function contact()
	{
		Assets::add('js/microJS/controllers/frontend.js');
		$this->layout->nest('body', 'frontend.contact');
	}

	public function page($page) {

		$html = HTML::content($page);
		$this->layout->nest('body', 'frontend.page', ['content'=>$html]);

	}

}
