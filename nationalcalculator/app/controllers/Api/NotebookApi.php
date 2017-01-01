<?php

namespace Controller\Api;

use Auth;
use Controller\Exceptions\InvalidObjectIdException;
use Controller\Exceptions\ValidationException;
use DB;
use Datatables;
use Models; //PHPStorm autocomplete wants this... :/
use Response;
use Input;
use Flash;
use Validator;

class NotebookApi extends BaseApi {
    
    /** @var Models\User $user */
    public $user;
    
    public function __construct() {
        $this->user = Auth::user();
    }

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function getIndex()
	{
		$notebook_id = Input::get('notebook_id');
        $validator = Validator::make(['notebook_id'=>$notebook_id], [
			        		'notebook_id'  => 'required|exists:notebooks,id',
        				]);

        if ( $validator->fails() ) {
            $this->setFormErrors('notebook', $validator->messages());
            throw new ValidationException(['errors' => $this->formErrors]);
        }

        $notebook = $this->user->notebooks()->whereId($notebook_id)->first();

        return [
            'success'=>true,
            'results'=> $notebook->getCalculator()->toArray(),
        ];

	}

	/**
	 * Send dataTable data
	 *
	 * @return Response
	 */
	public function getDatatables()
	{
		$notebooks = $this->user->notebooks()->with('zip')->with('zip.state')->with('county')->ModelJoin('zip')->ModelJoin('county');
		//$notebooks is HasMany, need Eloquent Builder
		return $dataTables = Datatables::of($notebooks->getQuery())
			->editColumn('updated_at', function($result_obj) {
				return $result_obj->updated_at->format('m/d/Y - h:ia');
			})
			//need to add columns as "notebook." so the dt is searchable
			->addColumn('notebooks.id', '{{ $id }}')
			->addColumn('notebooks.name', '{{ $name }}')
			->addColumn('manage', '{{ HTML::link("members/calculated/$id", "View", ["class"=>"btn btn-info"]) }}')
			->setIndexColumn('notebook-{{ $id }}')
			->setRowData('notebook_id', '{{ $id }}')
			->make();
	}

    /**
     * Create notebook
     */
    public function postIndex() {

        $notebook = new Models\Notebook;
		$notebook->user_id = $this->user->id;

		return $this->credate($notebook);
    }

    /**
     * Update notebook
	 *
	 * @param Models\Notebook $notebook
	 *
	 * @return array
	 */
    public function putIndex($notebook) {

		if ($notebook->user_id != $this->user->id) {
			throw new InvalidObjectIdException(['id'=>$this->user->id]);
		}

        return $this->credate($notebook);
    }

    /**
     * Method to both create and update notebook
     *
     * @param $notebook Models\Notebook
     *
     * @return array
     */
    protected function credate($notebook) {
        DB::beginTransaction();

        $inputs = Input::get('notebook', []);

		if ($inputs['zip']) {
			/** @var Models\Zip $zip */
			$zip = Models\Zip::whereZip($inputs['zip'])->first();
			if ($zip) {
				$inputs['zip_id'] = $zip->id;
				if ($inputs['county']) {
					$county = Models\County::whereName($inputs['county'])->where('state_id','=', $zip->state_id)->first();
					if ($county) {
						$inputs['county_id'] = $county->id;
					}
				}
			}
		}
		if ($inputs['county_fips_code']) {
			$county = Models\County::where('fips_code','=', $inputs['county_fips_code'])->first();
			if ($county) {
				$inputs['county_id'] = $county->id;
			}
		}
		unset($inputs['zip']);
		unset($inputs['county']);
		unset($inputs['county_fips_code']);

        $notebook->fill($inputs);
		switch ($notebook->type) {
			case 'cash':
				$notebook->loan_amount = 0;
				break;
			case 'refinance':
				$notebook->purchase_price = 0;
				break;
		}

        if (!$notebook->save()) {
            $this->setFormErrors('notebook', $notebook->getErrors());
        }

		if (empty($this->formErrors)) {

			//SAVE DOCUMENTS
			$documents = $notebook->documents;
			$document_list = $documents->lists('id') + ['0']; //must have 1 element for whereIn
			$county = $notebook->county;
			if ($county->exists) {
				$county_documents = $county->documents()->whereNotIn('id', $document_list)->get();
				$documents = $documents->merge($county_documents);
			}
			$doc_pages = Input::get('notebook.documents', []);
			$doc_attach = [];
			foreach ($documents as $doc) {
				$pages = $doc_pages[$doc->id]['pages'];
				if (Validator::make(['pages' => $pages],['pages' => 'integer'])->fails()) {
					$this->setFormErrors("notebook.document_error", "Issue with documents");
					$this->setFormErrors("notebook.documents.".$doc->id.".pages", "Page count must be a number or empty");
					continue;
				}
				if (!empty($pages)) {
					$doc_attach[$doc->id] = ['pages' => $doc_pages[$doc->id]['pages']];
				}
			}
			if (empty($this->formErrors) && !empty($doc_attach)) {
				$notebook->documents()->sync($doc_attach);
			}
			//END SAVE DOCUMENTS

			//SAVE ENDORSEMENTS
			$endorsement_selected = Input::get('notebook.endorsements', []);
			$endorsement_ids = array_keys($endorsement_selected);
			try {
				$notebook->endorsements()->sync($endorsement_ids);
			} catch (\Exception $e) {
				$this->setFormErrors("notebook.endorsement_error", "Invalid endorsement chosen");
			}
			//END SAVE ENDORSEMENTS
			
			//SAVE MISCS
			$misc_selected = Input::get('notebook.miscs', []);
			$misc_ids = array_keys($misc_selected);
			try {
				$notebook->miscs()->sync($misc_ids);
			} catch (\Exception $e) {
				$this->setFormErrors("notebook.misc_error", "Invalid misc chosen");
			}
			//END SAVE MISCS
		}


        if (empty($this->formErrors)) {
            $notebook->save();
            DB::commit();

			$uri = \URL::action('Controller\Frontend\MemberController@calculated', [$notebook->id]);

            return [
                'success' => true,
                'flash_msg' => Flash::get_flash(),
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
