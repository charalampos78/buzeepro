<?php

namespace My\MForm;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Html\FormBuilder;
use Illuminate\Support\Arr;

/**
 * Class MForm
 */
class MForm extends FormBuilder {

    protected $bootstrap;
    protected $modelName;
    protected $extra = [];


    /**
     * Returns a name from the predefined validationAttributeNames on the model, or
     * transformed into pretty print
     * Eg. ThiS_is.Nod => ThiS Is Nod
     * @param $name
     *
     * @return string
     */
    public function getPrettyName($name, $model = null) {
        if (!$name) return $name;

        if (!$model) $model = $this->model;

        //remove items like {#}
        $name = preg_replace('/[\.]?(\{|\[)[0-9]*(\}|\])[\.]?/',".", $name);
        if (preg_match('/\[|\]/', $name)) $name = $this->transformKey($name);

        if ($model && property_exists($model, "validationAttributeNames")
            && isset($model->validationAttributeNames[$name])
        ) {
            return $model->validationAttributeNames[$name];
        }
        
        if (strpos($name, '.') !== false) {
            $names = explode(".", $name);
            if ($model == $this->model && $names[0] == $this->modelName) array_shift($names);
            $subModelName = array_shift($names);
            if (method_exists($model, $subModelName)) {
                $relation = $model->$subModelName();
                if (method_exists($relation, 'getRelated')) {
                    return $this->getPrettyName(implode('.', $names), $relation->getRelated());
                }
            }
        }

        $prettyName = array_filter(preg_split('/((?=[A-Z]![A-Z])|_| |\.)/', $name));
        if (!$model && (strpos($name, '.') !== false)) { array_shift($prettyName); }
        $prettyName = ucwords(implode(' ', $prettyName));

        return $prettyName;

    }

    /**
     * Returns bracketed name for model
     */
    protected function getInputName($name, $multiple = false) {
        if (!$name) return $name;

        $name_suffix = ($multiple) ? '[]' : '';

        $name = trim($name, " ._");
		//if there are brackets [] that aren't a number, then already formatted so return
        if (preg_match('/\[[0-9]+\]/', $name) != 1 && preg_match('/\[|\]/', $name) == 1) {
            return $name.$name_suffix;
        }

        $modelName = "";
        if ($this->model) {
            $modelName = $this->modelName;
        } elseif (strpos($name, '.')) {
            $name = explode(".", $name);
            $modelName = array_shift($name);
            $name = implode(".", $name);
        }

        if (strpos($name, '.') === false) {
            if (empty($modelName)) return str_replace(' ', '-', $name).$name_suffix;
            return $modelName."[$name]".$name_suffix;
        } else {
            $names = explode('.', $name);
            if ($names[0] == $modelName) array_shift($names);
            $names = array_map(function($val) { return trim($val, "[]"); }, $names );
            return $modelName."[". implode("][", $names) ."]".$name_suffix;
        }
    }

	public function toggleBootstrap($enabled = null) {
		if (!isset($enabled)) {
			$this->bootstrap = !!!$this->bootstrap;
		} else {
			$this->bootstrap = $enabled;
		}
	}

    /**
     * Open up a new HTML form.
     *
     * @param  array   $options
     * @return string
     */
    public function open(array $options = array())
    {
		$this->toggleBootstrap(Arr::pull($options, 'bootstrap', false));

        if ($this->bootstrap) {
            if (!isset($options['role'])) {
                $options['role'] = "form";
            }
        }

        return parent::open($options);
    }

    /**
     * Create a new model based form builder.
     *
     * @param  mixed  $model
     * @param  array  $options
     * @return string
     */
    public function model($model, array $options = array())
    {

        $modelName = preg_split('#\\\\#', get_class($model));
        $this->modelName = strtolower(array_pop($modelName));

        if (!isset($options['method'])) {
            $options['method'] = $model->exists ? 'put' : 'post';
        }

        $element = parent::model($model, $options);

        return $element;
    }    

    /**
     * Close the current form.
     *
     * @return string
     */
    public function close()
    {
		$this->toggleBootstrap(false);
        $this->extra = [];
        return parent::close();
    }

    protected function formatBootstrap($element) {
        $prefix = $suffix = "";

        if ($this->bootstrap) {
            $prefix = "<div class='form-group'>";
            $suffix = "</div>";
        }

        return $prefix.$element.$suffix;
    }

