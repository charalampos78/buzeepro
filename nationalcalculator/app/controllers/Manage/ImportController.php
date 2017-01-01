<?php

namespace Controller\Manage;

use Carbon;
use DB;
use Input;
use Redirect;
use League\Csv\Reader;
use Validator;

class ImportController extends BaseManageController {

	/**
	 * Display a listing of the resource.
	 * GET /manage/dashboard
	 *
	 * @return \Response
	 */
	public function getIndex()
	{
		$this->layout->nest('body', 'manage.import');
	}

	public function getZipCounty() {
		return $this->getIndex();
	}

	/**
	 * First this clears the zip_import table and inserts the items from the CSV.
	 * Then it sets the state and existing county id's for the imported zips.
	 * Next it adds new counties to the system that didn't match with an existing county id.
	 * Now that the counties do exist, it sets the remaining county id's
	 * Finally it inserts the new zips that don't match a existing zip/county combo.
	 *
	 * @return $this
	 */
	public function postZipCounty()
	{
		$validator = Validator::make(Input::file(), [
			'CSV' => 'required'
		]);
		$validator->setAttributeNames(['CSV'=>'CSV']);

		if ($validator->fails()) {
			return Redirect::action('Controller\Manage\ImportController@getIndex')->withErrors($validator);
		}

		set_time_limit(0);
		ignore_user_abort(true);

		DB::table('zip_import')->delete();
		//import csv into table
		$csv = Reader::createFromPath(Input::file('CSV')->getRealPath());
		DB::disableQueryLog();

		$csv_limit = 5000;
		$count = 0;
		do {
			$csv_rows = $csv->setOffset($csv_limit * $count)
							->setLimit($csv_limit)
							->fetchAssoc(['county','zip','zip_type','fips_code','city','st','addy_count','primary_county','multi_county']);
			DB::table('zip_import')->insert($csv_rows);
			$count++;
		} while (count($csv_rows) >= $csv_limit);

		DB::enableQueryLog();

		$startZipCount = DB::table('zips')->count();
		$startCountyCount = DB::table('counties')->count();

		//set state_id's
		DB::table('zip_import')->leftJoin('states', "states.abbr", "=", "zip_import.st")->update(["state_id"=>DB::raw('states.id')]);
		DB::table('zip_import')->whereNull('state_id')->delete();

		//set county_id's for counties that exist
		DB::table('zip_import')->leftJoin('counties', function($join) {
			$join->on("zip_import.county","=","counties.name");
			$join->on("zip_import.state_id","=","counties.state_id");
		})->update(["county_id"=>DB::raw('counties.id')]);

		//add counties that don't exist
		$selectNewCounties = DB::table('zip_import')->select('county', 'fips_code', 'state_id', DB::raw(0))->whereNull('county_id')->groupBy(['county','st']);

		$insertQuery = 'INSERT INTO counties (`name`, `fips_code`,`state_id`,`status_flag`) ' . $selectNewCounties->toSql();
		DB::statement($insertQuery);

		//set county_id's remaining counties
		DB::table('zip_import')->leftJoin('counties', function($join) {
			$join->on("zip_import.county","=","counties.name");
			$join->on("zip_import.state_id","=","counties.state_id");
		})->whereNull('county_id')->update(["county_id"=>DB::raw('counties.id')]);

		$selectNewZips = DB::table('zip_import')
							->select('zip_import.zip', 'zip_import.city', 'zip_import.zip_type',
								DB::raw("REPLACE(zip_import.primary_county, 'Y', '1')"),
								DB::raw("REPLACE(zip_import.multi_county, 'Y', '1')"),
								'zip_import.county_id', 'zip_import.state_id')
								->whereNotNull('zip_import.county_id')->whereNotNull('zip_import.state_id')
								->leftJoin('zips', function($join) {
									$join->on("zip_import.zip","=","zips.zip");
									$join->on("zip_import.county_id","=","zips.county_id");
								})->whereNull('zips.id')
								->groupBy(['zip_import.zip', 'zip_import.county_id']);

		$insertQuery = 'INSERT INTO zips (`zip`, `city`, `zip_type`, `primary_county`, `multi_county`, `county_id`, `state_id`) ' . $selectNewZips->toSql();
		DB::statement($insertQuery);

		//DB::table('zip_import')->delete();

		DB::table('zips')->where('created_at','=','0000-00-00 00:00:00')->update(['created_at'=> Carbon\Carbon::now(), 'updated_at' => Carbon\Carbon::now()]);
		DB::table('counties')->where('created_at','=','0000-00-00 00:00:00')->update(['created_at'=> Carbon\Carbon::now(), 'updated_at' => Carbon\Carbon::now()]);

		$countyCount = DB::table('counties')->count() - $startCountyCount;
		$zipCount = DB::table('zips')->count() - $startZipCount;

		$this->layout->nest('body', 'manage.import_zip_county_done', compact('countyCount', 'zipCount'));
	}

