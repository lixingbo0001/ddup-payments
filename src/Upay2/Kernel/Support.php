<?php

namespace Ddup\Payments\Upay2\Kernel;


use Ddup\Payments\Exceptions\PayPaymentException;

class Support
{

    public static function getBaseUri(UpayConfig $config)
    {
        switch ($config->mode) {
            case $config::MODE_TEST:
                return 'https://qr-test2.chinaums.com/netpay-route-server/api/';
            default:
                return 'https://qr.chinaums.com/netpay-route-server/api/';
        }
    }

    public static function msgSrc(UpayConfig $config)
    {
        switch ($config->mode) {
            case $config::MODE_TEST:
                return 'WWW.TEST.COM';
            default:
                return '';
        }
    }

    public static function generateSign($payload, $key)
    {
        if (is_null($key)) {
            throw new PayPaymentException('Missing Upay2 Config -- [key]', PayPaymentException::miss_key);
        }

        $string = md5(self::getSignContent($payload) . $key);
        $string = strtoupper($string);

        return $string;
    }

    public static function getSignContent($payload)
    {
        $para_filter = self::paraFilter($payload);
        $para_sort   = self::argSort($para_filter);

        return self::createLinkstring($para_sort);
    }

    public static function paraFilter($para)
    {
        $para_filter = [];

        foreach ($para as $key => $val) {
            if ($key == 'sign') {
                continue;
            }

            if (in_array($val, ["", " ", "\t", "\n", "\r", null], true)) {
                continue;
            }

            $para_filter[$key] = $para[$key];
        }

        return $para_filter;
    }

    private static function argSort($para)
    {
        ksort($para);

        return $para;
    }

    private static function createLinkstring($data)
    {
        $param = [];

        foreach ($data as $k => $v) {
            if ($k == 'sign') continue;

            $v = is_array($v) ? json_encode($v, JSON_UNESCAPED_UNICODE) : $v;

            $param[] = $k . "=" . $v;
        }

        return join("&", $param);
    }

}