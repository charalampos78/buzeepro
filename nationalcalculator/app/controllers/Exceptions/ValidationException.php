<?php

namespace Controller\Exceptions;

class ValidationException extends CustomException {

    public function __construct(array $data, $message = "Invalid input value in form data", \Exception $previous = null, array $headers = array(), $code = 0) {

        parent::__construct($data, 403, $message, $previous, $headers, $code);

    }

}