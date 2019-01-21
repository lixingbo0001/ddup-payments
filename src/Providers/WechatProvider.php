<?php
/**
 * Created by PhpStorm.
 * User: lixingbo
 * Date: 2019/1/21
 * Time: 下午5:15
 */

namespace Ddup\Payments\Providers;


use Ddup\Payments\Helper\Application;
use Ddup\Payments\Wechat;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class WechatProvider implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {

        if ($pimple instanceof Application) {
            $pimple->wechat = function () use ($pimple) {
                $api = new Wechat($pimple);
                return $api;
            };
        }
    }

}