<?php

namespace Ddup\Payments\Upay\Kernel;


use Ddup\Part\Request\HasHttpRequest;
use Ddup\Payments\Exceptions\PayApiException;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use Illuminate\Support\Collection;
use Ddup\Payments\Helper\Application;

class UpayClient
{
    use HasHttpRequest;

    private $config;
    private $app;

    public function __construct(Application $app, UpayConfig $config)
    {
        $this->app    = $app;
        $this->config = $config;

        $this->timeout = 20;

        $this->loggerMiddleware();
    }

    private function loggerMiddleware()
    {
        $fomater = new MessageFormatter('{url} {method} {req_body}');

        $this->pushMiddleware(Middleware::log($this->app->logger, $fomater), 'log');
    }

    public function requestOptions()
    {
        return [];
    }

    public function requestParams()
    {
        return [];
    }

    function getBaseUri()
    {
        return Support::getBaseUri($this->config);
    }

    public function requestApi($endpoint, $data):Collection
    {
        $ret    = $this->post($endpoint, $data);
        $result = json_decode($ret, true);

        if (json_last_error() != JSON_ERROR_NONE) {
            throw new PayApiException('Upay API Error:' . $ret, PayApiException::api_error, $result);
        }

        if (!isset($result['code'])) {
            throw new PayApiException('Upay API Error:网络异常', PayApiException::api_error, $result);
        }

        if ($result['code'] != 100) {
            throw new PayApiException('Upay API Error:' . $result['message'], PayApiException::api_message, $result);
        }

        if ($result['sub_code'] != 100) {
            throw new PayApiException('Upay API Submessage:' . $result['sub_message'], PayApiException::api_message, $result);
        }

        return new Collection($result);
    }


}