<?php
/**
 * Created by PhpStorm.
 * User: lixingbo
 * Date: 2019/1/21
 * Time: 下午5:15
 */

namespace Ddup\Payments\Providers;


use Ddup\Payments\Helper\Application;
use Ddup\Payments\Upay;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class UpayProvider implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        if ($pimple instanceof Application) {
            $pimple->upay = function () use ($pimple) {
                $api = new Upay($pimple);
                return $api;
            };
        }
    }

}