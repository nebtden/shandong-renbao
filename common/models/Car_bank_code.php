<?php

namespace common\models;

use common\components\AlxgBase;

class Car_bank_code extends AlxgBase
{
    protected $currentTable = '{{%car_bank_code}}';
    //状态，0：禁用，1：正常,2：完成兑换
    public static $status = [
        '0' => '禁用',
        '1' => '正常',
        '2' => '完成兑换'
    ];

}