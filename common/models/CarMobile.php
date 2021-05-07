<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/13 0013
 * Time: 下午 3:26
 */
namespace common\models;

use Yii;
use common\components\AlxgBase;
use common\components\W;

class CarMobile extends AlxgBase
{
    protected $currentTable = '{{%car_mobile}}';

    public static $status = [
        '0' => '禁用',
        '1' => '正常'
    ];
}