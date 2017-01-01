<?php

namespace Controller\Api;

use Auth;
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

class StateApi extends BaseApi {

	/**
	 * Send dataTable data
	 *
	 * @return Response
	 */
	public function getDatatables()
	{
        $states = Models\State::select(["states.*",
										    DB::raw('COUNT(DISTINCT endorsements.id) as eCount'),
										    DB::raw('COUNT(DISTINCT rates.id) as rCount'),
										    DB::raw('COUNT(DISTINCT miscs.id) as mCount'),
										])
									->leftJoin('endorsements','endorsements.state_id','=','states.id')
									->leftJoin('rates','rates.state_id','=','states.id')
									->leftJoin('miscs','miscs.state_id','=','states.id')
									->groupBy('states.id');
        $dataTables = Datatables::of($states)
            ->editColumn('status_flag', '{{ $status_flag ? "Enabled" : "Disabled" }}')
            ->addColumn('endorsement_count', function($result_obj) {
				return $result_obj->eCount;
			})
            ->addColumn('rate_count', function($result_obj) {
				return $result_obj->rCount;
			})
            ->addColumn('misc_count', function($result_obj) {
				return $result_obj->mCount;
			})
			->addColumn('manage', '{{ HTML::link("manage/data/state-edit/$id", "Edit", ["class"=>"btn btn-info"]) }}', 3)
            ->setIndexColumn('state-{{ $id }}')
            ->setRowData('state_id', '{{ $id }}')
            ->make();
		return $dataTables;
	}

    /**
     * Create state
     */
    public function postIndex() {

        $state = new Models\State;

        return $this->credate($state);
    }

    /**
     * Update state
     */
    public function putIndex($state) {

        return $this->credate($state);

    }

    /**
     * Method to both create and update state
     *
     * @param $state Models\State
     *
     * @return array
     */
    protected function credate($state) {
        DB::beginTransaction();

        $inputs = Input::get('state', []);

        $state->fill($inputs);

		if (!$state->save()) {
			$this->setFormErrors('state', $state->getErrors());
		}

		/** @var Models\Endorsement $endorsement */
		$endorsements = [];
		foreach (Arr::get($inputs, 'endorsements', []) as $key => $data ) {
			$endorsement = Models\Endorsement::findOrNew($key);
			if ($data['deleted']) {
				if ($endorsement->exists) $endorsement->delete();
				continue;
			}
			$endorsement->fill($data);
			$endorsement->state()->associate($state);
			if (!$endorsement->isValid()) {
				$this->setFormErrors("state.endorsements.$key", $endorsement->getErrors());
			}
			$endorsements[] = $endorsement;
		}

		/** @var Models\Misc $misc */
		$miscs = [];
		foreach (Arr::get($inputs, 'miscs', []) as $key => $data ) {
			$misc = Models\Misc::findOrNew($key);
			if ($data['deleted']) {
				if ($misc->exists) $misc->delete();
				continue;
			}
			$misc->fill($data);
			$misc->state()->associate($state);
			if (!$misc->isValid()) {
				$this->setFormErrors("state.miscs.$key", $misc->getErrors());
			}
			$miscs[] = $misc;
		}
		
		/** @var Models\Rate $rate */
		$rates = [];
		foreach (Arr::get($inputs, 'rates', []) as $key => $data ) {
			$rate = Models\Rate::findOrNew($key);
			if ($data['deleted']) {
				if ($rate->exists) $rate->delete();
				continue;
			}
			$rate->fill($data);
			$rate->state()->associate($state);
			if (!$rate->isValid()) {
				$this->setFormErrors("state.rates.$key", $rate->getErrors());
			}
			$rates[] = $rate;
		}
		
		/** @var Models\Rate $rate */
		foreach (Arr::get($inputs, 'rate_counties', []) as $key => $data ) {
			$rate = Models\Rate::findOrNew($key);
			if ($data['deleted']) {
				if ($rate->exists) $rate->delete();
				continue;
			}
			$rate->fill($data);
			$rate->state()->associate($state);

			$counties = Arr::get($data, 'counties', "");
			$counties = explode(",", $counties);
			if (empty($counties) || empty($counties[0])) {
				$this->setFormErrors("state.rate_counties.$key", ['counties'=>['Must select at least one county']]);
			} elseif (!$rate->save()) {
				$this->setFormErrors("state.rate_counties.$key", $rate->getErrors());
			} else {
				$rate->counties()->sync($counties);
			}
		}

		if (empty($this->formErrors)) {
			$state->endorsements()->saveMany($endorsements);
			$state->miscs()->saveMany($miscs);
			$state->rates()->saveMany($rates);
		}


		if (empty($this->formErrors)) {
            DB::commit();

            $uri = \URL::action('Controller\Manage\DataController@getStateEdit', [$state->id]);
			Flash::success("State:{$state->name} saved successfully");

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
	 * Returns endorsements for a specific state
	 */
	public function postEndorsements()
	{
		$inputs = Input::get('endorsements', []);

		$rules = [
			'state'  => 'required|exists:states,abbr',
		];

		$validator = Validator::make($inputs, $rules);
		if (($validator->fails())) {
			$this->setFormErrors('endorsements', $validator->messages());
		}

		if (empty($this->formErrors)) {
			/** @var Models\State $state */
			$state = Models\State::whereAbbr($inputs['state'])->first();
			$endorsements = $state->endorsements->map(function ($item, $key) {
				/** @var Models\Endorsement $item */
				return [
					"id"    => $item->id,
					"name"  => $item->name,
					"standard_flag"  => $item->standard_flag,
				];
			});

			return [
				"success" => true,
				"response" => $endorsements,
			];


		} else {
			throw new ValidationException(['errors' => $this->formErrors]);
		}

	}

	public function deleteIndex($id)
    {

    }

}
