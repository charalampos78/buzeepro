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

class ContentApi extends BaseApi {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function getIndex()
	{
        return ['contents'=>['list goes here']];
		//
	}


	/**
	 * Send dataTable data
	 *
	 * @return Response
	 */
	public function getDatatables()
	{
        $contents = Models\Content::select();
        return $dataTables = Datatables::of($contents)
            ->addColumn('manage', '{{ HTML::link("manage/content/edit/$id", "Edit", ["class"=>"btn btn-info"]) }}', 3)
            ->editColumn('content', '{{ str_limit(strip_tags($content), 100) }}')
            ->setIndexColumn('content-{{ $id }}')
            ->setRowData('content_id', '{{ $id }}')
            ->make();
	}

    /**
     * Create content
     */
    public function postIndex() {

        $content = new Models\Content;

        return $this->credate($content);
    }

    /**
     * Update content
     */
    public function putIndex($content) {

        return $this->credate($content);

    }

    /**
     * Method to both create and update content
     *
     * @param $content Models\Content
     *
     * @return array
     */
    protected function credate($content) {
        DB::beginTransaction();

        $inputs = Input::get('content', []);

        $content->fill($inputs);

        if (!$content->isValid()) {
            $this->setFormErrors('content', $content->getErrors());
        }

        if (empty($this->formErrors)) {
            $content->save();
            DB::commit();

			$uri = \URL::action('Controller\Manage\ContentsController@getIndex');

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
