<?php

namespace Ddup\Payments\Upay\Kernel;


use Ddup\Part\Libs\Arr;
use Ddup\Payments\Helper\Exceptions\PayPaymentException;

class Support
{

    public static function getBaseUri(UpayConfig $config)
    {
        switch ($config->mode) {
            case $config::MODE_TEST:
                return 'https://api.mch.weixin.qq.com/sandboxnew';
            default:
                return 'https://upay.zjpay.com/upay/gateway';
        }
    }

    public static function getRequestId($devId)
    {
        return $devId . date('His');
    }

    public static function getTermRequestId($devId)
    {
        return $devId . date('His') . str_pad((microtime(true) % 1000), 4, "0", STR_PAD_LEFT);
    }

    public static function filterPayload($payload, UpayConfig $config)
    {
        if (isset($payload['notify_url'])) unset($payload['notify_url']);

        $payload['sign'] = self::generateSign($payload, $config->key);

        return $payload;
    }

    public static function generateSign($payload, $key)
    {
        if (is_null($key)) {
            throw new PayPaymentException('Missing Upay Config -- [key]', PayPaymentException::miss_key);
        }
        $string = md5(self::getSignContent($payload) . '&key=' . $key);

        $string = strtoupper($string);

        return $string;
    }

    public static function getSignContent($payload)
    {
        $para_filter = self::paraFilter($payload);
        $para_sort   = self::argSort($para_filter);

        return self::createLinkstring($para_sort);
    }

    private static function paraFilter($para)
    {
        $para_filter = [];

        $para = Arr::filter($para, ["", null], true);

        foreach ($para as $key => $val) {
            if (in_array($key, ['sign', 'sign_type', 'sign_format'])) {
                continue;
            }

            $para_filter[$key] = $para[$key];
        }

        return $para_filter;
    }

    private static function argSort($para)
    {
        ksort($para);
        reset($para);

        return $para;
    }

    private static function createLinkstring($para)
    {
        $arg = [];
        foreach ($para as $key => $val) {
            $arg[] = $key . "=" . $val;
        }

        $str = join('&', $arg);

        if (function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc()) {
            $str = stripslashes($str);
        }

        return $str;
    }

}