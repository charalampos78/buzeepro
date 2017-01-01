<?php

namespace My\MForm\Facade;

use Illuminate\Support\Facades\Facade;

class MForm extends Facade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'mform'; }

}