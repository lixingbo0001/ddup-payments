<?php
/**
 * Created by PhpStorm.
 * User: lixingbo
 * Date: 2019/1/21
 * Time: 下午9:22
 */

namespace Ddup\Payments\Fuyou\Kernel;


use Ddup\Part\Api\ApiResultInterface;
use Ddup\Part\Api\ApiResulTrait;
use Ddup\Part\Request\HasHttpRequest;
use Ddup\Payments\Exceptions\PayApiException;
use Ddup\Payments\Helper\Application;

class FuyouClient
{
    use HasHttpRequest, ApiResulTrait;

    private $app;
    private $config;

    public function __construct(Application $app, FuyouConfig $config)
    {
        $this->app    = $app;
        $this->config = $config;

        $this->app->registerRequestMiddleware($this);
    }

    public function newResult($ret):ApiResultInterface
    {
        return new FuyouApiResult($ret);
    }

    public function getBaseUri()
    {
        return Support::getBaseUri($this->config);
    }

    public function requestParams()
    {
        return [];
    }

    public function requestOptions()
    {
        return [
            'headers' => [
                'content-type' => 'application/x-www-form-urlencoded'
            ]
        ];
    }

    public function requestApi($endpoint, $parmas)
    {
        $xml = Support::toXml($parmas);

        $this->app->logger->debug('请求富友原始数据', $parmas);

        $ret = $this->post($endpoint, 'req=' . Support::bodyEncode($xml));

        $ret = Support::bodyDecode($ret);

        $this->parseResult($ret);

        if (!$this->result()->isSuccess()) {

            throw new PayApiException(
                '富友通道报错：' . $this->result()->getMsg() . ':' . $this->result()->getCode(),
                PayApiException::api_error,
                $this->result()->getData()
            );
        }

        return $this->result()->getData();
    }

}