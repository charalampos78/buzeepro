<?php

namespace Models;

/**
 * This is the model class for table "profiles".
 *
 * @property string $user_id
 * @property string $photo_id
 * @property string $first_name
 * @property string $last_name
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Photo $photo
 * @property User $user
 */
class Profile extends BaseModel
{

    /**
     * The database connection used by the model.
     *
     * @var string
     */
    //protected $connection = 'homestead';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'user_id';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'profiles';

    /**
     * Fields that are allowed to be mass assigned
     *
     * @var string
     */
    protected $fillable = ['user_id', 'photo_id', 'first_name', 'last_name'];

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
            'photo_id' => 'integer',
            'first_name' => 'required|max:128',
            'last_name' => 'required|max:128',
        ]
    ];

    /**
     * List of human readable attribute names for use with a validator.
     *
     * @var array
     */
    public $validationAttributeNames = [
        'user_id' => 'User ID',
        'photo_id' => 'Photo ID',
        'first_name' => 'First Name',
        'last_name' => 'Last Name',
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
     * @return Photo
     */
    public function photo()
    {
        return $this->belongsTo('Models\Photo', 'photo_id', 'id' );
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