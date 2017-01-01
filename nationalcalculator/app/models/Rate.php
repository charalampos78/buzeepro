<?php

namespace Models;

/**
 * This is the model class for table "rates".
 *
 * @property string $id
 * @property string $state_id
 * @property integer $default_flag
 * @property integer $percent
 * @property integer $extra
 * @property integer $range_min
 * @property integer $range_max
 * @property string $type
 *
 * @property County $counties[]
 * @property State $state
 */
class Rate extends BaseModel
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
    protected $table = 'rates';

    /**
     * If the model needs timestamp created_at and updated_at filled
     *
     * @var string
     */
    public $timestamps = false;

    /**
     * Fields that are allowed to be mass assigned
     *
     * @var string
     */
    protected $fillable = ['state_id', 'default_flag', 'percent', 'extra', 'range_min', 'range_max', 'type'];

    /**
     * Fields that are NOT allowed to be mass assigned
     *
     * @var string
     */
    protected $guarded = ['id'];

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
            'default_flag' => 'integer|boolean',
            'percent' => 'required|numeric',
            'extra' => 'numeric',
            'range_min' => 'required|integer|column_lesser:range_max',
            'range_max' => 'required|integer|column_greater:range_min',
            'type' => 'required|in:owner,lender',
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
        'default_flag' => 'Default Flag',
        'percent' => '% / $',
        'extra' => 'Extra$',
        'range_min' => 'R Min',
        'range_max' => 'R Max',
        'type' => 'Type',
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
    public function counties()
    {
        return $this->belongsToMany('Models\County', 'rate_counties', 'rate_id', 'county_id');
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