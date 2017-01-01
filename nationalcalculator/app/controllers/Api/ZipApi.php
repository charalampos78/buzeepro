<?php

namespace Controller\Api;

use Controller\Exceptions\ValidationException;
use DB;
use Datatables;
use Models; //PHPStorm autocomplete wants this... :/
use Response;
use Input;
use Flash;

/**
 * Class ZipApi
 *
 * @SWG\Resource(
 *     resourcePath="/zip",
 *     basePath="/api",
 *     authorizations="apiKey",
 *     @SWG\Produces("application/json"),
 *     @SWG\Consumes("application/json")
 * )
 *
 * @package Controller\Api
 */
class ZipApi extends BaseApi {

	/**
	 * Display a listing of the resource.
	 *
	 * @SWG\Api(
	 *   path="/zip/select2",
	 *   @SWG\Operation(
	 *     method="GET",
	 *     summary="Get zips",
	 *   )
	 * )
	 *
	 * @return Response
	 */
	public function getSelect2()
	{

		$search = Input::get('q');

        $zips = Models\Zip::select(["zips.*", DB::raw("group_concat(DISTINCT '\"',counties.id,'\":\"',counties.name,'\"') AS counties")])->distinct()
					->leftJoin("states", function($join) {
						$join->on("states.id","=","zips.state_id");
					})
					->leftJoin("counties", function($join) {
						$join->on("counties.id","=","zips.county_id");
						//$join->where('counties.status_flag','=',1);
					})
					->where('states.status_flag','=',1)
					->where('counties.status_flag','=',1)
					->where('zips.status_flag','=',1)
					->where("zip","LIKE", "$search%")
					->groupBy("zips.zip")
					->get();

        return [
			'success' => true,
			'zips' => $zips,
		];
		//
	}

	/**
	 * Send dataTable data
	 *
	 * @return Response
	 */
	public function getDatatables()
	{
		$zips = Models\Zip::select()->with('state')->ModelJoin('county');
		return $dataTables = Datatables::of($zips)
			->editColumn('status_flag', '{{ $status_flag ? "Enabled" : "Disabled" }}')
			->addColumn('manage', '{{ HTML::link("manage/data/zip-edit/$id", "Edit", ["class"=>"btn btn-info"]) }}', 3)
//			->editColumn('county', '{{ $county["name"] }}', 3)
			->setIndexColumn('zip-{{ $id }}')
			->setRowData('zip_id', '{{ $id }}')
			->make();
	}

	/**
	 * Create zip
	 */
	public function postIndex() {

		$zip = new Models\Zip;

		return $this->credate($zip);
	}

	/**
	 * Update zip
	 */
	public function putIndex($zip) {

		return $this->credate($zip);

	}

	/**
	 * Method to both create and update zip
	 *
	 * @param $zip Models\Zip
	 *
	 * @return array
	 */
	protected function credate($zip) {
		DB::beginTransaction();

		$inputs = Input::get('zip', []);

		$zip->fill($inputs);

		if (!$zip->isValid()) {
			$this->setFormErrors('zip', $zip->getErrors());
		}

		if (empty($this->formErrors)) {
			$zip->save();
			DB::commit();

			$uri = \URL::action('Controller\Manage\DataController@getZipEdit', [$zip->id]);
			Flash::success("Zip:{$zip->zip} saved successfully");

			return [
				'success' => true,
				'flash_msg' => Flash::get_flash(null, false),
				'uri' => $uri,
			];
		} else {
			DB::rollBack();
			throw new ValidationException(['errors' => $this->formErrors]);
		}
	}

	public function deleteIndex($id)
	{

	}

}
