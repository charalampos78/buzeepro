<?php

namespace Models;

/**
 * This is the model class for table "miscs".
 *
 * @property string $id
 * @property string $state_id
 * @property string $name
 * @property float $price
 *
 * @property Notebook $notebooks[]
 * @property State $state
 */
class Misc extends BaseModel
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
    protected $table = 'miscs';

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
    protected $fillable = ['state_id', 'name', 'price'];

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
            'name' => 'required|max:128',
            'price' => 'required|numeric',
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
        'price' => 'Price',
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
        return $this->belongsToMany('Models\Notebook', 'notebook_miscs', 'misc_id', 'notebook_id');
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