<?php

namespace Models;

use Illuminate\Database\Eloquent\SoftDeletingTrait;

/**
 * This is the model class for table "states".
 *
 * @property string $id
 * @property string $abbr
 * @property string $name
 * @property float $owner_min
 * @property float $owner_extra
 * @property float $owner_simultaneous
 * @property float $lender_min
 * @property float $lender_extra
 * @property float $lender_simultaneous
 * @property integer $status_flag
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 *
 * @property County $counties[]
 * @property Endorsement $endorsements[]
 * @property Misc $miscs[]
 * @property Rate $rates[]
 * @property Zip $zips[]
 */
class State extends BaseModel
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
    protected $table = 'states';

    /**
     * Fields that are allowed to be mass assigned
     *
     * @var string
     */
    protected $fillable = ['abbr', 'name', 'owner_min', 'owner_extra', 'owner_simultaneous', 'lender_min', 'lender_extra', 'lender_simultaneous', 'status_flag'];

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
            'abbr' => 'required|max:2',
            'name' => 'required|max:45',
            'owner_min' => 'required|numeric',
            'owner_extra' => 'numeric',
            'owner_simultaneous' => 'required|numeric',
            'lender_min' => 'required|numeric',
            'lender_extra' => 'numeric',
            'lender_simultaneous' => 'required|numeric',
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
        'abbr' => 'Abbr',
        'name' => 'Name',
        'owner_min' => 'Owner Min',
        'owner_extra' => 'Owner Extra',
        'owner_simultaneous' => 'Owner Simultaneous',
        'lender_min' => 'Lender Min',
        'lender_extra' => 'Lender Extra',
        'lender_simultaneous' => 'Lender Simultaneous',
        'status_flag' => 'Status Flag',
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
    public function counties()
    {
        return $this->hasMany('Models\County', 'state_id', 'id' );
    }

    /**
     *
     * @return Endorsement
     */
    public function endorsements()
    {
        return $this->hasMany('Models\Endorsement', 'state_id', 'id' );
    }

    /**
     *
     * @return Misc
     */
    public function miscs()
    {
        return $this->hasMany('Models\Misc', 'state_id', 'id' );
    }

    /**
     *
     * @return Rate
     */
    public function rates()
    {
        return $this->hasMany('Models\Rate', 'state_id', 'id' );
    }
    public function rate_counties()
    {
        return $this->hasMany('Models\Rate', 'state_id', 'id' );
    }

    /**
     *
     * @return Zip
     */
    public function zips()
    {
        return $this->hasMany('Models\Zip', 'state_id', 'id' );
    }

}