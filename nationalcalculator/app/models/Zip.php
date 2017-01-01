<?php

namespace Models;

/**
 * This is the model class for table "zips".
 *
 * @property string $id
 * @property string $zip
 * @property string $city
 * @property int $primary_county
 * @property int $multi_county
 * @property string $county_id
 * @property string $state_id
 * @property integer $status_flag
 * @property string $created_at
 * @property string $updated_at
 *
 * @property County $county
 * @property Notebook $notebooks[]
 * @property State $state
 */
class Zip extends BaseModel
{

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
    protected $table = 'zips';

    /**
     * Fields that are allowed to be mass assigned
     *
     * @var string
     */
    protected $fillable = ['zip', 'city', 'primary_county', 'multi_county', 'county_id', 'state_id', 'status_flag'];

    /**
     * Fields that are NOT allowed to be mass assigned
     *
     * @var string
     */
    protected $guarded = ['id', 'created_at', 'updated_at'];

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
            'zip' => 'required|max:45',
            'city' => 'max:128',
            'primary_county' => 'numeric',
            'multi_county' => 'numeric',
            'county_id' => 'required|integer',
            'state_id' => 'required|integer',
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
        'zip' => 'Zip',
        'city' => 'City',
        'primary_county' => 'Primary County',
        'multi_county' => 'Multiple County',
        'county_id' => 'County',
        'state_id' => 'State',
        'status_flag' => 'Status',
        'created_at' => 'Created At',
        'updated_at' => 'Updated At',
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
     * @return Notebook
     */
    public function notebooks()
    {
        return $this->hasMany('Models\Notebook', 'zip_id', 'id' );
    }

    /**
     *
     * @return State
     */
    public function state()
    {
        return $this->belongsTo('Models\State', 'state_id', 'id' );
    }

}