	/**
	 * First it clears the temp table and imports the csv into that temp table.
	 * Then it updates the temp table trimming all the columns and setting the state and county id's on matches
	 * Next it deletes entries in the tax_collector table that match the new items in the temp table.
	 * Using those matches it then fills in the tax_collector table with new data.
	 *
	 * @return $this
	 */
	public function postTaxCollectors() {
		$validator = Validator::make(Input::file(), [
			'CSV' => 'required'
		]);
		$validator->setAttributeNames(['CSV'=>'CSV']);

		if ($validator->fails()) {
			return Redirect::action('Controller\Manage\ImportController@getIndex')->withErrors($validator);
		}

		set_time_limit(0);
		ignore_user_abort(true);

		$existTCCount = DB::table('tax_collectors')->count();

		DB::table('collectors_import')->delete();
		//import csv into table
		$csv = Reader::createFromPath(Input::file('CSV')->getRealPath());
		DB::disableQueryLog(); //query log would be too big for massive amount of imports

		$csv_limit = 2500; //can't be more than ~3000 or too many placeholders error
		$count = 0;
		$totalRows = 0;
		do {
			$csv_rows = $csv->setOffset($csv_limit * $count)
				->setLimit($csv_limit)
				->fetchAssoc(['county', 'st', 'office', 'municipality', 'commissioner', 'address', 'city', 'state', 'zip', 'email', 'phone', 'fax', 'paysite', 'com2', 'm_address', 'm_city', 'm_state', 'm_zip', 'phone2', 'fax2', 'website']);

			$result = DB::table('collectors_import')->insert($csv_rows);
			$count++;
			$totalRows += count($csv_rows);
		} while (count($csv_rows) >= $csv_limit);

		DB::enableQueryLog();

		//need to remove all spaces in columns
		DB::table('collectors_import')->update([
			'county' => DB::raw("TRIM(`county`)"),
			'st' => DB::raw("TRIM(`st`)"),
			'office' => DB::raw("TRIM(`office`)"),
			'municipality' => DB::raw("TRIM(`municipality`)"),
			'commissioner' => DB::raw("TRIM(`commissioner`)"),
			'address' => DB::raw("TRIM(`address`)"),
			'city' => DB::raw("TRIM(`city`)"),
			'state' => DB::raw("TRIM(`state`)"),
			'zip' => DB::raw("TRIM(`zip`)"),
			'email' => DB::raw("TRIM(`email`)"),
			'phone' => DB::raw("TRIM(`phone`)"),
			'fax' => DB::raw("TRIM(`fax`)"),
			'paysite' => DB::raw("TRIM(`paysite`)"),
			'com2' => DB::raw("TRIM(`com2`)"),
			'm_address' => DB::raw("TRIM(`m_address`)"),
			'm_city' => DB::raw("TRIM(`m_city`)"),
			'm_state' => DB::raw("TRIM(`m_state`)"),
			'm_zip' => DB::raw("TRIM(`m_zip`)"),
			'phone2' => DB::raw("TRIM(`phone2`)"),
			'fax2' => DB::raw("TRIM(`fax2`)"),
			'website' => DB::raw("TRIM(`website`)"),
		]);

		//set state_id's
		DB::table('collectors_import')->leftJoin('states', "states.abbr", "=", "collectors_import.st")->update(["state_id"=>DB::raw('states.id')]);

		//set county_id's for counties that exist
		DB::table('collectors_import')->join('counties', function($join) {
			$join->on("counties.name", "=", "collectors_import.county");
			$join->on("counties.state_id", "=", "collectors_import.state_id");
		})->update(["county_id"=>DB::raw('counties.id')]);


		//remove all old tax collector info that has a match
		DB::table('tax_collectors')->join('collectors_import', 'tax_collectors.county_id', '=', 'collectors_import.county_id')->delete();
		//add new tc info
		$selectNewCollectors = DB::table('collectors_import')
			->select('county_id', 'municipality', 'commissioner',
				'address', 'city', 'state', 'zip',
				'email', 'phone', 'fax', 'paysite',
				'm_address', 'm_city', 'm_state', 'm_zip', 'phone2', 'fax2', 'website')
			->whereNotNull('county_id')
		;
		$insertQuery = 'INSERT INTO tax_collectors (`county_id`, `municipality`, `commissioner`, `address`, `city`, `state`, `zip`, `email`, `phone`, `fax`, `paysite`, `m_address`, `m_city`, `m_state`, `m_zip`, `phone2`, `fax2`, `website`) '
			. $selectNewCollectors->toSql();

		DB::statement($insertQuery);
		DB::table('tax_collectors')->where('created_at','=','0000-00-00 00:00:00')->update(['created_at'=> Carbon\Carbon::now(), 'updated_at' => Carbon\Carbon::now()]);
		$finalTCCount = DB::table('tax_collectors')->count();


		$missing = DB::table('collectors_import')->whereNull('county_id')->groupBy(['st', 'county'])->get();

		//DB::table('collectors_import')->delete();

		$this->layout->nest('body', 'manage.import_collectors_done', compact('totalRows', 'existTCCount', 'finalTCCount', 'missing'));

	}

}