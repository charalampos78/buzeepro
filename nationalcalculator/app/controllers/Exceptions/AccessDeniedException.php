<?php

namespace Controller\Exceptions;

class AccessDeniedException extends CustomException {

    public function __construct(Array $data, $message = "Access Denied", \Exception $previous = null, array $headers = array(), $code = 0) {

        parent::__construct($data, 403, $message, $previous, $headers, $code);

    }

}