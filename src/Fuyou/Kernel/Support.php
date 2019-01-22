<?php

namespace Ddup\Payments\Fuyou\Kernel;


use Ddup\Part\Message\MsgToXml;
use Ddup\Payments\Exceptions\PayPaymentException;

class Support
{

    public static function getBaseUri(FuyouConfig $config)
    {
        switch ($config->mode) {
            case $config::MODE_PROD:
                return 'https://spay.fuiou.com';
                break;
            default:
                return 'http://116.239.4.195:28164';
                break;
        }
    }

    private static function argSort($param)
    {
        ksort($param);
        reset($param);
        return $param;
    }

    private static function createLinkstring($param)
    {
        $tmp = [];

        foreach ($param as $key => $val) {
            $tmp[] = "{$key}={$val}";
        }

        return join('&', $tmp);
    }

    private static function encode($param, $pem_path)
    {
        if (!file_exists($pem_path)) {
            throw new PayPaymentException('富友秘钥不存在');
        }

        $pem    = file_get_contents($pem_path);
        $pkeyid = openssl_pkey_get_private($pem);

        openssl_sign($param, $sign, $pkeyid, OPENSSL_ALGO_MD5);

        return base64_encode($sign);
    }

    public static function signString($param)
    {
        array_forget($param, ['sign', 'reserved']);

        $param = self::argSort($param);
        return self::createLinkstring($param);
    }

    public static function sign($param, $pem_path)
    {
        return self::encode(self::signString($param), $pem_path);
    }

    public static function toXml($data)
    {
        $xml_content = new MsgToXml($data);

        return "<?xml version=\"1.0\" encoding=\"GBK\" standalone=\"yes\"?><xml>" . $xml_content . "</xml>";
    }
}
