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
use Ddup\Part\Libs\Helper;
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
    }

    public function newResult($ret):ApiResultInterface
    {
        return new FuyouApiResult($ret);
    }

    public function getBaseUri()
    {
        // TODO: Implement getBaseUri() method.
    }

    public function requestParams()
    {
        return [];
    }

    public function requestOptions()
    {
        return [];
    }

    public function requestApi($endpoint, array $parmas)
    {
        $ret = $this->post($endpoint, $parmas);

        $this->parseResult($ret);

        if (!$this->result->isSuccess()) {
            throw new PayApiException('银联通道报错：' . $this->result->getMsg(), PayApiException::api_error, Helper::toArray($ret));
        }

        return $this->result->getData();
    }

}