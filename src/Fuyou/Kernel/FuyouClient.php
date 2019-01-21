<?php
/**
 * Created by PhpStorm.
 * User: lixingbo
 * Date: 2019/1/21
 * Time: 下午9:22
 */

namespace Ddup\Payments\Fuyou\Kernel;


use Ddup\Part\Request\HasHttpRequest;
use Ddup\Payments\Helper\Application;

class FuyouClient
{
    use HasHttpRequest;

    private $app;
    private $config;

    public function __construct(Application $app, FuyouConfig $config)
    {
        $this->app    = $app;
        $this->config = $config;
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

    }

}