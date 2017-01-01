<?php

namespace Models;

/**
 * This is the model class for table "password_reminders".
 *
 * @property string $email
 * @property string $token
 * @property string $created_at
 */
class PasswordReminder extends BaseModel
{

    /**
    * The database connection used by the model.
    *
    * @var string
    */
    //protected $connection = 'homestead';

    /**
    * The database table used by the model.
    *
    * @var string
    */
    protected $table = 'password_reminders';

    /**
    * Fields that are allowed to be mass assigned
    *
    * @var string
    */
    protected $fillable = ['email', 'token'];

    /**
    * Fields that are NOT allowed to be mass assigned
    *
    * @var string
    */
    protected $guarded = ['created_at'];

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
            'email' => 'required|max:255',
            'token' => 'required|max:255',
        ]
    ];

    /**
     * List of human readable attribute names for use with a validator.
     *
     * @var array
     */
    public $validationAttributeNames = [
        'email' => 'Email',
        'token' => 'Token',
        'created_at' => 'Created At',
    ];

    /**
    * Initalizing method to attach observers
    */
    public static function boot() {
        parent::boot();
    }

}