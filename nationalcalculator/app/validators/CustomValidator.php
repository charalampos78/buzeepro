<?php

namespace Validators;

use Illuminate\Validation\Validator;
use Auth;
use Models;


class CustomValidator extends Validator {

    private $_custom_messages = array(
        "model_column_matches" => "Column doesn't match",
        "no_spaces" => ":attribute may not contain spaces",
        "column_greater" => ":attribute must be greater than :other",
        "column_lesser" => ":attribute must be less than :other",
    );

    public function __construct( $translator, $data, $rules, $messages = array(), $customAttributes = array() ) {
        parent::__construct( $translator, $data, $rules, $messages, $customAttributes );

        $this->_set_custom_stuff();
    }

    /**
     * Setup any customizations etc
     *
     * @return void
     */
    protected function _set_custom_stuff() {
        //setup our custom error messages
        $this->setCustomMessages( $this->_custom_messages );
    }

    /**
     * Confirms a specific value from one tables column matches current column
     *
     * ex) rule: 'thing_id' => model_column_matches:Thing,thing_column,this_value'
     *            makes sure that Models\Thing::find($this->data->thing_id)->thing_column == $this->data->this_value
     */
    public function validateModelColumnMatches($attribute, $value, $parameters)
    {
        $this->requireParameterCount(3, $parameters, 'model_column_matches');

        $model_name = "Models\\".$parameters[0];
        $model_column = $parameters[1];
        $matches_value_of = array_get($this->data, $parameters[2]);

        $model = $model_name::find($value);
        if (!$model) return false;

        return $model->$model_column == $matches_value_of;

    }

	/**
	 * Compares one column is greater than another
	 *
	 * @param $attribute
	 * @param $value
	 * @param $parameters
	 *
	 * @return bool
	 */
    public function validateColumnGreater($attribute, $value, $parameters)
    {
        $this->requireParameterCount(1, $parameters, 'column_greater');

		return $value > array_get($this->data, $parameters[0]);

    }
	/**
	 * Compares one column is lesser than another
	 *
	 * @param $attribute
	 * @param $value
	 * @param $parameters
	 *
	 * @return bool
	 */
    public function validateColumnLesser($attribute, $value, $parameters)
    {
        $this->requireParameterCount(1, $parameters, 'column_lesser');

		return $value < array_get($this->data, $parameters[0]);

    }

    public function validateNoSpaces($attribute, $value)
    {
        return strpos($value, " ") === false;
    }




    /**
     * @param $message
     * @param $attribute
     * @param $rule
     * @param $parameters
     *
     * @return mixed
     */
    protected function replaceColumnGreater($message, $attribute, $rule, $parameters)
    {
		$parameters[0] = $this->getAttribute($parameters[0]);
		return str_replace(':other', $parameters[0], $message);
    }

    protected function replaceColumnLesser($message, $attribute, $rule, $parameters)
    {
		$parameters[0] = $this->getAttribute($parameters[0]);
        return str_replace(':other', $parameters[0], $message);
    }

}
