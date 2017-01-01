<?php

namespace Models;

//use Zizaco\Entrust\EntrustRole;
//use Watson\Validating\ValidatingTrait;
use Models\Traits\EntrustRoleTrait;

/**
 * This is the model class for table "roles".
 *
 * @property string $id
 * @property string $name
 * @property string $description
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Permission $permissions[]
 * @property User $users[]
 */
class Role extends BaseModel //EntrustRole
{

	const LOGIN = 1;
	const ADMIN = 2;


    //use ValidatingTrait;
    use EntrustRoleTrait; //created EntrustTrait so doesn't have to be extended from

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
    protected $table = 'roles';

    /**
    * Fields that are allowed to be mass assigned
    *
    * @var string
    */
    protected $fillable = ['name', 'description', 'created_at', 'updated_at'];

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
            'name' => 'required|max:255|unique:roles,name',
            'description' => 'max:255',
        ]
    ];

    /**
     * List of human readable attribute names for use with a validator.
     *
     * @var array
     */
    public $validationAttributeNames = [
        'id' => 'ID',
        'name' => 'Role',
        'description' => 'Description',
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
     * @return Permission
     */
    public function permissions()
    {
        return $this->belongsToMany('Models\Permission', 'permission_role', 'role_id', 'permission_id');
    }

    /**
     *
     * @return User
     */
    public function users()
    {
        return $this->belongsToMany('Models\User', 'assigned_roles', 'role_id', 'user_id');
    }

}