<?php

namespace Ddup\Payments;

use Ddup\Payments\Config\PaymentNotifyStruct;
use Ddup\Payments\Config\PayOrderStruct;
use Ddup\Payments\Contracts\PaymentInterface;
use Ddup\Payments\Exceptions\PayApiException;
use Ddup\Payments\Fuyou\Kernel\FuyouConfig;
use Ddup\Payments\Helper\MakePaymentTrait;
use Ddup\Payments\Helper\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class Fuyou implements PaymentInterface
{

    use MakePaymentTrait;

    protected $config;

    private $app;

    public function __construct(Application $app)
    {
        $this->app    = $app;
        $this->config = new FuyouConfig($app->config);
    }

    public function payload()
    {
        return [
            'notify_url' => $this->config->notify_url,
            'version'    => $this->config->version,
            'timestamp'  => date('Y-m-d H:i:s')
        ];
    }

    public function cancel($name, $order)
    {
        // TODO: Implement cancel() method.
    }

    public function close($name, $order)
    {
        // TODO: Implement close() method.
    }

    public function find($name, Collection $order):Collection
    {
        return new Collection();
    }

    public function pay($name, PayOrderStruct $order):Collection
    {
        return $this->makePay(__CLASS__, $name, $this->app, $this->config)->pay($this->payload(), $order);
    }

    public function refund($name, Collection $order):Collection
    {
        return new Collection();
    }

    public function success()
    {
        return 1;
    }

    public function verify():Collection
    {
        $data = Request::createFromGlobals()->all();

        if (!$data) {
            throw  new PayApiException("Fuyou notify error:not data", PayApiException::api_error);
        }

        return new Collection($data);
    }

    public function callbackConversion($data):PaymentNotifyStruct
    {
        return new PaymentNotifyStruct($data);
    }
}