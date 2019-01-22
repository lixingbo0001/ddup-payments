<?php
/**
 * Created by PhpStorm.
 * User: lixingbo
 * Date: 2019/1/22
 * Time: 上午10:21
 */

namespace Ddup\Payments\Providers;


use Ddup\Payments\Fuyou;
use Ddup\Payments\Helper\Application;
use Ddup\Payments\Upay;
use Ddup\Payments\Wechat;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ChannelProvider implements ServiceProviderInterface
{

    public function register(Container $pimple)
    {
        if (!($pimple instanceof Application)) return;

        $pimple->wechat = function () use ($pimple) {
            $api = new Wechat($pimple);
            return $api;
        };

        $pimple->upay = function () use ($pimple) {
            $api = new Upay($pimple);
            return $api;
        };

        $pimple->fuyou = function () use ($pimple) {
            $api = new Fuyou($pimple);
            return $api;
        };
    }
}