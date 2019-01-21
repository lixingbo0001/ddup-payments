<?php
/**
 * Created by PhpStorm.
 * User: lixingbo
 * Date: 2018/9/30
 * Time: 下午6:14
 */

namespace Ddup\Payments\Config;

use Illuminate\Support\Collection;


abstract class Config
{
    protected $config;
    private $ssl_verify = false;


    public function __construct($config)
    {
        $this->config = new Collection($config);
    }

    abstract function checkMode();

    abstract function getBaseUri();

    public function getKey()
    {
        return $this->config->get('key');
    }

    public function getSslCert($def = null)
    {
        return $this->config->get('ssl_cert', $def);
    }

    public function getCertKey($def = null)
    {
        return $this->config->get('cert_key', $def);
    }

    public function getRootca($def = null)
    {
        return $this->config->get('rootca', $def);
    }

    public function getAppId()
    {
        return $this->config->get('app_id');
    }

    public function setSslVerify($verify = false)
    {
        $this->ssl_verify = $verify;
    }

    public function sslIsVerify()
    {
        return $this->ssl_verify;
    }

    public function getMchId()
    {
        return $this->config->get('mch_id');
    }

    public function getNotifyUrl()
    {
        return $this->config->get('notify_url');
    }

    public function getSubMchId()
    {
        return $this->config->get('sub_mch_id');
    }

    public function getSubAppId()
    {
        return $this->config->get('sub_app_id');
    }
}