<?php

namespace Models;

use Illuminate\Database\Eloquent\SoftDeletingTrait;

/**
 * This is the model class for table "documents".
 *
 * @property string $id
 * @property string $county_id
 * @property string $name
 * @property float $price_first
 * @property string $price_text
 * @property integer $price_count
 * @property float $price_additional
 * @property integer $status_flag
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 *
 * @property County $county
 * @property DocumentTax $taxes[]
 * @property Notebook $notebooks[]
 */
class Document extends BaseModel
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
    protected $table = 'documents';

    /**
     * Fields that are allowed to be mass assigned
     *
     * @var string
     */
    protected $fillable = ['county_id', 'name', 'price_first', 'price_text', 'price_count', 'price_additional', 'status_flag'];

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
            'county_id' => 'required|integer',
            'name' => 'required|max:45',
            'price_first' => 'required|numeric',
            'price_text' => 'required|max:128',
            'price_count' => 'required|numeric',
            'price_additional' => 'numeric',
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
        'county_id' => 'County ID',
        'name' => 'Name',
        'price_first' => 'Price First',
        'price_text' => 'Price Text',
        'price_count' => '# First Pages',
        'price_additional' => "Price Add'l",
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
    public function county()
    {
        return $this->belongsTo('Models\County', 'county_id', 'id' );
    }

    /**
     *
     * @return DocumentTax
     */
    public function taxes()
    {
        return $this->hasMany('Models\DocumentTax', 'document_id', 'id' );
    }

    /**
     *
     * @return Notebook
     */
    public function notebooks()
    {
        return $this->belongsToMany('Models\Notebook', 'notebook_documents', 'document_id', 'notebook_id');
    }

}