<?php

namespace Controller\Api;

use Auth;
use Illuminate\Support\Arr;
use Controller\Exceptions\InvalidObjectIdException;
use Controller\Exceptions\ValidationException;
use Input;
use mikehaertl\pdftk\Pdf;
use Models; //PHPStorm autocomplete wants this... :/
use Validator;
use Mail;
use Config;

class ExportApi extends BaseApi {
    
    /** @var Models\User $user */
    public $user;
    
    public function __construct() {
        $this->user = Auth::user();
    }

	public function getIndex($notebook, $output) {
		if ($notebook->user_id != $this->user->id) {
			throw new InvalidObjectIdException(['id'=>$this->user->id]);
		}

		switch ($output) {
			case 'HUD':
			case 'GFE':
				break;
			default:
				$output = "";
		}

		$save_path = app_path('docs')."/notebooks/{$notebook->id}/";
		$filename = "$output.notebook.{$notebook->id}.{$notebook->getHash()}.pdf";

		if (file_exists($save_path.$filename)) {
			return \Response::download($save_path.$filename); //, $filename, ["Content-Type"=>"application/pdf"]);
			//$pdf = new Pdf($save_path.$filename);
			//$pdf->send($filename);
		}

	}

    /**
     * Create notebook
	 *
	 * @param Models\Notebook $notebook
	 */
    public function postIndex($notebook) {
		if ($notebook->user_id != $this->user->id) {
			throw new InvalidObjectIdException(['id'=>$this->user->id]);
		}

		$inputs = Input::get('export',[]);
		$rules = array(
				'output' => 'required|in:HUD,GFE,email',
				'email' => 'email|required_if:output,email'
		);
		$validator = Validator::make($inputs, $rules, [], ['output'=>'export form']);
		if ( ($validator->fails()) ) {
			$this->setFormErrors('export', $validator->messages());
		}

		$return = true;
		switch (Arr::get($inputs, 'output')) {
			case 'HUD':
				$this->formHud($notebook);
				break;
			case 'GFE':
				$this->formGfe($notebook);
				break;
			case 'email':
				$this->formEmail($notebook);
				break;
		}

		if (empty($this->formErrors)) {
			return [
				'success'   => true,
			];
		} else {
			throw new ValidationException(['errors' => $this->formErrors]);
		}

    }

