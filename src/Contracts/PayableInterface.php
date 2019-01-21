<?php

namespace Ddup\Payments\Contracts;

use Illuminate\Support\Collection;

interface PayableInterface
{
    public function pay(Array $payload, Collection $params): Collection;

    function getChannel();

    function getTradeType();

}