    /**
     * Adds additional formatting to and around html elements.
     *
     * @param $type
     * @param $name
     * @param $value
     * @param $options
     */
    protected function preProcess($type, &$name, &$value, &$options) {

        $multiple = (in_array($type, []) || isset($options['multiple']) || array_search(strtolower('multiple'), array_map('strtolower', $options)) !== false );

        $inputName = $this->getInputName($name, $multiple);
        $prettyName = $this->getPrettyName($name);

        $options['placeholder'] = Arr::get($options, 'placeholder', $prettyName);

        if ( !in_array($type,["button","submit"])
             &&  ( !isset($options['label']) || $options['label'] )
        ) {
            $optionsLabel = Arr::pull($options, 'label', []);
            $labelName = Arr::get($options, 'labelName', $prettyName);
            $this->extra['label'] = $this->label($name, $labelName, $optionsLabel);
        } elseif (in_array($type,["checkbox"])) {
			$this->labels[] = $inputName;
		}

        if ($this->bootstrap) {
			$class = "";
            if (in_array($type,["button","submit"])) { $class = " btn "; }
            elseif (!in_array($type,["checkbox", "radio", "file"])) { $class = " form-control "; }

            $options['class'] = Arr::get($options, 'class', '') . $class;
        }

        $name = $inputName;

    }
    protected function postProcess($type, &$name, &$element) {
        if ($type == "hidden") {
			$this->extra = [];
			return;
		}

        $element = Arr::pull($this->extra, 'label', '').$element;
        $this->formatBootstrap($element);

        $this->extra = [];
    }

    protected function getModelValueAttribute($name)
    {
        $transformedKey = preg_replace('/^'.$this->modelName.'\./','', $this->transformKey($name));

        if (is_object($this->model))
        {
            return $this->object_get($this->model, $transformedKey);
        }
        elseif (is_array($this->model))
        {
            return array_get($this->model, $transformedKey);
        }
    }

    /**
     * Modified version of object_get helper that also checks if the segment is a method and if it is, invoke that.
     * @param      $object
     * @param      $key
     * @param null $default
     *
     * @return mixed
     */
    function object_get($object, $key, $default = null, $return_object = false)
    {
        if (is_null($key) || trim($key) == '') return $object;

        $key_split = explode('.', $key);
        foreach ($key_split as $curr_key => $segment)
        {
            //if the last key ends in "_id" then remove the id to get it's object
            end($key_split);
            if ($return_object && ($curr_key == key($key_split)) && (substr($segment, -3)==='_id') && method_exists($object, substr($segment,0,-3)) ) {
                $segment = substr($segment,0,-3);
            }

            if (!is_object($object) ||  ( !isset($object->{$segment}) && ! method_exists($object, $segment) )) {
                return value($default);
            } elseif (isset($object->{$segment})) {
                $object = $object->{$segment};
            } elseif (method_exists($object, $segment)) {
                if ($return_object) {
                    $object = $object->$segment();
                } else {
                    return value($default);
                }
            }

        }

        return $object;
    }
    function object_get_object($object, $key, $default = null) {
        return $this->object_get($object, $key, $default, true);
    }

	/**
	 * Create a form label element.
	 *
	 * @param  string  $name
	 * @param  string  $value
	 * @param  array   $options
	 * @return string
	 */
	public function label($name, $value = null, $options = array())
	{
		$name = $this->getInputName($name);
		return parent::label($name, $value, $options);
	}

    /**
     * Create a form input field.
     *
     * @param  string  $type
     * @param  string  $name
     * @param  string  $value
     * @param  array   $options
     * @return string
     */
    public function input($type, $name, $value = null, $options = array())
    {
		//if input begins with _ then it's a special laravel field
        if (strpos($name, "_") === 0) return parent::input($type, $name, $value, $options);

        $this->preProcess($type, $name, $value, $options);
        $element = parent::input($type, $name, $value, $options);
        $this->postProcess($type, $name, $element);

        return $element;
    }

    /**
     * Create a textarea input field.
     *
     * @param  string  $name
     * @param  string  $value
     * @param  array   $options
     * @return string
     */
    public function textarea($name, $value = null, $options = array())
    {
        $type = "textarea";
        $this->preProcess($type, $name, $value, $options);
        $element = parent::textarea($name, $value, $options);
        $this->postProcess($type, $name, $element);

        return $element;
    }

    /**
     * Create a select box field.
     *
     * @param  string  $name
     * @param  array   $list
     * @param  string  $selected
     * @param  array   $options
     * @return string
     */
    public function select($name, $list = array(), $selected = null, $options = array())
    {
        //key from remote table to use data from
        $list_text = "name";
        if (is_string($list)) {
            $list_text = $list;
            $list = [];
        }

        if (empty($list) || is_string($list) || empty($selected) ) {
            //if list is not an array of items to be selected
            $transformedKey = preg_replace('/^' . $this->modelName . '\./', '', $this->transformKey($name));
            $object = $this->object_get_object($this->model, $transformedKey);

            if (is_object($object)) {
                if (empty($list) || is_string($list)) {
                    if ($object instanceof Model) {
                        $model = $object;
                    } else {
                        $model = $object->getQuery()->getModel();
                    }
                    $list = $model->lists($list_text, 'id');
                    if ( !isset($options['multiple']) && array_search(strtolower('multiple'), array_map('strtolower', $options)) === false ) {
                        $list = ['' => 'Select ' . $this->getPrettyName($name)] + $list;
                    }
                }
                if (empty($selected)) {
                    //$selected = $object->get()->lists('id');
                }
            } else {
                $selected = $object;
            }

        }
        $value = "";
        $type = "select";
        $this->preProcess($type, $name, $value, $options);
        $element = parent::select($name, $list, $selected, $options);
        $this->postProcess($type, $name, $element);

        return $element;
    }    