	/**
	 * @param Models\Notebook $notebook
	 */
	protected function formHud($notebook) {

		$path = app_path('docs')."/Blank_HUD.pdf";
		$fdf =  app_path('docs')."/Blank_HUD.fdf";
		$save_path = app_path('docs')."/notebooks/{$notebook->id}/";
		$filename = "HUD.notebook.{$notebook->id}.{$notebook->getHash()}.pdf";

		$deedDoc = $notebook->documents()->where('name','=', 'Deed')->first();
		$mortgageDoc = $notebook->documents()->where('name','=', 'Mortgage')->first();
		$satisfactionDoc = $notebook->documents()->where('name','=', 'Satisfaction of Mortgage')->first();
		$assignmentDoc = $notebook->documents()->where('name','=', 'Assignment of Mortgage')->first();
		$subordinationDoc = $notebook->documents()->where('name','=', 'Subordination')->first();

		$deedDocCost = $deedDoc?$notebook->getCalculator()->docPageCost($deedDoc):0;
		$mortgageDocCost = $mortgageDoc?$notebook->getCalculator()->docPageCost($mortgageDoc):0;
		$satisfactionDocCost = $satisfactionDoc?$notebook->getCalculator()->docPageCost($satisfactionDoc):0;
		$assignmentDocCost = $assignmentDoc?$notebook->getCalculator()->docPageCost($assignmentDoc):0;
		$subordinationDocCost = $subordinationDoc?$notebook->getCalculator()->docPageCost($subordinationDoc):0;
		$T1206a = [];
		$T1206a[] = $assignmentDocCost?"Assignment":"";
		$T1206a[] = $subordinationDocCost?"Subordination":"";
		$T1206a = array_filter($T1206a);


		$deedTransferTax = ($deedDoc)?$deedDoc->taxes()->whereIn('name',['Transfer Tax', 'State Transfer Tax', 'Documentary Tax'])->get():null;
		$mortgageDocumentaryTax = ($mortgageDoc)?$mortgageDoc->taxes()->where('name','=','Documentary Tax')->first():null;
		$mortgageIntangibleTax = ($mortgageDoc)?$mortgageDoc->taxes()->where('name','=','Intangible Tax')->first():null;

		$deedTransferTaxCost = 0;
		if (count($deedTransferTax)) {
			foreach ($deedTransferTax as $dt) {
				$deedTransferTaxCost += $notebook->getCalculator()->docTaxCost($deedDoc, $dt);
			}
		}
		$mortgageDocumentaryTaxCost = ($mortgageDoc&&$mortgageDocumentaryTax)?$notebook->getCalculator()->docTaxCost($mortgageDoc, $mortgageDocumentaryTax):0;
		$mortgageIntangibleTaxCost = ($mortgageDoc&&$mortgageDocumentaryTax)?$notebook->getCalculator()->docTaxCost($mortgageDoc, $mortgageIntangibleTax):0;

		$settlement = $notebook->miscs()->where('name','=','Settlement Fee')->first();
		$settlementFee = ($settlement)?$settlement->price:0;

		$fields = [
			'T101' => $notebook->purchase_price, //sales price (purchase price)
			'T401' => $notebook->purchase_price, //sales price (purchase price)
			'T202' => $notebook->loan_amount, //loan amount
			//'T1001-1' => '', //1002:+:1006 - 1007 - calc in pdf
			//'T1101-1' => '', //1102 + 1104 + 1109 + 1110 + 1111
			'T1102b' => $settlementFee,
			'T1103-1' => $notebook->getCalculator()->ownerCost(), //owners policy
			'T1104a' => "Endorsements: ".implode(", ", $notebook->getCalculator()->endorsementList()), //endorsement list
			'T1104b' => $notebook->getCalculator()->lenderCost() + $notebook->getCalculator()->endorsementCost(), //lender policy + endorsements
			'T1105' => $notebook->loan_amount, //loan amount
			'T1106' => $notebook->purchase_price, //purchase price
			//'1201' => '', //total of 1202a/b/c - calc in pdf
			'T1202a' => $deedDocCost, //deed doc
			'T1202b' => $mortgageDocCost, //mortgage doc
			'T1202c' => $satisfactionDocCost, //satisfaction doc
			//'1203' => '', //total of 1204a/b 1205a/b - calc in pdf
			//'T1204a' => '', //mortgage doc's documentary tax - unclear
			'T1204b' => $mortgageDocumentaryTaxCost, //mortgage doc: documentary tax
			'T1205a' => $deedTransferTaxCost, //deed doc: transfer tax
			'T1205b' => $mortgageIntangibleTaxCost, //mortgage doc: intangible tax
			'T1206a' => implode(", ", $T1206a),
			'T1206b' => $assignmentDocCost + $subordinationDocCost, //optional - deed tax for sales

			'loan-initial' => $notebook->loan_amount, //loan amount
		];

		if (!file_exists($save_path.$filename)) {
			$pdf = new Pdf($path);
			$pdf->fillForm($fields)->needAppearances();

			if (!file_exists($save_path)) { mkdir($save_path, 0777, true); }
			if (!$pdf->saveAs($save_path . $filename)) {
				$this->setFormErrors('export.output', $pdf->getError());
			}
		}

	}


