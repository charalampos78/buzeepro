<?php

namespace My\Calculator;

/**
 * Class NCCalc
 * @package My
 *
 */
class NCCalc {

	/**
	 * @var \Models\Notebook
	 */
	protected $notebook;

	protected $ownerCost = null;
	protected $lenderCost = null;

	public function __construct($notebook) {
		$this->notebook = $notebook;
	}

	/**
	 * @param \Models\Notebook $notebook
	 *
	 * @return NCCalc
	 */
	public static function generate($notebook) {

		$state = $notebook->county->state->abbr;
		$class = "My\\Calculator\\States\\".$state;

		if (class_exists($class)) {
			return new $class($notebook);
		}

		return new NCCalc($notebook);
	}

	public function getState() {
		return $this->notebook->county->state->abbr;
	}


	public function ownerCost() {
		if (!$this->ownerCost) {
			$this->ownerCost = $this->policyCost("owner");
		}
		return $this->ownerCost;
	}
	public function lenderCost() {
		if (!$this->lenderCost) {
			$this->lenderCost = $this->policyCost("lender");
		}
		return $this->lenderCost;
	}

	/**
	 *
	 * @param $policy
	 *
	 * @return int|mixed|string
	 */
	public function policyCost($policy) {
		/** @var \Models\Rate $rate */

		$state = $this->notebook->county->state;

		$policy_amount = 0;
		switch ($this->notebook->type) {
			case "purchase":
				if ($policy == "lender" &&  (int)$state->lender_simultaneous ) {
					return (int)$state->lender_simultaneous;
				} elseif ($policy == "lender") {
					$policy_amount = $this->notebook->loan_amount;
				} elseif ($policy == "owner") {
					$policy_amount = $this->notebook->purchase_price; # ( loan + cash )
				}
				break;
			case "cash":
				if ($policy == "lender") {
					return 0;
				}
				$policy_amount = $this->notebook->purchase_price; # ( cash )
				break;
			case "refinance":
				if ($policy == "owner") {
					return 0;
				}
				$policy_amount = $this->notebook->loan_amount; # ( loan )
				break;
		}

		$rates = $this->getRates($policy);

		$total = 0;
		$max_fixed = 0;
		$extra = 0;
		foreach ($rates as $rate) {
			$type = ($rate->percent <= 1) ? "percent" : "fixed";
			$rate_extra = ($rate->extra != 0) ? $rate->extra : $extra;
			switch ($type) {
				case 'percent' :
					if ($policy_amount >= $rate->range_max) {
						$total += ($rate->range_max - $rate->range_min) * ($rate->percent / 100);
						$extra = $rate_extra;
					} elseif ($rate->range_min < $policy_amount && $policy_amount < $rate->range_max) {
						$total += ($policy_amount - $rate->range_min) * ($rate->percent / 100);
						$extra = $rate_extra;
						break 2;
					}
					break;
				case 'fixed' :
					if ($rate->range_min < $policy_amount ) {
						$max_fixed = $rate->percent;
						$extra = $rate_extra;
					}
					break;
			}
		}
		$total += $max_fixed;

		$total += $extra;

		$policy_min = $policy."_min";
		$policy_extra = $policy."_extra";

		if ($total < $state->$policy_min) {
			$total = $state->$policy_min;
		}

		if ($state->$policy_extra) {
			$total += $state->$policy_extra;
		}

		return $total;
	}

	protected function getRates($policy) {

		$county = $this->notebook->county;

		$rates = $county->rates()->where('type',$policy)->orderBy('range_min')->get();

		if (!$rates->count()) {
			$rates = $county->state->rates()->has('counties', '=', 0)->where('type',$policy)->orderBy('range_min')->get();
		}

		return $rates;

	}

	/**
	 * @param \Models\Document $doc
	 *
	 * @return int
	 */
	public function docPageCost($doc = null) {
		$total = 0;
		if ($doc) {
			//todo: add special calculating class that cascades through states
			//calculate total here
			$pages = $doc->pivot->pages;

			if ($pages > 0) {
				$total += $doc->price_first;

				if ($doc->price_additional && $pages > $doc->price_count) {
					$total += $doc->price_additional * ($pages - $doc->price_count);
				}
			}
		} else {
			foreach ($this->notebook->documents as $doc) {
				$total += $this->docPageCost($doc);
			}
		}

		return $total;
	}
	/**
	 * @param \Models\Document $doc
	 *
	 * @return int
	 */
	public function docTaxCost($doc = null, $doc_tax = null) {
		$total = 0;
		if ($doc) {
			//todo: add special calculating class that cascades through states
			//calculate total here
			$pages = $doc->pivot->pages;
			if ($pages > 0) {

				if ($doc_tax) {
					$taxes = [$doc_tax];
				} else {
					$taxes = $doc->taxes;
				}

				foreach ($taxes as $tax) {
					switch ($tax->type) {
						case 'loan':
							$total += $this->notebook->loan_amount * ($tax->percent );
							break;
						case 'sales':
							$total += $this->notebook->purchase_price * ($tax->percent );
							break;
						case 'sales-loan':
							$total += ( $this->notebook->purchase_price - $this->notebook->loan_amount) * ($tax->percent );
							break;
						case 'fixed':
							$total += $tax->percent;
							break;
					}
				}
			}
		} else {
			foreach ($this->notebook->documents as $doc) {
				$total += $this->docTaxCost($doc);
			}
		}

		return $total;
	}

