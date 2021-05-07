<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/26 0026
 * Time: 下午 2:14
 */


namespace common\models;

use Yii;
use common\components\AlxgBase;
use common\components\W;

class CarMeal extends AlxgBase
{
    protected $currentTable = '{{%car_meal}}';

    public static $status = [
        '0' => '禁用',
        '1' => '正常'
    ];

}