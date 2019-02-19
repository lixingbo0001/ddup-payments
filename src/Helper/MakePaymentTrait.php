<?php namespace Ddup\Payments\Helper;

use Ddup\Payments\Exceptions\PayPaymentException;
use Illuminate\Support\Str;
use Ddup\Payments\Contracts\PayableInterface;


Trait MakePaymentTrait
{

    protected function makePay($preFix, $name, Application $app, $config):PayableInterface
    {

        $class = $preFix . '\\' . Str::studly($name) . 'Payment';

        if (!class_exists($class)) {
            throw new PayPaymentException("支付方式 [{$class}] 不存在", PayPaymentException::pay_method_undefind);
        }

        $app = new $class($app, $config);

        if ($app instanceof PayableInterface) {
            return $app;
        }

        throw new PayPaymentException("支付方式 [{$class}] 必须实现 PayableInterface", PayPaymentException::pay_gateway_not_instance);
    }

}