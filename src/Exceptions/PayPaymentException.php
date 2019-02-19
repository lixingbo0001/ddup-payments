<?php

namespace Ddup\Payments\Exceptions;


use Ddup\Part\Exception\ExceptionCustomCodeAble;

class PayPaymentException extends ExceptionCustomCodeAble
{

    const invalid_mode       = 'invalid_mode';
    const invalid_middleware = 'invalid_middleware';
    const miss_key           = 'miss_key';
    const pay_gateway_not_instance = 'pay_gateway_not_instance';
    const pay_method_undefind = 'pay_method_undefind';

}
