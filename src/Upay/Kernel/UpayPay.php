<?php

namespace Ddup\Payments\Upay\Kernel;

use Ddup\Part\Libs\Arr;
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

    abstract function bizContent(array $params);

    public function payRequest(array $payload, PayOrderStruct $order):Collection
    {
        $params     = array_merge($payload, $order->toArray());
        $bizContent = $this->bizContent($params);
        $params     = Arr::getIfExists($params, $this->getCommonFields());

        $params['biz_content'] = json_encode($bizContent, JSON_UNESCAPED_UNICODE);
        $params['biz_channel'] = $this->getChannel();
        $params['biz_type']    = $this->getTradeType();

        $params['sign'] = Support::generateSign($params, $this->config->key);

        $result = $this->client->requestApi($this->endPoint(), $params);

        $bzContent = json_decode($result->get('biz_content'), true);
        $bzContent = new Collection($bzContent);

        $return = [
            "amount"         => $bzContent->get('total_amount'),
            "order_no"       => $bzContent->get('ext_no'),
            "transaction_id" => $bzContent->get('trade_no'),
            "wx_appid"       => '',
            "openid"         => $bzContent->get('buyer_id'),
            "attach"         => $bzContent->get('attach'),
            'qr_code'        => $bzContent->get('qr_code')
        ];

        return new Collection($return);
    }

    protected function getCommonFields()
    {
        return [
            'merchant_id',
            'terminal_id',
            'operator_id',
            'device_id',
            'request_id',
            'term_request_id',
            'timestamp',
            'biz_channel',
            'biz_type',
            'biz_content',
            'notify_url',
            'version',
            'app_info',
            'sign',
            'sign_type',
            'sign_format'
        ];
    }


}