	/**
	 * @param Models\Notebook $notebook
	 */
	protected function formGfe($notebook) {

		$path = app_path('docs')."/Blank_GFE.pdf";
		$fdf =  app_path('docs')."/Blank_GFE.fdf";
		$save_path = app_path('docs')."/notebooks/{$notebook->id}/";
		$filename = "GFE.notebook.{$notebook->id}.{$notebook->getHash()}.pdf";

		$deedDoc = $notebook->documents()->where('name','=', 'deed')->first();
		$mortgageDoc = $notebook->documents()->where('name','=', 'Assignment of Mortgage')->first();
		$satisfactionDoc = $notebook->documents()->where('name','=', 'Satisfaction of Mortgage')->first();

		$deedDocCost = $deedDoc?$notebook->getCalculator()->docPageCost($deedDoc):0;
		$mortgageDocCost = $mortgageDoc?$notebook->getCalculator()->docPageCost($mortgageDoc):0;
		$satisfactionDocCost = $satisfactionDoc?$notebook->getCalculator()->docPageCost($satisfactionDoc):0;

		$deedTransferTax = ($deedDoc)?$deedDoc->taxes()->whereIn('name',['Transfer Tax', 'State Transfer Tax', 'Documentary Tax'])->get():null;
		$mortgageDocumentaryTax = ($mortgageDoc)?$mortgageDoc->taxes()->where('name','=','Documentary Tax')->first():null;
		$mortgageIntangibleTax = ($mortgageDoc)?$mortgageDoc->taxes()->where('name','=','Intangible Tax')->first():null;

		$deedTransferTaxCost = 0;
		if (count($deedTransferTax)) {
			foreach ($deedTransferTax as $dt) {
				$deedTransferTaxCost += $notebook->getCalculator()->docTaxCost($deedDoc, $dt);
			}
		}
		$mortgageDocumentaryTaxCost = ($mortgageDoc&&$mortgageDocumentaryTax)?$notebook->getCalculator()->docTaxCost($mortgageDoc, $mortgageDocumentaryTax):0;
		$mortgageIntangibleTaxCost = ($mortgageDoc&&$mortgageDocumentaryTax)?$notebook->getCalculator()->docTaxCost($mortgageDoc, $mortgageIntangibleTax):0;

		$settlement = $notebook->miscs()->where('name','=','Settlement Fee')->first();
		$settlementFee = ($settlement)?$settlement->price:0;

		$fields = [
			'loan-amount' => $notebook->loan_amount, //loan amount
			'T4-1101' => $settlementFee + $notebook->getCalculator()->lenderCost() + $notebook->getCalculator()->endorsementCost(), //1102 + 1104 lenders policy + endorsements
			'T5-1103' => $notebook->getCalculator()->ownerCost(), //owners policy
			'T7-1201' => $deedDocCost + $mortgageDocCost + $satisfactionDocCost,
			'T8-1203' => $mortgageDocumentaryTaxCost + $deedTransferTaxCost + $mortgageIntangibleTaxCost, //total of 1204a/b 1205a/b - calc in pdf
			'loan-amount2' => $notebook->loan_amount, //loan amount
		];

		if (!file_exists($save_path.$filename)) {
			$pdf = new Pdf($path);
			$pdf->fillForm($fields)->needAppearances();

			if (!file_exists($save_path)) { mkdir($save_path, 0777, true); }
			if (!$pdf->saveAs($save_path . $filename)) {
				$this->setFormErrors('export.output', $pdf->getError());
			}
		}

	}

	/**
	 * Just to see what the fields are for testing.
	 */
	public function getFDF() {
		$files = ['Blank_GFE', 'Blank_HUD'];

		foreach ($files as $file) {
			$path = app_path('docs') . "/$file.pdf";
			$fdf = app_path('docs') . "/$file.fdf";

			$pdf = new Pdf($path);

			if (!$pdf->generateFdfFile($fdf)) {
				$this->setFormErrors('export.output', $pdf->getError());
			}
		}
	}

	protected function formEmail($notebook) {
		$inputs = Input::get('export',[]);
		$user = Auth::user();


		$rules = array(
			'email'      => 'required|email',
		);

		$validator = Validator::make($inputs, $rules);

		if ( ($validator->fails()) ) {
			$this->setFormErrors('export', $validator->messages());
		}

		if (empty($this->formErrors)) {

			Mail::send('emails.calculated', compact("notebook", "user"), function ($message) use ($inputs, $notebook) {
				$message
					->from(Config::get('app.contact'), Config::get('app.name'))
					->to($inputs['email'])
					->subject("National Calculator - Export #{$notebook->id}");

			});
		}
	}
}
