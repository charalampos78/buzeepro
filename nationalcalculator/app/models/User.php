<?php

namespace Models;

//use Illuminate\Auth\UserTrait;
//use Illuminate\Auth\UserInterface;
//use Illuminate\Auth\Reminders\RemindableTrait;
use Controller\Exceptions\CustomException;
use Illuminate\Database\Eloquent\SoftDeletingTrait;
use Laravel\Cashier\Billable;
use Models\Traits\HashingTrait;
use Zizaco\Confide\Support;
use Zizaco\Confide\ConfideUser;
use Zizaco\Confide\ConfideUserInterface;
use Zizaco\Entrust\HasRole;

//use Watson\Validating\ValidatingTrait;

/**
 * This is the model class for table "users".
 *
 * @SWG\Model()
 *
 * @property string $id
 * @property string $username
 * @property string $email
 * @property string $password
 * @property string $confirmation_code
 * @property integer $confirmed
 * @property integer $remote_created_flag
 * @property string $stripe_id
 * @property string $card_brand
 * @property string $card_last_four
 * @property string $remember_token
 * @property string $deleted_at
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Notebook $notebooks[]
 * @property Device $devices[]
 * @property Permission $permissions[]
 * @property Photo $photos[]
 * @property Profile $profile
 * @property Role $roles[]
 */
class User extends BaseModel implements ConfideUserInterface
{

    const PLANS = [
        1 => [
            "code" => "basic",
            "name" => "Prime",
            "cost" => 25,
        ],
        2 => [
            "code" => "premium",
            "name" => "Preferred",
            "cost" => 30,
        ]
    ];

    use SoftDeletingTrait;

    use ConfideUser, HasRole, HashingTrait, Billable /*, ValidatingTrait*/ {
        //ValidatingTrait::isValid insteadof ConfideUser;
        ConfideUser::isValid as dontUseThisOneItsInValidatingTraitFromExtendedModel;
        ConfideUser::save as dontUseThisOneItsAlsoInValidatingTraitFromExtendedModel;
        ConfideUser::errors as dontUseThisEither;
    }

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
    protected $table = 'users';

    /**
     * Fields that are allowed to be mass assigned
     *
     * @var string
     */
    protected $fillable = ['username', 'email', 'password', 'confirmation_code', 'confirmed', 'remember_token'];

    /**
     * Fields that are NOT allowed to be mass assigned
     *
     * @var string
     */
    protected $guarded = ['id', 'deleted_at', 'created_at', 'updated_at'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = array('password', 'remember_token', 'confirmation_code', 'roles');

    /**
     * Fields that can be hashed
     *
     * @var array
     */
    public $hashable = ['password'];

    /**
     * Fields that are Carbon date instances
     *
     * @var array
     */
    protected $dates = [];

    /**
     * Relations to always lazy load
     *
     * @var array
     */
    protected $with = array(
        'profile'
    );

    /**
     * Stores the device the users current auth token belongs to
     *
     * @var Device
     */
    public $current_device;

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
            'user_id' => 'required|exists:users,id',
        ],
        'saving' => [
            'username' => 'required|min:4|max:255|no_spaces|unique:users,username',
            'email' => 'required|max:255|email|unique:users,email',
            'password' => 'required|min:6|max:255',
            //'confirmation_code' => 'required|max:255',
            'confirmed' => 'integer|boolean',
            'remote_created_flag' => 'integer',
            'remember_token' => 'max:100',
        ]
    ];

    /**
     * List of human readable attribute names for use with a validator.
     *
     * @var array
     */
    public $validationAttributeNames = [
        'id' => 'ID',
        'username' => 'Username',
        'email' => 'Email',
        'password' => 'Password',
        'confirmation_code' => 'Confirmation Code',
        'confirmed' => 'Confirmed',
        'remote_created_flag' => 'Remote Created',
//        'stripe_active' => 'Stripe Active',
        'stripe_id' => 'Stripe ID',
//        'stripe_subscription' => 'Stripe Subscription',
//        'stripe_plan' => 'Stripe Plan',
        'card_last_four' => 'Card Last Four',
        'card_brand' => 'Card Type',
//        'trial_ends_at' => 'Trial Ends At',
//        'subscription_ends_at' => 'Subscription Ends At',
        'remember_token' => 'Remember Token',
        'deleted_at' => 'Deleted At',
        'created_at' => 'Created At',
        'updated_at' => 'Updated At',
    ];

    /**
     * Don't need to use ConfideUser save() method since ValidatingTrait has event listener for validation on save.
     * Even though we're aliasing ConfideUser::save to something else, PHP still appears to point to it when going
     * $this->save() :/
     *
     * @param array $options
     *
     * @return bool|void
     */
    public function save(array $options = array())
    {
        return parent::save($options);
    }

    /**
     * We want to use the isValid from the parent with the ValidatingTrait trait version of isValid, not ConfideUser
     *
     * @param null $ruleset
     * @param bool $mergeWithSaving
     *
     * @return bool
     */
    public function isValid($ruleset = null, $mergeWithSaving = true)
    {
        return parent::isValid($ruleset, $mergeWithSaving);
    }

    /**
     * Want to return the errors from the ValidatingTraits error messages, not ConfideUser
     *
     * @return bool
     */
    public function errors()
    {
        return $this->getErrors();
    }

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
        return $this->hasMany('Models\Notebook', 'user_id', 'id' );
    }

    /**
     *
     * @return Device
     */
    public function devices()
    {
        return $this->hasMany('Models\Device', 'user_id', 'id' );
    }

    /**
     *
     * @return Permission
     */
    public function permissions()
    {
        return $this->belongsToMany('Models\Permission', 'permission_user', 'user_id', 'permission_id');
    }

    /**
     *
     * @return Photo
     */
    public function photos()
    {
        return $this->belongsToMany('Models\Photo', 'profiles', 'user_id', 'photo_id');
    }

    /**
     *
     * @return Profile
     */
    public function profile()
    {
        return $this->hasOne('Models\Profile', 'user_id', 'id' );
    }

    /**
     *
     * @return Role
     */
    public function roles()
    {
        return $this->belongsToMany('Models\Role', 'assigned_roles', 'user_id', 'role_id');
    }

    /**
     * Helper method to properly sync a list of new roles
     * 
     * @return void
     */
    public function updateRoles($new_roles) {

        $existing_roles = $this->roles()->select('roles.id')->lists('id');
        sort($existing_roles);
        sort($new_roles);

        if ( ($existing_roles != $new_roles) && (\Auth::check() && $this->id == \Auth::user()->id) ) {
            \Flash::info("Can't update your own roles");
        } elseif ($existing_roles != $new_roles) {
            $this->roles()->sync($new_roles);
        }

    }

    public function getStripePlanInfo($selectedPlan = null) {
        if (!$selectedPlan) {
            $selectedPlan = $this->subscription()->stripe_plan;
        }
        foreach (self::PLANS as $plan) {
            if ($plan['code'] == $selectedPlan) {
                return $plan;
            }
        }
        throw new CustomException([], 400, "Invalid plan");
    }

    public function getReminderEmail()
    {
        return $this->email;
    }

    /**
     * Return url to redirect to after login
     *
     * @return string
     */
    public function getDefaultUrl() {

        if ($this->hasRole('admin')) {
            return '/manage';
        } else {
            return '/members';
        }

    }

}