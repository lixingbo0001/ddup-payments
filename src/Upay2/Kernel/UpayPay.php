<?php

namespace Ddup\Payments\Upay2\Kernel;

use Ddup\Payments\Config\PayOrderStruct;
use Ddup\Payments\Contracts\PayableInterface;
use Ddup\Payments\Exceptions\PayPaymentException;
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

    abstract function actionType();

    public function payRequest(array $payload, PayOrderStruct $order):Collection
    {
        $payload['msgType']     = $this->actionType();
        $payload['instMid']     = $this->getTradeType();
        $payload['billNo']      = $order->order_no;
        $payload['billDate']    = $order->get('created_at');
        $payload['billDesc']    = $order->subject;
        $payload['totalAmount'] = $order->amount;
        $payload['notifyUrl']   = $this->config->notify_url;

        if ($order->get('expired_at')) {
            $payload['expireTime'] = $order->get('expired_at');
        }

        //分账标记
        $payload['divisionFlag']   = true;
        $payload['platformAmount'] = 0;
        $sub_merchant_id           = $this->config->get('sub_merchant_id');

        if (!$sub_merchant_id) {
            throw new PayPaymentException("富友分账配置缺少子商户号");
        }

        $payload['subOrders'] = [
            [
                'mid'         => $sub_merchant_id,
                'totalAmount' => $order->amount,
            ]
        ];

        $payload = Support::paraFilter($payload);

        $payload['sign'] = Support::generateSign($payload, $this->config->key);

        $data = $this->client->requestApi($this->endPoint(), $payload);

        $return = [
            'qr_code' => $data->get('billQRCode')
        ];

        return new Collection($return);
    }

}