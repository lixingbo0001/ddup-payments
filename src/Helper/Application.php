<?php namespace Ddup\Payments\Helper;


use Ddup\Payments\Contracts\PaymentInterface;
use Ddup\Payments\Exceptions\PayApiException;
use Ddup\Payments\Providers\ChannelProvider;
use Ddup\Payments\Providers\LogProvider;
use Pimple\Container;
use Psr\Log\LoggerInterface;

/**
 * Class Pay
 * @property array config;
 * @property LoggerInterface $logger;
 * @property PaymentInterface wechat;
 * @property PaymentInterface upay;
 * @property PaymentInterface fuyou;
 * @package Ddup\Payments\Helper
 */
class Application extends Container
{

    public function __construct(array $values = array())
    {
        parent::__construct($values);

        $this->registerProvidrs($this->providers());
    }

    public function __set($id, $value)
    {
        $this->offsetSet($id, $value);
    }

    public function __get($name)
    {
        return $this->offsetGet($name);
    }

    private function registerProvidrs(array $providers)
    {
        foreach ($providers as $provider) {
            parent::register(new $provider);
        }
    }

    private function providers()
    {
        return [
            LogProvider::class,
            ChannelProvider::class
        ];
    }

    protected function create($gateway):PaymentInterface
    {
        if (!$this->offsetExists($gateway)) {
            throw new PayApiException("暂不支持的支付通道[{$gateway}]", PayApiException::pay_gateway_not_instance);
        }

        $payment = $this->$gateway;

        if (!($payment instanceof PaymentInterface)) {
            throw new PayApiException("[{$gateway}]需要实现 PaymentInterface", PayApiException::pay_gateway_not_instance);
        }

        return $payment;
    }

    public static function payment($method, $config):PaymentInterface
    {
        $app = new self;

        $app->config = $config;

        $api = $app->create($method);

        return new PayProxy($api);
    }
}
