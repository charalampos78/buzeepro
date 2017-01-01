<?php

namespace Models;

/**
 * This is the model class for table "contents".
 *
 * @property string $id
 * @property string $key
 * @property string $name
 * @property string $content
 * @property integer $locked_flag
 * @property string $created_at
 * @property string $updated_at
 */
class Content extends BaseModel
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
    protected $table = 'contents';

    /**
     * Fields that are allowed to be mass assigned
     *
     * @var string
     */
    protected $fillable = ['key', 'name', 'content', 'locked_flag'];

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
            'key' => 'max:128|unique:contents,key|required',
            'name' => 'max:128|required',
            'content' => 'required',
            'locked_flag' => 'integer|boolean',
        ]
    ];

    /**
     * List of human readable attribute names for use with a validator.
     *
     * @var array
     */
    public $validationAttributeNames = [
        'id' => 'ID',
        'key' => 'Key',
        'name' => 'Name',
        'content' => 'Content',
        'locked_flag' => 'Locked Flag',
        'created_at' => 'Created At',
        'updated_at' => 'Updated At',
    ];

    /**
     * Initalizing method to attach observers
     */
    public static function boot() {
        parent::boot();
    }

}