<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/6 0006
 * Time: 上午 9:21
 */
namespace common\models;

use common\components\AlxgBase;

class CarNews extends AlxgBase
{
    protected $currentTable = '{{%car_news}}';

    public static $status = [
        '0' => '删除',
        '1' => '正常'
    ];

}