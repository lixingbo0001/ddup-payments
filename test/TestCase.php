<?php
/**
 * Created by PhpStorm.
 * User: lixingbo
 * Date: 2018/10/1
 * Time: 上午10:22
 */

namespace Ddup\Payments\Test;

use Ddup\Logger\Cli\CliLogger;

class TestCase extends \PHPUnit\Framework\TestCase
{

    public function __construct(string $name = null, array $data = [], string $dataName = '')
    {

        date_default_timezone_set('PRC');

        parent::__construct($name, $data, $dataName);
    }

    public function logger()
    {
        return new CliLogger();
    }
}