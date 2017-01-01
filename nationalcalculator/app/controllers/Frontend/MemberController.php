<?php

namespace Controller\Frontend;

use Auth;
use Assets;
use Config;
use Flash;
use Illuminate\Support\Collection;
use Models;
use Models\User;
use HTML;
use Redirect;

class MemberController extends BaseFrontendController {

	/**
	 * @var Models\User
	 */
	protected $user = null;

	public function __construct() {

		$this->user = Auth::user();

	}

	protected function setupLayout()
	{
		parent::setupLayout();

		Assets::add('js/microJS/controllers/member.js');
		$this->layout->nest('navtop_sub', 'members.navtop_sub');
		//$this->layout->navtop = "manage.navtop";
	}

	public function index()
	{
		$html = HTML::content('member_home');
		$this->layout->nest('body', 'frontend.page', ['content'=>$html]);

	}

	public function account()
	{
		$user = Auth::user();
		$this->layout->content = \View::make('frontend.account.edit', compact('user'));
	}

	public function notebook()
	{
		Assets::add('dataTables');

		$user = Auth::user();
		$this->layout->content = \View::make('members.notebook', compact('user'));
	}

	public function subscribe()
	{
		$user = $this->user;

//		if ($user->subscribed() && $user->cancelled()) {
		if ($user->subscribed() && $user->subscription()->onGracePeriod()) {
			$this->subscribe_cancelled();
				return;
		}

		if ($user->subscriptions->count()) {
			//already subscribed once
			$trial = "";
		} else {
			$trial = "+ 7 day trial";
		}
		$plans = [
			User::PLANS[1]['code'] => User::PLANS[1]['name'] . " Membership ($".User::PLANS[1]['cost'].")" . $trial,
			User::PLANS[2]['code'] => User::PLANS[2]['name'] . " Membership ($".User::PLANS[2]['cost'].")" . $trial,
		];
		if ($user->subscribed()) {
			if ($user->onPlan(User::PLANS[1]['code'])) {
				unset($plans[User::PLANS[1]['code']]);
			} elseif ($user->onPlan(User::PLANS[2]['code'])) {
				unset($plans[User::PLANS[2]['code']]);
			}
		}

		if ($user->hasStripeId()) {
			$invoices = $user->invoices(true);
		} else {
			$invoices = Collection::make([]);
		}

		$name = "";
		if ($user->profile) {
			$name = $user->profile->first_name . ' ' . $user->profile->last_name;
		}
		$stripePublic = Config::get('services.stripe.public');
		Assets::add('https://js.stripe.com/v2/#stripe.js');
		$this->layout->nest('body', 'members.subscribe', compact('stripePublic', 'name', 'user', 'plans', 'invoices'));
	}

	public function subscribe_cancelled() {
		$user = $this->user;

		if ($user->hasStripeId()) {
			$invoices = $user->invoices(true);
		} else {
			$invoices = Collection::make([]);
		}

		$this->layout->nest('body', 'members.subscribe_cancelled', compact('user','invoices'));
	}

	public function subscribe_invoice($invoice_id) {
		$user = Auth::user();

		$invoice = $user->findInvoice($invoice_id);

		$product = $invoice->subscriptions()[0]->plan->name;

		return $invoice->view(["product"=>$product]);

	}

	public function calculated($notebook) {
		if ($notebook->exists && $notebook->user_id != $this->user->id) {
			Flash::warning("Invalid calculated notebook selected");
			return Redirect::action('Controller\Frontend\MemberController@calculator');
		}

		$user = $this->user;

		Assets::add('js/libs/jquery-fileDownload-1.4.4/jquery.fileDownload.js');
		//code to display static calculated info here
		$this->layout->nest('body', 'members.calculated', compact('notebook', 'user'));
//		return \View::make('emails.calculated', compact('notebook', 'user'));

	}

	/**
	 * @param Models\Notebook $notebook
	 *
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function calculator($notebook = null)
	{
		if ($notebook && $notebook->exists && $notebook->user_id != $this->user->id) {
			Flash::notice("Invalid calculated notebook selected");
			return Redirect::action('Controller\Frontend\MemberController@calculator');
		}

		$user = $this->user;

		$notebook = $notebook?:new Models\Notebook();

		$documents = $notebook->documents;
		$document_list = $documents->lists('id') + ['0']; //must have 1 element for whereIn

		$endorsements_selected = $notebook->endorsements()->orderBy('standard_flag','DESC')->orderBy('name','ASC')->get();
		$endorsement_list = $endorsements_selected->lists('id') + ['0']; //must have 1 element for whereIn
		
		$miscs_selected = $notebook->miscs()->orderBy('name','ASC')->get();
		$misc_list = $miscs_selected->lists('id') + ['0']; //must have 1 element for whereIn

		$endorsements = [];
		$endorsements_more = [];
		$miscs = [];
		$county = $notebook->county;
		if ($county->exists) {
			$county_documents = $county->documents()->whereNotIn('id', $document_list)->get();
			//can be merged because of pivot table data to see difference
			$documents = $documents->merge($county_documents);

			$endorsements = $county->state->endorsements()->whereNotIn('id', $endorsement_list)->where('standard_flag','1')->orderBy('name','ASC')->get();
			$endorsements_more = $county->state->endorsements()->whereNotIn('id', $endorsement_list)->where('standard_flag','0')->orderBy('name','ASC')->get();
			$miscs = $county->state->miscs()->whereNotIn('id', $misc_list)->orderBy('name','ASC')->get();

		}

		$note = $county->note;

		$this->layout->nest('body', 'members.calculator', compact('user', 'notebook', 'documents', 'endorsements', 'endorsements_more', 'endorsements_selected', 'miscs', 'miscs_selected', 'note'));
	}

	/**
	 * Will return partial view of the form for a counties view
	 *
	 * @param $county Models\County
	 *
	 * @return \Illuminate\View\View
	 */
	public function calcDocs($county) {

		if ( $this->user->hasRole('admin') || ($this->user->onPlan(User::PLANS[2]['code'])) ) {
			$documents = $county->documents;
		} else {
			$documents = [];
		}

		return \View::make('members.calcDocs', compact('documents'));
	}

	public function calcNote($county) {

		$note = $county->note;

		return \View::make('members.calcNote', compact('note'));
	}

	/**
	 * @param $state Models\State
	 *
	 * @return \Illuminate\View\View
	 */
	public function calcEndorsements($state) {

		$endorsements = $state->endorsements()->where('standard_flag','1')->orderBy('name','ASC')->get();
		$endorsements_more = $state->endorsements()->where('standard_flag','0')->orderBy('name','ASC')->get();
		$endorsements_selected = [];

		return \View::make('members.calcEndorsements', compact('endorsements', 'endorsements_more', 'endorsements_selected'));
	}

	/**
	 * @param $state Models\State
	 *
	 * @return \Illuminate\View\View
	 */
	public function calcMisc($state) {

		if ( $this->user->hasRole('admin') || ($this->user->onPlan(User::PLANS[2]['code'])) ) {
			$miscs = $state->miscs()->orderBy('name', 'ASC')->get();
		} else {
			$misc = [];
		}
		$miscs_selected = [];

		return \View::make('members.calcMisc', compact('miscs', 'miscs_selected'));
	}

}
