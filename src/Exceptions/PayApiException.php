<?php

namespace Ddup\Payments\Helper\Exceptions;

class PayApiException extends ExceptionCustomCodeAble
{
    const api_message              = 'api_message';
    const pay_method_undefind      = 'pay_method_undefind';
    const pay_gateway_not_instance = 'pay_gateway_not_instance';
    const data_convert_fail        = 'data_convert_fail';
    const pay_api_invalid_sign     = 'pay_api_invalid_sign';
    const pay_gateway_undefind     = 'pay_gateway_undefind';


    public function __construct(string $message = "", string $code = "", array $row = [])
    {
        parent::__construct($message, $code, $row);
    }
}
