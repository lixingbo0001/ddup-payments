<?php

namespace Ddup\Payments\Exceptions;


use Ddup\Part\Exception\ExceptionCustomCodeAble;

class PayPaymentException extends ExceptionCustomCodeAble
{

    const invalid_mode = 'invalid_mode';
    const miss_key     = 'miss_key';
}
