<?php

namespace Models;

/**
 * This is the model class for table "tax_collectors".
 *
 * @property string $id
 * @property string $county_id
 * @property string $municipality
 * @property string $commissioner
 * @property string $address
 * @property string $city
 * @property string $state
 * @property string $zip
 * @property string $email
 * @property string $phone
 * @property string $phone2
 * @property string $fax
 * @property string $fax2
 * @property string $website
 * @property string $paysite
 * @property string $m_address
 * @property string $m_city
 * @property string $m_state
 * @property string $m_zip
 * @property string $created_at
 * @property string $updated_at
 *
 * @property County $county
 */
class TaxCollector extends BaseModel
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
    protected $table = 'tax_collectors';

    /**
     * Fields that are allowed to be mass assigned
     *
     * @var string
     */
    protected $fillable = ['county_id', 'municipality', 'commissioner', 'address', 'city', 'state', 'zip', 'email', 'phone', 'phone2', 'fax', 'fax2', 'website', 'paysite', 'm_address', 'm_city', 'm_state', 'm_zip'];

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
            'county_id' => 'required|integer',
            'municipality' => 'max:128',
            'commissioner' => 'max:256',
            'address' => 'max:128',
            'city' => 'max:128',
            'state' => 'max:3',
            'zip' => 'max:10',
            'email' => 'max:128',
            'phone' => 'max:30',
            'phone2' => 'max:30',
            'fax' => 'max:30',
            'fax2' => 'max:30',
            'website' => 'url|max:256',
            'paysite' => 'url|max:256',
            'm_address' => 'max:128',
            'm_city' => 'max:128',
            'm_state' => 'max:3',
            'm_zip' => 'max:10',
        ]
    ];

    /**
     * List of human readable attribute names for use with a validator.
     *
     * @var array
     */
    public $validationAttributeNames = [
        'id' => 'ID',
        'county_id' => 'County ID',
        'municipality' => 'Municipality',
        'commissioner' => 'Revenue Commissioner',
        'address' => 'Address',
        'city' => 'City',
        'state' => 'State',
        'zip' => 'Zip',
        'email' => 'Email',
        'phone' => 'Phone',
        'phone2' => 'Phone Alt',
        'fax' => 'Fax',
        'fax2' => 'Fax Alt',
        'website' => 'Website',
        'paysite' => 'Pay Site',
        'm_address' => 'Mailing Address',
        'm_city' => 'City',
        'm_state' => 'State',
        'm_zip' => 'Zip',
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

}