    /**
     * Create a select box field.
     *
     * @param  string  $name
     * @param  array   $selected eg) [id=>text]
     * @param  array   $options
     * @return string
     */
    public function select2($name, $selected = [], $options = array())
    {
        $value = null;
        if (count($selected) == 1 && !isset($options['multipleS2'])) {
            Arr::set($options, "data-value_text", current($selected));
            Arr::set($options, "value", $value);
            $value = key($selected);
        } else {
            Arr::set($options, "data-select2_data", json_encode($selected));
			$value = implode(",",array_keys($selected));
        }

        $type = "text";
        $this->preProcess($type, $name, $value, $options);
        $element = parent::input($type, $name, $value, $options);
        $this->postProcess($type, $name, $element);

        return $element;
    }

    /**
     * Create a select range field.
     *
     * @param  string  $name
     * @param  string  $begin
     * @param  string  $end
     * @param  string  $selected
     * @param  array   $options
     * @return string
     */
    public function selectRange($name, $begin, $end, $selected = null, $options = array())
    {
        $range = array_combine($range = range($begin, $end), $range);

        if (isset($options['placeholder'])) {
            $range = [null=>$options['placeholder']] + $range;
        }

        return $this->select($name, $range, $selected, $options);
    }

    /**
     * Create a select year field.
     *
     * @param  string  $name
     * @param  string  $begin
     * @param  string  $end
     * @param  string  $selected
     * @param  array   $options
     * @return string
     */
    public function selectYear()
    {
        $element = parent::selectYear();

        return $element;
    }    

    /**
     * Create a select month field.
     *
     * @param  string  $name
     * @param  string  $selected
     * @param  array   $options
     * @param  string  $format
     * @return string
     */
    public function selectMonth($name, $selected = null, $options = array(), $format = '%B')
    {
        $months = array();

        if (isset($options['placeholder'])) {
            $months[null] = $options['placeholder'];
        }

        foreach (range(1, 12) as $month)
        {
            $months[$month] = strftime($format, mktime(0, 0, 0, $month, 1));
        }

        return $this->select($name, $months, $selected, $options);

    }

    /**
     * Create a checkbox input field.
     *
     * @param  string  $name
     * @param  mixed   $value
     * @param  bool    $checked
     * @param  array   $options
     * @return string
     */
    public function checkbox($name, $value = 1, $checked = null, $options = array())
    {
        $element = parent::checkbox($name, $value, $checked, $options);

        return $element;
    }    

    /**
     * Create a radio button input field.
     *
     * @param  string  $name
     * @param  mixed   $value
     * @param  bool    $checked
     * @param  array   $options
     * @return string
     */
    public function radio($name, $value = null, $checked = null, $options = array())
    {
        $element = parent::radio($name, $value, $checked, $options);

        return $element;
    }

    /**
     * Create a radio button input field.
     *
     * @param  string  $name
     * @param  mixed   $value
     * @param  bool    $checked
     * @param  array   $options
     * @return string
     */
    public function radioGroup($name, $value = [], $checked = [], $options = array())
    {
        $button_type = "btn-".Arr::pull($options, 'button-type', "primary");
        $key_as_val = Arr::pull($options, 'key-as-val', false);

        $html = "<div class='btn-group' data-toggle='buttons'>";

        //need to make sure labels aren't included on the individual radio button generation
        $options['label'] = false;
        $inputs = [];
        foreach ($value as $key => $val) {

            $radio_val = ($key_as_val) ? $key : $val;

            $bootstrap_val = $this->bootstrap;
            $this->bootstrap = false;
            $inputs[$key] = $this->radio($name, $radio_val, Arr::get($checked, $key, null), $options);
            $this->bootstrap = $bootstrap_val;


            $checked_state = $this->getCheckedState('radio', $name, $radio_val, Arr::get($checked, $key, null));

            $label_name = $val;
            $prettyName = $this->getPrettyName($label_name);

            $inputs[$key] = "<label class='btn {$button_type} ".($checked_state?"active":"")."'>".$inputs[$key].$prettyName."</label>";

        }

        $html .= implode("", $inputs)."</div>";

        return $html;
    }

    /**
     * Create a HTML reset input element.
     *
     * @param  string  $value
     * @param  array   $attributes
     * @return string
     */
    public function reset($value, $attributes = array())
    {
        $element = parent::reset($value, $attributes);

        return $element;
    }    

    /**
     * Create a HTML image input element.
     *
     * @param  string  $url
     * @param  string  $name
     * @param  array   $attributes
     * @return string
     */
    public function image($url, $name = null, $attributes = array())
    {
        $element = parent::image($url, $name, $attributes);

        return $element;
    }    

    /**
     * Create a submit button element.
     *
     * @param  string  $value
     * @param  array   $options
     * @return string
     */
    public function submit($value = null, $options = array())
    {
        $element = parent::submit($value, $options);

        return $element;
    }    

    /**
     * Create a button element.
     *
     * @param  string  $value
     * @param  array   $options
     * @return string
     */
    public function button($value = null, $options = array())
    {
        $element = parent::button($value, $options);

        return $element;
    }    

}
