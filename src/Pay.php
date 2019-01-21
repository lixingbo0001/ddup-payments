<?php namespace Ddup\Payments;


use Ddup\Payments\Contracts\PaymentApplicationInterface;
use Ddup\Payments\Helper\Exceptions\PayApiException;
use Ddup\Payments\Helper\PayProxy;


class Pay
{

    protected $config;

    public function __construct($config)
    {
        $this->config = $config;
    }


    /**
     * @param $method
     * @return PaymentApplicationInterface
     * @throws PayApiException
     */
    protected function create($method):PaymentApplicationInterface
    {
        $gateway = __NAMESPACE__ . '\\' . ucfirst($method);

        if (class_exists($gateway)) {
            return self::make($gateway);
        }

        throw new PayApiException("Gateway [{$method}] Not Exists", PayApiException::pay_gateway_undefind);
    }

    /**
     * @param $gateway
     * @return mixed
     * @throws PayApiException
     */
    protected function make($gateway)
    {
        $app = new $gateway($this->config);

        if ($app instanceof PaymentApplicationInterface) {
            return $app;
        }

        throw new PayApiException("payment [$gateway] Must Be An Instance Of PaymentApplicationInterface", PayApiException::pay_gateway_not_instance);
    }

    public static function payment($method, $config):PaymentApplicationInterface
    {
        $app = new self($config);

        return new PayProxy($app->create($method));
    }
}
