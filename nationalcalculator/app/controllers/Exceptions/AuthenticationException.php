<?php

namespace Controller\Exceptions;

class AuthenticationException extends CustomException {

    public function __construct(Array $data, $message = "User not logged in", \Exception $previous = null, array $headers = array(), $code = 0) {

        parent::__construct($data, 401, $message, $previous, $headers, $code);

    }

}