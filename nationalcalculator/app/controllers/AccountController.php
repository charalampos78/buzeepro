<?php

namespace Controller;

use Auth;
use Config;
use DB;
use Flash;
use Models;
use Redirect;

class AccountController extends BaseController
{

    public $layout = "frontend.application";

    public function setupLayout() {
        parent::setupLayout();

        \Assets::add('js/microJS/controllers/account.js');
		\Assets::add('frontend');
		$this->layout->nest('content_wrapper', 'frontend.content_wrapper');

	}

    /**
     * @return \Response
     */
    public function login()
    {
       $this->layout->content = \View::make('frontend.account.login', array());
    }

	/**
	 * Should already be validated via JWT, so session should be set as logged in
	 * So this just needs to forward to page
	 */
	public function loginJWT($notebook_id = null) {

		Auth::loginUsingId(Auth::user()->id);

		if ($notebook_id) {
			return Redirect::route('memberCalculated', ['notebook'=>$notebook_id]);
		}
		return Redirect::route('memberNotebook');
	}

	public function getJWT() {
		$html = \AuthHelper::createJWT();
		$this->layout->nest('body', 'frontend.page', ['content'=>$html]);
	}
	public function getPostman() {
		$html = <<<HEREDOC
<div class="postman-run-button"
data-postman-action="collection/import"
data-postman-var-1="7e712d8a6034e6f27b5a"></div>
<script type="text/javascript">
  (function (p,o,s,t,m,a,n) {
    !p[s] && (p[s] = function () { (p[t] || (p[t] = [])).push(arguments); });
    !o.getElementById(s+t) && o.getElementsByTagName("head")[0].appendChild((
      (n = o.createElement("script")),
      (n.id = s+t), (n.async = 1), (n.src = m), n
    ));
  }(window, document, "_pm", "PostmanRunObject", "https://run.pstmn.io/button.js"));
</script>
HEREDOC;
		$this->layout->nest('body', 'frontend.page', ['content'=>$html]);
	}

	public function recover($token = null)
	{
		if (!$token) {
			Flash::notice("Can't recover password without recovery token!");
			return Redirect::route("login");
		}
		$expires = Config::get('auth.reminder.expire');
		$reminder = Models\PasswordReminder::whereToken($token)->where("created_at", ">", DB::raw("NOW() - INTERVAL $expires MINUTE"))->first();
		if (!$reminder) {
			Flash::notice("Sorry, invalid, expired, or used recovery link!");
			return Redirect::route("login");
		}


		$this->layout->content = \View::make('frontend.account.recover', compact("token"));
	}

    /**
     * @return \Response
     */
    public function register()
    {
		//echo implode(Flash::get_flash_html());
		$user = new Models\User();
		$this->layout->content = \View::make('frontend.account.register', compact('user'));
    }

    public function logout() {

        //@TODO: call logout API

        \Auth::logout();

        return \Redirect::to('/');

    }

}