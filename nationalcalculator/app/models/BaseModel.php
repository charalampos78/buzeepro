<?php

namespace Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Watson\Validating\ValidatingTrait;
use Eloquent;
use Illuminate\Database\Query\Expression;

class BaseModel extends Eloquent
{
    use ValidatingTrait;

	protected $forcedNullFields = [];

	protected $rules = [
    ];

    protected $validationMessages = [
        'example_field.unique' => "Field not unique."
    ];

    /**
     * This determines the foreign key relations automatically to prevent the need to figure out the columns.
     *
     * @param \Illuminate\Database\Query\Builder $query
     * @param string $relation_name
     * @param string $operator
     * @param string $type
     * @param bool   $where
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeModelJoin($query, $relation_name, $operator = '=', $type = 'left', $where = false) {
        $relation = $this->$relation_name();
        $table = $relation->getRelated()->getTable();
        if ($relation instanceof BelongsTo) {
            //this tables key pointing to remote table
            $one = $this->getTable() . "." . $relation->getForeignKey();
            //remote table primary key
            $two = $relation->getRelated()->getQualifiedKeyName();
        } else { //HasMany or HasOne
            //current table id column
            $one = $relation->getParent()->getQualifiedKeyName();
            //remote table column related to this id
            $two = $relation->getForeignKey();
        }
        //counties.state_id = state.id
        if (empty($query->columns)) {
            $query->select($this->getTable().".*");
        }

        //$join_alias = $table;
        $prefix = $query->getQuery()->getGrammar()->getTablePrefix();
        $join_alias = $relation_name;
        foreach (\Schema::getColumnListing($table) as $related_column) {
            $query->addSelect(\DB::raw("`$prefix$join_alias`.`$related_column` AS `$join_alias.$related_column`"));
        }
        $two = str_replace($table . ".", $join_alias . ".", $two);
        return $query->join("$table AS $prefix$relation_name", $one, $operator, $two, $type, $where); //->with($relation_name);

    }

    /**
     * This extends the parent method.  If the relation is BelongsTo or HasOne, and the relation doesn't actually exist,
     * it returns a new instance of that relation instead.
     *
     * @param  string  $key
     * @param  string  $camelKey
     * @return mixed
     */
    protected function getRelationshipFromMethod($key, $camelKey)
    {
        $results = parent::getRelationshipFromMethod($key, $camelKey);

        $relations = $this->$camelKey();
        if ( ( !$this->exists || !$results ) && ( $relations instanceof BelongsTo || $relations instanceof HasOne) ) {
            $results = $relations->getRelated()->newInstance();
        }

        return $this->relations[$key] = $results;

    }

	public static function boot()
	{
		parent::boot();

		static::saving(function($model)
		{
			if (count($model->forcedNullFields) > 0) {
				foreach ($model->forcedNullFields as $fieldName) {
					$value = $model->{$fieldName};
					if (!is_null($value) && empty($value) && strlen($value) == 0) {
						$model->{$fieldName} = null;
					}

				}
			}

			return true;
		});

	}

}