<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2019\6\3 0003
 * Time: 10:29
 */

namespace common\models;

use common\components\AlxgBase;

class Car_wash_order_taibao extends AlxgBase
{
    protected $currentTable = '{{%car_wash_order_taibao}}';
    //状态 -1取消 1进行中 2完成 3已接单 4已过期
    public static $status_text = [
        '-1' => '取消',
        '1' => '进行中',
        '2' => '完成',
        '3' => '已接单',
        '4' => '已过期'
    ];
    //16-普洗 17-精洗 18-洗车A 19-洗车B 20-洗车C
    public static $service_type = [
        '16' => '普洗',
        '17' => '精洗',
        '18' => '洗车A',
        '19' => '洗车B',
        '20' => '洗车C'
    ];

    //1权益已发放，2权益已使用，3权益已恢复，4权益已过期
    public static $equity_status = [
        '1' => '权益已发放',
        '2' => '权益已使用',
        '3' => '权益已恢复',
        '4' => '权益已过期'
    ];
}