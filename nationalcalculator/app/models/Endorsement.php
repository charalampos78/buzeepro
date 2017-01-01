<?php

namespace Models;

/**
 * This is the model class for table "endorsements".
 *
 * @property string $id
 * @property string $state_id
 * @property string $name
 * @property integer $standard_flag
 * @property string $type
 * @property float $amount
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Notebook $notebooks[]
 * @property State $state
 */
class Endorsement extends BaseModel
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
    protected $table = 'endorsements';

    /**
     * Fields that are allowed to be mass assigned
     *
     * @var string
     */
    protected $fillable = ['state_id', 'name', 'standard_flag', 'type', 'amount'];

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
            'state_id' => 'required|integer',
            'name' => 'required|max:128',
            'standard_flag' => 'integer|boolean',
            'amount' => 'required|numeric',
        ]
    ];

    /**
     * List of human readable attribute names for use with a validator.
     *
     * @var array
     */
    public $validationAttributeNames = [
        'id' => 'ID',
        'state_id' => 'State ID',
        'name' => 'Name',
        'standard_flag' => 'Std.',
        'type' => 'Type',
        'amount' => 'Amount',
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
     * @return Notebook
     */
    public function notebooks()
    {
        return $this->belongsToMany('Models\Notebook', 'notebook_endorsements', 'endorsement_id', 'notebook_id');
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