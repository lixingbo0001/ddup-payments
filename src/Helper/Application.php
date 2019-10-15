<?php namespace Ddup\Payments\Helper;


use Ddup\Payments\Contracts\PaymentInterface;
use Ddup\Payments\Contracts\PaymentProxyAble;
use Ddup\Payments\Exceptions\PayApiException;
use Ddup\Payments\Exceptions\PayPaymentException;
use Ddup\Payments\Providers\ChannelProvider;
use Ddup\Payments\Providers\LogProvider;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use Pimple\Container;
use Psr\Log\LoggerInterface;

/**
 * Class Pay
 * @property array config;
 * @property LoggerInterface $logger;
 * @property PaymentInterface wechat;
 * @property PaymentInterface upay;
 * @property PaymentInterface upay2;
 * @property PaymentInterface fuyou;
 * @property PaymentProxyAble proxy;
 */
class Application extends Container
{

    public function __construct(array $values = array())
    {
        parent::__construct($values);

        $this->registerProvidrs($this->providers());
    }

    public function registerRequestMiddleware($client)
    {
        $fomater = new MessageFormatter('{url} {method} {req_body}');

        $client->pushMiddleware(Middleware::log($this->logger, $fomater), 'log');
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

    private function getPayment($gateway)
    {
        $payment = $this->$gateway;

        if (!($payment instanceof PaymentInterface)) {
            throw new PayPaymentException("[{$gateway}]需要实现 PaymentInterface", PayPaymentException::pay_gateway_not_instance);
        }

        return $payment;
    }

    private function packagePayment(PaymentInterface $payment)
    {
        if ($this->proxy) {

            $this->proxy->setPayment($payment);

            return $this->proxy;
        }

        return $payment;
    }

    private function init($config)
    {
        $this->config = $config;

        PayApiException::setApp($this);
    }

    function create($gateway, $config):PaymentInterface
    {
        if (!$this->offsetExists($gateway)) {
            throw new PayPaymentException("暂不支持的支付通道[{$gateway}]", PayPaymentException::pay_gateway_not_instance);
        }

        $this->init($config);

        $payment = $this->getPayment($gateway);
        $payment = $this->packagePayment($payment);

        return $payment;
    }
}