	/**
	 * @param \Models\Endorsement $e
	 *
	 * @return int
	 */
	public function endorsementCost($e = null) {
		$total = 0;
		if ($e) {
			//todo: add special calculating class that cascades through states
			//calculate total here
			switch ($e->type) {
				case 'fixed':
					$total += $e->amount;
					break;
				case 'percent':
					$state = $this->notebook->county->state;
					$percent_of = $this->lenderCost();
					if ($state->lender_simultaneous) {
						$percent_of += $this->ownerCost();
					}
					//$percent_of = ($this->notebook->loan_amount)?:$this->notebook->purchase_price;
					$total += $percent_of * ($e->amount / 100);
					break;
			}

		} else {
			foreach ($this->notebook->endorsements as $e) {
				$total += $this->endorsementCost($e);
			}
		}

		return $total;
	}

	/**
	 * @param \Models\Endorsement $e
	 *
	 * @return array
	 */
	public function endorsementList($e = null) {
		$total = [];
		if ($e) {
			$matches = [];
			preg_match("/[0-9]+[[.-]?[0-9]+]?/i", $e->name, $matches);
			//preg_match("/[0-9]*[.-]?[0-9]?/i", $e->name, $matches);
			$total[] = $matches[0];
		} else {
			foreach ($this->notebook->endorsements as $e) {
				$total = array_merge($total, $this->endorsementList($e));
			}
		}
		return array_filter($total);
	}

	/**
	 * @param \Models\Misc $misc
	 *
	 * @return int
	 */
	public function miscCost($misc = null) {
		$total = 0;
		if ($misc) {
			//todo: add special calculating class that cascades through states
			//calculate total here
			$total += $misc->price;

		} else {
			foreach ($this->notebook->miscs as $misc) {
				$total += $this->miscCost($misc);
			}
		}

		return $total;
	}


	/**
	 * @return int
	 */
	public function totalCost() {
		$total = 0;

		$total += $this->docPageCost();
		$total += $this->docTaxCost();
		$total += $this->endorsementCost();
		$total += $this->miscCost();
		$total += $this->ownerCost();
		$total += $this->lenderCost();

		return $total;

	}

	public function toArray() {

		$notebookArray = [
			'name' => $this->notebook->name,
			'type' => $this->notebook->type,
			'city' => $this->notebook->zip->city,
			'zip' => $this->notebook->zip->zip,
			'state' => $this->notebook->zip->state->abbr,
			'county' => $this->notebook->county->name,
			'countyNote' => $this->notebook->county->note,
			'purchasePrice' => (int) $this->notebook->purchase_price,
			'loanAmount' => (int) $this->notebook->loan_amount,
			'ownerCost' => $this->ownerCost(),
			'lenderCost' => $this->lenderCost(),
			'documents' => [],
			'documentCost' => $this->docTaxCost() + $this->docPageCost(),
			'endorsements' => [],
			'endorsementCost' => $this->endorsementCost(),
			'miscs' => [],
			'miscCost' => $this->miscCost(),
			'totalCost' => $this->totalCost(),
		];

		foreach ($this->notebook->documents as $doc) {
			$taxes = [];
			foreach ($doc->taxes as $tax) {
				$taxes[] = [
					'type' => $tax->type,
					'name' => $tax->name,
					'cost' => $this->docTaxCost($doc, $tax),
				];
			}
			$notebookArray['documents'][] = [
				'name' => $doc->name,
				'pages' => (int) $doc->pivot->pages,
				// 'price_text' => $doc->price_text,
				// 'price_first' => $doc->price_first,
				// 'price_additional' => $doc->price_additional,
				'cost' => $this->docPageCost($doc),
				'taxes' => $taxes,
			];
		}

		foreach ($this->notebook->endorsements as $e) {
			$notebookArray['endorsements'][] = [
				'name' => $e->name,
				'type' => $e->type,
				'cost' => $this->endorsementCost($e),
			];
		};

		foreach ($this->notebook->miscs as $m) {
			$notebookArray['miscs'][] = [
				'name' => $m->name,
				'cost' => $this->miscCost($m),
			];
		};

		return $notebookArray;
	}
}
