<?php

namespace Controller\Manage;

use Models;
use DB;

class DataController extends BaseManageController {

	protected function setupLayout() {
		parent::setupLayout();
		\Assets::add('js/microJS/controllers/data.js');
	}

	/*******************************************************
	 *  STATES
	 *******************************************************/
	public function getStates()
	{
		$this->layout->nest('body', 'manage.data.states');
	}

	/**
	 * @param $state Models\State
	 */
	public function getStateEdit($state)
	{
		$rates = $state->rates()->has('counties', '=', 0)->orderBy('type')->orderBy('range_min')->get();
		$rate_counties = $state->rates()->select('rates.*', DB::raw('GROUP_CONCAT(DISTINCT rate_counties.county_id ORDER BY rate_counties.county_id) as rc_list'))->has('counties', '>=', 1)->leftJoin('rate_counties', function($join) {
			$join->on('rates.id', '=', 'rate_counties.rate_id');
		})->groupBy('rates.id')->orderBy('rc_list')->orderBy('type')->orderBy('range_min')->get();

		$this->layout->nest('body', 'manage.data.stateAddit', compact('state', 'rates', 'rate_counties'));
	}

	/*******************************************************
	 *  COUNTIES
	 *******************************************************/
	public function getCounties()
	{
		$this->layout->nest('body', 'manage.data.counties');
	}

	public function getCountyAdd()
	{
		$county = new Models\County;
		$this->layout->nest('body', 'manage.data.countyAddit', compact('county'));
	}
	public function getCountyEdit($county)
	{
		$this->layout->nest('body', 'manage.data.countyAddit', compact('county'));
	}
	
	/*******************************************************
	 *  ZIPS
	 *******************************************************/
	public function getZips()
	{
		$this->layout->nest('body', 'manage.data.zips');
	}

	public function getZipAdd()
	{
		$zip = new Models\Zip;

		$related_zips = [];
		$similar_zips = [];

		$this->layout->nest('body', 'manage.data.zipAddit', compact('zip', 'related_zips', 'similar_zips'));
	}
	public function getZipEdit($zip)
	{
		$related_zips = $zip->county->zips()->with('county', 'state')
			->where("zips.id","!=",$zip->id)
			->orderBy('zip')
			->get();

		$similar_zips = Models\Zip::select('zips.*')->with('county', 'state')
			->leftJoin('counties','counties.id','=','zips.county_id')
			->where("zips.zip","=",$zip->zip)
			->where("zips.id","!=",$zip->id)
			->orderBy('counties.name')
			->get();

		$this->layout->nest('body', 'manage.data.zipAddit', compact('zip', 'related_zips', 'similar_zips'));
	}

	public function anyZipExtra() {
		$related_zips = [];

		$zip_id = \Input::get('zip_id'); //existing entry
		$county_id = \Input::get('county_id');
		$zip_code = \Input::get('zip');

		$zip = Models\Zip::find($zip_id) ?: (object) array('id'=>'null');
		$county = Models\County::find($county_id);

		if ($county) {
			$related_zips = $county->zips()->with('county', 'state')
				->orderBy('zip')
				->where("zips.id","!=",$zip->id)
				->get();
		}

		$similar_zips = Models\Zip::select('zips.*')->with('county', 'state')
			->leftJoin('counties','counties.id','=','zips.county_id')
			->where("zips.zip","=",$zip_code)
			->orderBy('counties.name')
			->where("zips.id","!=",$zip->id)
			->get();

		return \View::make('manage.data.zipExtra', compact('related_zips','similar_zips'));
	}
}