<?php

namespace Ddup\Payments\Wechat\Kernel;


use Ddup\Part\Request\HasHttpRequest;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use Illuminate\Support\Collection;
use Ddup\Payments\Helper\Application;

class WechatClient
{
    use HasHttpRequest;


    private $app;
    private $config;

    public function __construct(Application $app, WechatConfig $config)
    {
        $this->app    = $app;
        $this->config = $config;

        $this->loggerMiddleware();
    }

    private function loggerMiddleware()
    {
        $fomater = new MessageFormatter('{url} {method} {req_body}');

        $this->pushMiddleware(Middleware::log($this->app->logger, $fomater), 'log');
    }

    function getBaseUri()
    {
        return Support::getBaseUri($this->config);
    }

    public function requestParams()
    {
        return [];
    }

    function requestOptions()
    {
        return [
            'verify' => false
        ];
    }

    public function safeRequestApi($endpoint, array $params)
    {
        $this->config->ssl_verify = true;

        return self::requestApi($endpoint, $params);
    }

    public function requestApi($endpoint, array $params)
    {
        $options = [];

        $xml = Support::toXml($params);

        Support::cert($options, $this->config);

        $ret = $this->post($endpoint, $xml, $options);

        $result_arr = Support::fromXml($ret);

        $result = new Collection($result_arr);

        Support::checkSuccess($result);

        Support::checkSign($endpoint, $result, $this->config->key);

        return $result;
    }

}
