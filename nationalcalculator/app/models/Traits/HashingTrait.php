<?php

namespace Models\Traits;

use Event;
use Hash;

trait HashingTrait {

    /**
     * Fields that can be hashed
     * Should be defined in model
     *
     * @var array
     */
    //public $hashable = [];

    /**
     * Stores original values of newly assigned items before hashing.
     *
     * @var array
     */
    protected $hashedOriginal = [];

    /**
     * Get hashable fields.
     *
     * @return array
     */
    public function getHashable()
    {
        return $this->hashable ?: [];
    }

    public static function bootHashingTrait() {

        Event::listen(['eloquent.validating: ' . static::class], function($model, $event = "") {

            list($event_name, $model_name) = explode(": ", Event::firing());
            //if (!is_a($model, $model_name)) return;
            if (!method_exists($model, "getHashable")) return;


            $hashable = $model->getHashable();

            foreach ($hashable as $hashAttrib) {
                if (isset($model->hashedOriginal[$hashAttrib])) {
                    //if it's been hashed already, set it back to its text value on all other validation events
                    $model->$hashAttrib = $model->hashedOriginal[$hashAttrib];
                }
            }
        });

        Event::listen(['eloquent.validated: ' . static::class], function($model, $event = "") {

            list($event_name, $model_name) = explode(": ", Event::firing());
            //if (!is_a($model, $model_name)) return;
            if (!method_exists($model, "getHashable")) return;

            $hashable = $model->getHashable();

            if (in_array($event, ["passed", "skipped"]) ) {

                foreach ($hashable as $hashAttrib) {
                    if ($model->isDirty($hashAttrib)) {
                        //TODO: potentially use Hash::needsRehash() to check is it's already been hashed?

                        $model->hashedOriginal[$hashAttrib] = $model->$hashAttrib;
                        $model->$hashAttrib = Hash::make($model->$hashAttrib);

                    }
                }
            }
        });
    }
}