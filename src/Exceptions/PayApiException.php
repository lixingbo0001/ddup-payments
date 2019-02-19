<?php

namespace Ddup\Payments\Exceptions;


use Ddup\Part\Exception\ExceptionCustomCodeAble;
use Ddup\Part\Libs\Helper;
use Ddup\Payments\Helper\Application;

class PayApiException extends ExceptionCustomCodeAble
{

    /**
     * @var Application
     */
    private static $app;

    const data_convert_fail    = 'data_convert_fail';
    const pay_api_invalid_sign = 'pay_api_invalid_sign';
    const api_error            = 'api_error';

    public static function setApp(Application $app)
    {
        self::$app = $app;
    }

    public function __construct(string $message = "", string $code = "", $row = [])
    {
        if (self::$app) self::$app->logger->error($message, Helper::toArray($row));

        parent::__construct($message, $code, $row);
    }
}
