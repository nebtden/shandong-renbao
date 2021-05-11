<?php

namespace common\models;

use Yii;
use common\components\AlxgBase;

class Aiqiyi extends Base_model
{

    public static $status = [
        '1' => '处理中',
        '2' => '充值成功',
        '3' => '充值失败',
    ];


    protected $currentTable = '{{%aiqiyi}}';
}