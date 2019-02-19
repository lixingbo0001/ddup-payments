<?php

namespace Ddup\Payments\Contracts;


use Ddup\Payments\Helper\Application;

interface PaymentProxyAble extends PaymentInterface
{
    function __construct(Application $app);

    function setPayment(PaymentInterface $api);
}

