<?php

namespace Controller\Exceptions;

class InvalidObjectIdException extends CustomException {

    public function __construct(array $data = [], $message = "Object id provided not found or no access", \Exception $previous = null, array $headers = array(), $code = 0) {
        $statusCode = 400;

        parent::__construct($data, $statusCode, $message, $previous, $headers, $code);

    }

}