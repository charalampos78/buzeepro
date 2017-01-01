<?php

namespace Controller\Api;

use Auth;
use Controller\Exceptions\CustomException;
use Controller\Exceptions\ValidationException;
use DB;
use Datatables;
use Doctrine\DBAL\Query\QueryBuilder;
use Mockery\CountValidator\Exception;
use Models; //PHPStorm autocomplete wants this... :/
use Response;
use Input;
use Flash;
use Illuminate\Support\Arr;
use Validator;

class CountyApi extends BaseApi {

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
        $state_id = Input::get('state_id');

        $counties = Models\County::where("name","LIKE", "%$search%");
        if ($state_id) {
            $counties->where('state_id','=',$state_id);
        }
        $counties = $counties->get();

        return [
            'success' => true,
            'counties' => $counties,
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
        $counties = Models\County::select()->with('documents')->ModelJoin('state');
        return $dataTables = Datatables::of($counties)
			->editColumn('status_flag', '{{ $status_flag ? "Enabled" : "Disabled" }}')
			->addColumn('manage', '{{ HTML::link("manage/data/county-edit/$id", "Edit", ["class"=>"btn btn-info"]) }}', 3)
            ->addColumn('counties.name', '{{ $name }}', 3)
			->addColumn('document_count', function($result_obj) {
				return $result_obj->documents->count();
			})
			->setIndexColumn('county-{{ $id }}')
            ->setRowData('county_id', '{{ $id }}')
            ->make();
	}

    /**
     * Copy county
	 *
	 * @param $county Models\County
	 * @return array
     */
    public function postCopy($county) {
		/** @var Models\County $cc */
		/** @var Models\TaxCollector $tc */
		/** @var Models\DocumentTax $tax_new */

		DB::beginTransaction();

		$county->load('taxCollectors','documents', 'documents.taxes');

		$county_ids = Input::get('copy_counties', []);
		$county_ids = explode(',', $county_ids);
		$counties = Models\County::find($county_ids);

		if (!$counties->count()) {
			Flash::notice("No counties selected to copy.");
			throw new CustomException(['errors' => $this->formErrors], 400);
		}

		$cc_names = [];
		/** @var Models\County $cc */
		foreach ($counties as $cc) {
//			$cc->taxCollectors()->delete();
			$cc->documents()->delete();
			$cc_names[] = $cc->name;

			$cc->note = $county->note;
			$cc->save();
		}

//		$new_tc = $county->taxCollectors;
		$new_docs = $county->documents;

		//Copy tax collector info
//		$tc_copy = [];
//		foreach ($new_tc as $tc) {
//			foreach ($counties as $cc) {
//				$tc_new = $tc->replicate();
//				$tc_new->county()->associate($cc);
//				$tc_copy[] = $tc_new;
//			}
//		}
//		if (!empty($tc_copy)) {
////			Models\TaxCollector::insert($tc_copy);
//		}

		$docs_copy = [];
		$doc_tax_copy = [];
		foreach ($new_docs as $doc) {
			foreach ($counties as $cc) {
				$doc_new = $doc->replicate();
				$doc_new->county()->associate($cc);
				//can't bulk insert because id is needed for tax
				$doc_new->save();
				$docs_copy[] = $doc_new;
				foreach ($doc->taxes as $doc_tax) {
					//can't use associate or infinite loop
					$tax_new = $doc_tax->replicate();
					$tax_new->document()->associate($doc_new);
					//bulk insert has issues, just save here
					$tax_new->save();
					$doc_tax_copy[] = $tax_new;
				}
			}
		}
		if (!empty($doc_tax_copy)) {
			//bulk insert of doc taxes, doesn't do updated/created dates, and doesn't expect array of eloquent objects
			//Models\DocumentTax::insert($doc_tax_copy);
		}

		if (empty($this->formErrors)) {
			DB::commit();

			Flash::success("County docs copied to ".implode(", ", $cc_names)." successfully.");

			return [
				'success' => true,
				'flash_msg' => Flash::get_flash(null),
			];
		} else {
			DB::rollBack();
			throw new ValidationException(['errors' => $this->formErrors]);
		}

    }

