<?php


namespace dasbit\apiclient;


use Throwable;

class ClientException extends \Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct(self::class . ':' . $message, $code, $previous);
    }
}
