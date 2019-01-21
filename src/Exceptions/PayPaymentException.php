<?php

namespace Ddup\Payments\Helper\Exceptions;




class PayPaymentException extends ExceptionCustomCodeAble
{

    const invalid_mode = 'invalid_mode';
    const miss_key  = 'miss_key';
}
