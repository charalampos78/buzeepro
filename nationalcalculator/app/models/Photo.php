<?php

namespace Models;

/**
 * This is the model class for table "photos".
 *
 * @property string $id
 * @property string $path
 * @property string $caption
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Profile $profiles[]
 * @property User $users[]
 */
class Photo extends BaseModel
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
    protected $table = 'photos';

    /**
     * Fields that are allowed to be mass assigned
     *
     * @var string
     */
    protected $fillable = ['path', 'caption'];

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
            'path' => 'required|max:255',
            'caption' => 'max:255',
        ]
    ];

    /**
     * List of human readable attribute names for use with a validator.
     *
     * @var array
     */
    public $validationAttributeNames = [
        'id' => 'ID',
        'path' => 'Path',
        'caption' => 'Caption',
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
     * @return Profile
     */
    public function profiles()
    {
        return $this->hasMany('Models\Profile', 'photo_id', 'id' );
    }

    /**
     *
     * @return User
     */
    public function users()
    {
        return $this->belongsToMany('Models\User', 'profiles', 'photo_id', 'user_id');
    }

}