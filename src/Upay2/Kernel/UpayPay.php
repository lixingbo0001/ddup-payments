<?php

namespace Ddup\Payments\Upay2\Kernel;

use Ddup\Part\Libs\Float_;
use Ddup\Payments\Config\PayOrderStruct;
use Ddup\Payments\Contracts\PayableInterface;
use Ddup\Payments\Helper\Application;
use Illuminate\Support\Collection;


abstract class UpayPay implements PayableInterface
{

    protected $config;
    private   $client;

    public function __construct(Application $app, UpayConfig $config)
    {
        $this->config = $config;
        $this->client = new UpayClient($app, $config);
    }

    public function endPoint()
    {
        return '';
    }

    public function getChannel()
    {
        return '';
    }

    public function getTradeType()
    {
        return '';
    }

    abstract function instMid();

    abstract function msgType();

    public function payRequest(array $payload, PayOrderStruct $order):Collection
    {
        $payload['msgType']     = $this->msgType();
        $payload['msgSrc']      = Support::msgSrc($this->config);
        $payload['instMid']     = $this->instMid();
        $payload['billNo']      = $order->order_no;
        $payload['billDate']    = $order->get('created_at');
        $payload['billDesc']    = $order->subject;
        $payload['totalAmount'] = $order->amount;
        $payload['notifyUrl']   = $this->config->notify_url;

        if ($order->get('expired_at')) {
            $payload['expireTime'] = $order->get('expired_at');
        }

        //分账标记
        if ($order->separate_account > 0) {
            $payload['divisionFlag']   = true;
            $payload['platformAmount'] = $order->separate_account;
            $payload['subOrders']      = [
                'mid'         => $this->config->get('mch_id'),
                'totalAmount' => Float_::reduce($order->amount, $order->separate_account),
            ];
        }

        $payload = Support::paraFilter($payload);

        $payload['sign'] = Support::generateSign($payload, $this->config->key);

        $data = $this->client->requestApi($this->endPoint(), $payload);

        $return = [
            'qr_code' => $data->get('billQRCode')
        ];

        return new Collection($return);
    }

}