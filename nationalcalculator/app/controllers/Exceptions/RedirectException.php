<?php

namespace Controller\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

class RedirectException extends HttpException {

    public $url;

    public function __construct($url, $statusCode, $message = null, \Exception $previous = null, array $headers = array(), $code = 0) {

        $this->url = $url;

        parent::__construct($statusCode, $message, $previous, $headers, $code);

    }

}