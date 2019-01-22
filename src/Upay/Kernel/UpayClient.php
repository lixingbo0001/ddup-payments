<?php

namespace Ddup\Payments\Upay\Kernel;


use Ddup\Part\Api\ApiResultInterface;
use Ddup\Part\Api\ApiResulTrait;
use Ddup\Part\Libs\Helper;
use Ddup\Part\Request\HasHttpRequest;
use Ddup\Payments\Exceptions\PayApiException;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use Illuminate\Support\Collection;
use Ddup\Payments\Helper\Application;

class UpayClient
{
    use HasHttpRequest, ApiResulTrait;

    private $config;
    private $app;

    public function __construct(Application $app, UpayConfig $config)
    {
        $this->app    = $app;
        $this->config = $config;

        $this->timeout = 20;

        $this->loggerMiddleware();
    }

    public function newResult($ret):ApiResultInterface
    {
        return new UpayApiResult($ret);
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
        $ret = $this->post($endpoint, $data);

        $this->parseResult($ret);

        if (!$this->result->isSuccess()) {
            throw new PayApiException('银联通道报错：' . $this->result->getMsg(), PayApiException::api_error, Helper::toArray($ret));
        }

        return $this->result->getData();
    }


}