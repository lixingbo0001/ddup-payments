<?php

namespace Ddup\Payments\Fuyou\Kernel;


use Ddup\Part\Message\MsgToXml;

class Support
{
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
