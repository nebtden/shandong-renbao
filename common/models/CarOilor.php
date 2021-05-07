<?php

namespace common\models;

use Yii;
use common\components\AlxgBase;

class CarOilor extends AlxgBase
{
    public static $status_text = [
        '0' => '处理中',
        '1' => '处理中',
        '2' => '充值成功',
        '3' => '充值失败',
        '4' => '处理中',
        '9' => '处理中'
    ];
    public static $status = [
        '1' => '处理中',
        '2' => '充值成功',
        '3' => '充值失败',
    ];

    public static $old_status_to_new = [
        '0' => '1',
        '1' => '1',
        '2' => '3',
        '3' => '0',
        '4' => '2',
        '9' => '2'
    ];

    public static $oilcardtype = [
        '1' => '中石油',
        '2' => '中石化'
    ];

    public static $oil_company = [
        'jisutong'=>1,
        'diandian'=>2
    ];


    protected $currentTable = '{{%car_oilor}}';
}