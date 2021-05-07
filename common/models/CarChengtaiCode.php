<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/12/10 0010
 * Time: 上午 10:48
 */
namespace common\models;

use common\components\AlxgBase;

class CarChengtaiCode extends AlxgBase
{
    protected $currentTable = '{{%car_chengtai_code}}';
    //状态，0：禁用，1：正常,2：完成兑换
    public static $status = [
        '0' => '禁用',
        '1' => '未兑换',
        '2' => '完成兑换'
    ];

}