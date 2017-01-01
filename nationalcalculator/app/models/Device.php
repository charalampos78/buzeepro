<?php

namespace Models;

/**
 * This is the model class for table "devices".
 *
 * @property string $id
 * @property string $user_id
 * @property string $type
 * @property string $device_number
 * @property string $auth_token
 * @property string $push_token
 * @property string $user_agent
 * @property string $created_at
 * @property string $updated_at
 *
 * @property User $user
 */
class Device extends BaseModel
{

    /**
     * The database connection used by the model.
     *
     * @var string
     */
    //protected $connection = 'rasterly';

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
    protected $table = 'devices';

    /**
     * Fields that are allowed to be mass assigned
     *
     * @var string
     */
    protected $fillable = ['type', 'device_number', 'push_token', 'user_agent'];

    /**
     * Fields that are NOT allowed to be mass assigned
     *
     * @var string
     */
    protected $guarded = ['created_at', 'updated_at'];

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
            'device_number' => 'max:255',
            'auth_token' => 'max:100',
            'push_token' => 'max:255',
            'user_agent' => 'max:255',
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
        'type' => 'Type',
        'device_number' => 'Device Number',
        'auth_token' => 'Auth Token',
        'push_token' => 'Push Token',
        'user_agent' => 'User Agent',
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
     * @return User
     */
    public function user()
    {
        return $this->belongsTo('Models\User', 'user_id', 'id' );
    }

}