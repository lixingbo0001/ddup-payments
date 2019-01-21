<?php

namespace Ddup\Payments\Helper;


use Ddup\Part\Libs\Unit;
use Illuminate\Support\Collection;

class MoneyFilter
{

    private static function keys($name, $before = true)
    {
        return config('payment.money_transfrom.' . $name . '.' . ($before ? 0 : 1), []);
    }

    private static function filter(Collection &$params, $name, $before = true)
    {
        $keys = self::keys($name, $before);

        foreach ($keys as $name) {
            if ($params->offsetExists($name)) {

                $val = $params->get($name);

                $params->offsetSet($name, $before ? Unit::yuntoFen($val) : Unit::fentoYun($val));
            }
        }
    }

    static function before($name, Collection &$params)
    {
        self::filter($params, $name, true);
    }

    static function after($name, Collection &$params)
    {
        self::filter($params, $name, false);

    }

}