<?php

namespace Models;

use Illuminate\Database\Eloquent\SoftDeletingTrait;
use My\Calculator\NCCalc;

/**
 * This is the model class for table "notebooks".
 *
 * @property string $id
 * @property string $user_id
 * @property string $zip_id
 * @property string $county_id
 * @property string $name
 * @property string $type
 * @property integer $purchase_price
 * @property integer $loan_amount
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 *
 * @property County $county
 * @property Document $documents[]
 * @property Endorsement $endorsements[]
 * @property Misc $miscs[]
 * @property User $user
 * @property Zip $zip
 */
class Notebook extends BaseModel
{
    use SoftDeletingTrait;

	/**
	 * @var NCCalc
	 */
	protected $calculator = null;

    /**
     * The database connection used by the model.
     *
     * @var string
     */
    //protected $connection = 'nc';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'notebooks';

    /**
     * Fields that are allowed to be mass assigned
     *
     * @var string
     */
    protected $fillable = ['user_id', 'zip_id', 'county_id', 'name', 'type', 'purchase_price', 'loan_amount'];

	/**
	 * Fields that will be set null if set to empty
	 * @var array
	 */
	protected $forcedNullFields = ['purchase_price', 'loan_amount'];

    /**
     * Fields that are NOT allowed to be mass assigned
     *
     * @var string
     */
    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = array();

    /**
     * Rules used in different cases
     *
     * @var array
     */
    public $rulesets = [
        'creating' => [
        ],
        'updating' => [
        ],
        'deleting' => [
        ],
        'saving' => [
            'user_id' => 'required|integer',
            'zip_id' => 'required|integer',
            'county_id' => 'required|integer',
            'name' => 'max:128',
            'type' => 'required|in:purchase,cash,refinance',
            'purchase_price' => 'integer|required_if:type,purchase,cash',
            'loan_amount' => 'integer|required_if:type,purchase,refinance',
        ]
    ];

    /**
     * List of human readable attribute names for use with a validator.
     *
     * @var array
     */
    public $validationAttributeNames = [
        'id' => 'ID',
        'user_id' => 'User ID',
        'zip_id' => 'Zip ID',
        'county_id' => 'County ID',
        'name' => 'Name',
        'type' => 'Type',
        'purchase_price' => 'Purchase Price',
        'loan_amount' => 'Loan Amount',
        'created_at' => 'Created At',
        'updated_at' => 'Updated At',
        'deleted_at' => 'Deleted At',
    ];

    /**
     * Initalizing method to attach observers
     */
    public static function boot() {
        parent::boot();
    }

    /**
     *
     * @return County
     */
    public function county()
    {
        return $this->belongsTo('Models\County', 'county_id', 'id' );
    }

    /**
     *
     * @return Document
     */
    public function documents()
    {
        return $this->belongsToMany('Models\Document', 'notebook_documents', 'notebook_id', 'document_id')->withPivot('pages');
    }

    /**
     *
     * @return Endorsement
     */
    public function endorsements()
    {
        return $this->belongsToMany('Models\Endorsement', 'notebook_endorsements', 'notebook_id', 'endorsement_id');
    }

    /**
     *
     * @return Misc
     */
    public function miscs()
    {
        return $this->belongsToMany('Models\Misc', 'notebook_miscs', 'notebook_id', 'misc_id');
    }

    /**
     *
     * @return User
     */
    public function user()
    {
        return $this->belongsTo('Models\User', 'user_id', 'id' );
    }

    /**
     *
     * @return Zip
     */
    public function zip()
    {
        return $this->belongsTo('Models\Zip', 'zip_id', 'id' );
    }

	/**
	 * @return NCCalc
	 */
	public function getCalculator() {
		if (!$this->calculator || ($this->calculator->getState() != $this->county->state->abbr)) {
			$this->calculator = NCCalc::generate($this);
		}
		return $this->calculator;
	}

	public function getHash() {
		$data = $this->toArray();
		unset($data['created_at']);
		unset($data['updated_at']);
		unset($data['deleted_at']);
		$data = implode(",", $data);

		return md5($data);

	}

}