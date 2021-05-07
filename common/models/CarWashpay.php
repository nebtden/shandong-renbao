<?php

namespace common\models;

use Yii;
use common\components\AlxgBase;

class CarWashpay extends AlxgBase
{
    const PRICE = 15;
    const CARD_TYPE = 6;//卡包对应的类型
    protected $currentTable = '{{%car_washpay}}';

    public static $status = [
        '0' => '未完成',
        '1' => '已完成',
        '2' => '已完成',
    ];

    public static $old_status_to_new = [
        '0' => '3',
        '1' => '3',
        '2' => '3',
    ];

    public static $statusarr=['0'=>'未支付','1'=>'已支付','2'=>'已激活'];
    public static $paytype=['1'=>'微信支付','2'=>'待定'];
}