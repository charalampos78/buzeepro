<?php

namespace Models;

/**
 * This is the model class for table "document_tax".
 *
 * @property string $id
 * @property string $document_id
 * @property string $name
 * @property string $percent
 * @property string $type
 *
 * @property Document $document
 */
class DocumentTax extends BaseModel
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
    protected $table = 'document_tax';

    /**
     * If the model needs timestamp created_at and updated_at filled
     *
     * @var string
     */
    public $timestamps = false;

    /**
     * Fields that are allowed to be mass assigned
     *
     * @var string
     */
    protected $fillable = ['document_id', 'name', 'percent', 'type'];

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
            'document_id' => 'required|integer',
            'name' => 'required|max:128',
            'percent' => 'required|numeric',
            'type' => 'required|in:loan,sales,fixed,sales-loan',
        ]
    ];

    /**
     * List of human readable attribute names for use with a validator.
     *
     * @var array
     */
    public $validationAttributeNames = [
        'id' => 'ID',
        'document_id' => 'Document ID',
        'name' => 'Name',
        'percent' => 'Multiplier / Fee',
        'type' => 'Type',
    ];

    /**
     * Initalizing method to attach observers
     */
    public static function boot() {
        parent::boot();
    }

    /**
     *
     * @return Document
     */
    public function document()
    {
        return $this->belongsTo('Models\Document', 'document_id', 'id' );
    }

}