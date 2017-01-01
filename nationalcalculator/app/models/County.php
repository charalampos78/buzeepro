<?php

namespace Models;

use Illuminate\Database\Eloquent\SoftDeletingTrait;

/**
 * This is the model class for table "counties".
 *
 * @property string $id
 * @property string $state_id
 * @property string $name
 * @property string $fips_code
 * @property string $note
 * @property integer $status_flag
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 *
 * @property Document $documents[]
 * @property Notebook $notebooks[]
 * @property Rate $rates[]
 * @property State $state
 * @property TaxCollector $taxCollectors[]
 * @property Zip $zips[]
 */
class County extends BaseModel
{
    use SoftDeletingTrait;

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
    protected $table = 'counties';

    /**
     * Fields that are allowed to be mass assigned
     *
     * @var string
     */
    protected $fillable = ['state_id', 'name', 'fips_code', 'note', 'status_flag'];

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
            'state_id' => 'required|integer',
            'name' => 'required|max:45',
            'fips_code' => 'max:45',
            'status_flag' => 'integer|boolean',
        ]
    ];

    /**
     * List of human readable attribute names for use with a validator.
     *
     * @var array
     */
    public $validationAttributeNames = [
        'id' => 'ID',
        'state_id' => 'State',
        'name' => 'Name',
        'fips_code' => 'FIPS Code',
        'note' => 'Note',
        'status_flag' => 'Status',
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
     * @return Document
     */
    public function documents()
    {
        return $this->hasMany('Models\Document', 'county_id', 'id' );
    }

    /**
     *
     * @return Notebook
     */
    public function notebooks()
    {
        return $this->hasMany('Models\Notebook', 'county_id', 'id' );
    }

    /**
     *
     * @return Rate
     */
    public function rates()
    {
        return $this->belongsToMany('Models\Rate', 'rate_counties', 'county_id', 'rate_id');
    }

    /**
     *
     * @return State
     */
    public function state()
    {
        return $this->belongsTo('Models\State', 'state_id', 'id' );
    }

	/**
	 *
	 * @return TaxCollector
	 */
	public function taxCollectors()
	{
		return $this->hasMany('Models\TaxCollector', 'county_id', 'id' );
	}

	/**
     *
     * @return Zip
     */
    public function zips()
    {
        return $this->hasMany('Models\Zip', 'county_id', 'id' );
    }

}