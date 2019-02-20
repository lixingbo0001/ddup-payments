<?php

namespace Ddup\Payments\Wechat\Kernel;


use Ddup\Payments\Exceptions\PayApiException;
use Ddup\Payments\Exceptions\PayPaymentException;
use Illuminate\Support\Collection;

class Support
{

    static private $js_api_sign_string;

    public static function jsApiSign($js_api_param, $key)
    {
        ksort($js_api_param);

        $js_api_param['key'] = $key;

        $tmp = [];

        foreach ($js_api_param as $k => $v) {

            if (is_null($v)) continue;

            $tmp[] = "{$k}={$v}";
        }

        $sign_string = join('&', $tmp);

        self::$js_api_sign_string = $sign_string;

        return strtoupper(md5($sign_string));
    }

    public static function jsApiSignString()
    {
        return self::$js_api_sign_string;
    }

    public static function getBaseUri(WechatConfig $config)
    {
        switch ($config->mode) {
            case $config::MODE_DEV:
                return 'https://api.mch.weixin.qq.com/sandboxnew/';
                break;
            default:
                return 'https://api.mch.weixin.qq.com/';
                break;
        }
    }

    private static function needCert(WechatConfig $config)
    {
        return $config->ssl_verify;
    }

    static function cert(&$options, WechatConfig $config)
    {
        if (!self::needCert($config)) {
            return;
        }

        $options['cert']    = $config->ssl_cert;
        $options['ssl_key'] = $config->cert_key;
        $options['verify']  = $config->rootca;
    }

    static function checkSign($endpoint, Collection $result, $key)
    {
        if (strpos($endpoint, 'mmpaymkttransfers') !== false || self::generateSign($result->all(), $key) === $result->get('sign')) {
            return;
        }

        throw new \Exception('Wechat Sign Verify FAILED', 3);
    }

    static function checkSuccess(Collection $result)
    {
        if (self::isSuccess($result)) {
            return;
        }

        throw new PayApiException(
            '微信通道报错:' . $result->get('return_msg') . $result->get('err_code_des', ''),
            PayApiException::api_error,
            $result->all()
        );
    }

    private static function isSuccess(Collection $result)
    {
        return $result->get('return_code') == 'SUCCESS' && $result->get('result_code') == 'SUCCESS';
    }

    public static function generateSign($data, $key = null):string
    {
        if (is_null($key)) {
            throw new PayPaymentException('Missing Wechat Config -- [key]', PayPaymentException::miss_key);
        }

        ksort($data);

        $string = md5(self::getSignContent($data) . '&key=' . $key);

        return strtoupper($string);
    }


    public static function getSignContent($data):string
    {
        $buff = [];

        foreach ($data as $k => $v) {

            if (self::notSignValue($k, $v)) continue;

            $buff[] = $k . '=' . $v;
        }

        return join('&', $buff);
    }

    private static function notSignValue($k, $v)
    {
        if (in_array($k, ['sign', 'sign_type'])) return true;

        if ($v == '' || is_array($v)) return true;

        return false;
    }

    public static function toXml($data):string
    {
        if (!is_array($data) || count($data) <= 0) {
            throw new PayApiException('微信通道xml数据转换失败', PayApiException::data_convert_fail);
        }

        $xml = '<xml>';
        foreach ($data as $key => $val) {
            $xml .= is_numeric($val) ? '<' . $key . '>' . $val . '</' . $key . '>' :
                '<' . $key . '><![CDATA[' . $val . ']]></' . $key . '>';
        }
        $xml .= '</xml>';

        return $xml;
    }

    public static function fromXml($xml):array
    {
        if (is_array($xml)) return $xml;

        if (!$xml) {
            throw new PayApiException('微信错误的xml结构', PayApiException::data_convert_fail);
        }

        libxml_disable_entity_loader(true);

        return json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA), JSON_UNESCAPED_UNICODE), true);
    }
}
