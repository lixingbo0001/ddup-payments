<?php

namespace Ddup\Payments\Helper;


use Ddup\Part\Libs\Unit;
use Ddup\Part\Struct\StructReadable;
use Illuminate\Support\Collection;

class MoneyFilter
{

    private static $tranform_config = [];

    static function init($config)
    {
        self::$tranform_config = $config;
    }

    private static function keys($name, $before = true)
    {
        if (!isset(self::$tranform_config[$name])) return [];

        return array_get(self::$tranform_config, ($before ? 0 : 1));
    }

    private static function filter(&$params, $name, $before = true)
    {

        if ($params instanceof Collection) {
            self::disposeCollection($params, $name, $before);
            return;
        }

        if ($params instanceof StructReadable) {
            self::disposeStruct($params, $name, $before);
            return;
        }
    }

    private static function disposeCollection(Collection &$params, $name, $before)
    {
        $keys = self::keys($name, $before);

        foreach ($keys as $name) {

            if ($params->offsetExists($name)) {

                $val = $params->get($name);

                $params->offsetSet($name, $before ? Unit::yuntoFen($val) : Unit::fentoYun($val));
            }
        }
    }

    private static function disposeStruct(StructReadable &$params, $name, $before)
    {
        $keys = self::keys($name, $before);

        foreach ($keys as $name) {

            if (!is_null($params->get($name))) {
                $val = $params->get($name);

                $params->$name = $before ? Unit::yuntoFen($val) : Unit::fentoYun($val);
            }
        }
    }

    static function before($name, &$params)
    {
        self::filter($params, $name, true);
    }

    static function after($name, &$params)
    {
        self::filter($params, $name, false);
    }

}