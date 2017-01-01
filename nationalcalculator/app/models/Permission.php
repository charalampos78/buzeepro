<?php

namespace Models;

//use Zizaco\Entrust\EntrustPermission;
//use Watson\Validating\ValidatingTrait;
use Config, Exception;


/**
 * This is the model class for table "permissions".
 *
 * @property string $id
 * @property string $name
 * @property string $display_name
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Role $roles[]
 */
class Permission extends BaseModel //EntrustPermission //doesn't need entrust, all methods are here
{
    //use ValidatingTrait;

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
    protected $table = 'permissions';

    /**
     * Fields that are allowed to be mass assigned
     *
     * @var string
     */
    protected $fillable = ['name', 'display_name'];

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
            'name' => 'required|between:4,128',
            'display_name' => 'required|between:4,128'
        ]
    ];

    /**
     * List of human readable attribute names for use with a validator.
     *
     * @var array
     */
    public $validationAttributeNames = [
        'id' => 'ID',
        'name' => 'Name',
        'display_name' => 'Display Name',
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
     * Before delete all constrained foreign relations
     *
     * @param bool $forced
     * @return bool
     */
    public function beforeDelete( $forced = false )
    {
        try {
            \DB::table(Config::get('entrust::permission_role_table'))->where('permission_id', $this->id)->delete();
        } catch(Exception $e) {}

        return true;
    }

    /**
     *
     * @return Roles
     */
    public function roles()
    {
        return $this->belongsToMany('Models\Role', 'permission_role', 'permission_id', 'role_id');
    }

}