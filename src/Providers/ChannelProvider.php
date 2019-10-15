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
use Ddup\Payments\Helper\PayProxy;
use Ddup\Payments\Upay;
use Ddup\Payments\Upay2;
use Ddup\Payments\Wechat;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ChannelProvider implements ServiceProviderInterface
{

    public function register(Container $pimple)
    {
        if (!($pimple instanceof Application)) return;

        $this->proxy($pimple);

        $this->channels($pimple);
    }

    private function proxy(Application $pimple)
    {
        $pimple->proxy = function () use ($pimple) {
            $proxy = new PayProxy($pimple);
            return $proxy;
        };
    }

    private function channels(Application $pimple)
    {
        $pimple->wechat = function () use ($pimple) {
            return new Wechat($pimple);
        };

        $pimple->upay = function () use ($pimple) {
            return new Upay($pimple);
        };

        $pimple->upay2 = function () use ($pimple) {
            return new Upay2($pimple);
        };

        $pimple->fuyou = function () use ($pimple) {
            return new Fuyou($pimple);
        };
    }
}