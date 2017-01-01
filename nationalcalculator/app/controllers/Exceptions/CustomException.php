<?php

namespace Controller\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Support\Arr;
use Flash;

class CustomException extends HttpException {

    public $data = [];
    public $flash = [];
    public $success = false;
    public $exceptionName = null;

    const THROTTLE = "ThrottleException";
    const BADGAME = "InvalidGameDataException";
    const CSRF = "InvalidCSRFException";
    const JWT = "InvalidJWTException";

    public function __construct(array $data, $statusCode, $message = null, \Exception $previous = null, array $headers = array(), $code = 0) {

        $this->flash = Arr::pull($data, 'flash_msg', Flash::get_flash());
        $this->success = Arr::pull($data, 'success', false);
        $this->exceptionName = Arr::pull($data, 'exceptionName', null);

        $this->data = $data;

        parent::__construct($statusCode, $message, $previous, $headers, $code);

    }

}