    /**
     * Create county
     */
    public function postIndex() {

        $county = new Models\County;

        return $this->credate($county);
    }

    /**
     * Update county
	 *
	 * @param $county
	 *
	 * @return array
     */

    public function putIndex($county) {

        return $this->credate($county);

    }

    /**
     * Method to both create and update county
     *
     * @param $county Models\County
     *
     * @return array
     */
    protected function credate($county) {
        DB::beginTransaction();

        $inputs = Input::get('county', []);

        $county->fill($inputs);

        if (!$county->save()) {
            $this->setFormErrors('county', $county->getErrors());
        }

		/** @var Models\TaxCollector $tc */
		$tcs = [];
		foreach (Arr::get($inputs, 'taxCollectors', []) as $key => $data ) {
			$tc = Models\TaxCollector::findOrNew($key);
			if ($data['deleted']) {
				if ($tc->exists) $tc->delete();
				continue;
			}
			$tc->fill($data);
			$tc->county()->associate($county);
			if (!$tc->isValid()) {
				$this->setFormErrors("county.taxCollectors.$key", $tc->getErrors());
			}
			$tcs[] = $tc;
		}

		/** @var Models\Document $document */
		/** @var Models\DocumentTax $tax */
		foreach (Arr::get($inputs, 'documents', []) as $key => $data ) {
			$document = $county->documents()->find($key) ?: new Models\Document();
			if ($data['deleted']) {
				if ($document->exists)
					$document->forceDelete();
				continue;
			}
			$document->fill($data);
			$document->county()->associate($county);
			if (!$document->save()) {
				$this->setFormErrors("county.documents.$key", $document->getErrors());
			}
			$taxes = [];
			foreach (Arr::get($data, 'taxes', []) as $tax_key => $tax_data) {
				$tax = $document->taxes()->find($tax_key) ?: new Models\DocumentTax();
				if ($tax_data['deleted']) {
					if ($tax->exists)
						$tax->delete();
					continue;
				}
				$tax->fill($tax_data);
				$tax->document()->associate($document);
				if (!$tax->isValid()) {
					$this->setFormErrors("county.documents.$key.taxes.$tax_key", $tax->getErrors());
				}
				$taxes[] = $tax;
			}

			if (empty($this->formErrors)) {
				$document->taxes()->saveMany($taxes);
			}
		}

		if (empty($this->formErrors)) {
			$county->taxCollectors()->saveMany($tcs);
		}

		if (empty($this->formErrors)) {
            DB::commit();

            $uri = \URL::action('Controller\Manage\DataController@getCountyEdit', [$county->id]);
			Flash::success("County:{$county->name} saved successfully");

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

	/**
	 * Returns documents for a specific county
	 */
	public function postDocuments()
	{
		$inputs = Input::get('documents', []);

		$rules = [
			'state'  => 'required_without:county_fips_code|exists:states,abbr',
			'county' => 'required_without:county_fips_code|exists:counties,name',
			'county_fips_code' => 'required_without:state,county|exists:counties,fips_code',
		];

		$validator = Validator::make($inputs, $rules);
		if (($validator->fails())) {
			$this->setFormErrors('documents', $validator->messages());
		}

		if (empty($this->formErrors)) {
			if ($inputs['county']) {
				$state = Models\State::whereAbbr($inputs['state'])->first();
				/** @var Models\County $county */
				$county = Models\County::where('state_id', '=', $state->id)->where('name', '=', $inputs['county'])->first();
			} else if ($inputs['county_fips_code']) {
				$county = Models\County::where('fips_code', '=', $inputs['county_fips_code'])->first();
			}

			$documents = $county->documents->map(function ($item, $key) {
				/** @var Models\Document $item */
				return [
					"id"   => $item->id,
					"name" => $item->name,
				];
			});

			return [
				"success" => true,
				"response" => $documents,
			];


		} else {
			throw new ValidationException(['errors' => $this->formErrors]);
		}

	}

    public function deleteIndex($id)
    {

    }

}
