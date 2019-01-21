<?php namespace Ddup\Payments\Helper;

use Illuminate\Support\Str;
use Ddup\Payments\Contracts\PayableInterface;
use Ddup\Payments\Exceptions\PayApiException;


Trait MakePaymentTrait
{

    protected function makePay($preFix, $name, Application $app, $config):PayableInterface
    {

        $class = $preFix . '\\' . Str::studly($name) . 'Payment';

        if (!class_exists($class)) {
            throw new PayApiException("Pay Gateway [{$class}] Not Exists", PayApiException::pay_method_undefind);
        }

        $app = new $class($app, $config);

        if ($app instanceof PayableInterface) {
            return $app;
        }

        throw new PayApiException("Pay Gateway [{$class}] Must Be An Instance Of PayableInterface", PayApiException::pay_gateway_not_instance);
    }

}