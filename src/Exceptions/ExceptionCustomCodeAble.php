<?php

namespace Ddup\Payments\Helper\Exceptions;


class ExceptionCustomCodeAble extends \Exception
{
    public  $row;
    private $customCode;

    public function __construct(string $message = "", $code = "", $row = [])
    {
        $this->row        = $row;
        $this->customCode = $code === "" ? 0 : $code;
        parent::__construct($message, intval($code));
    }

    public function getCustomCode()
    {
        return $this->customCode;
    }

    public function getRow()
    {
        return $this->row;
    }
}
