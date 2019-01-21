<?php

namespace Ddup\Payments\Fuyou\Kernel;


class Support
{
    public function paraFilter($param)
    {
        $para_filter = array();
        foreach ($param as $key => $val) {
            if ($key == "sign" || strstr($key, 'reserved')) {
                continue;
            } else {
                $para_filter[$key] = $param[$key];
            }
        }
        return $para_filter;
    }

    public function argSort($param)
    {
        ksort($param);
        reset($param);
        return $param;
    }

    public function createLinkstring($param)
    {
        $arg = "";
        foreach ($param as $key => $val) {
            $arg .= $key . "=" . $val . "&";
        }
        //去掉最后一个&字符
        $arg = trim($arg, '&');
        //如果存在转义字符，那么去掉转义
        if (function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc()) {
            $arg = stripslashes($arg);
        }
        return $arg;
    }

    public function sign($param, $pem_path)
    {
        $param  = $this->paraFilter($param);
        $param  = $this->argSort($param);
        $data   = $this->createLinkstring($param);
        $pem    = file_get_contents($pem_path);   //读取密钥文件
        $pkeyid = openssl_pkey_get_private($pem);   //获取私钥

        openssl_sign($data, $sign, $pkeyid, OPENSSL_ALGO_MD5);   //MD5WithRSA私钥加密

        return base64_encode($sign);   //返回base64加密之后的数据
    }
}
