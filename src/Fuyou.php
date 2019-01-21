<?php

namespace Ddup\Payments;

use Ddup\Payments\Config\PaymentNotifyStruct;
use Ddup\Payments\Contracts\PaymentInterface;
use Ddup\Payments\Exceptions\PayApiException;
use Ddup\Payments\Helper\MakePaymentTrait;
use Ddup\Payments\Helper\Application;
use Ddup\Payments\Upay\Kernel\UpayConfig;
use Illuminate\Http\Request;
use Ddup\Payments\Upay\Kernel\Support;
use Illuminate\Support\Collection;

class Fuyou implements PaymentInterface
{

    use MakePaymentTrait;

    protected $config;

    private $app;

    public function __construct(Application $app)
    {
        $this->app    = $app;
        $this->config = new UpayConfig($app->config);
    }

    public function payload()
    {
        return [
            'merchant_id'     => $this->config->merchant_id,
            'notify_url'      => $this->config->notify_url,
            'version'         => $this->config->version,
            'terminal_id'     => $this->config->terminal_id,
            'sign_type'       => $this->config->sign_type,
            'timestamp'       => date('Y-m-d H:i:s'),
            'request_id'      => Support::getRequestId($this->config->terminal_id),
            'term_request_id' => Support::getTermRequestId($this->config->terminal_id)
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

    public function pay($name, Collection $params):Collection
    {
        return $this->makePay(__CLASS__, $name, $this->app, $this->config)->pay($this->payload(), $params);
    }

    public function refund($name, Collection $order):Collection
    {
        return new Collection();
    }

    public function success()
    {
        return "success";
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