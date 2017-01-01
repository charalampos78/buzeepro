<?php

Validator::resolver(function($translator, $data, $rules, $messages, $customAttributes) {
    return new Validators\CustomValidator($translator, $data, $rules, $messages, $customAttributes);
});