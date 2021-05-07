<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/8/7 0007
 * Time: 上午 11:43
 */

namespace common\models;

use common\components\AlxgBase;

class Code extends AlxgBase
{
    protected $currentTable = '{{%code}}';
    //状态，0：禁用，1：正常,2：完成兑换
//    public static $status = [
//        '0' => '禁用',
//        '1' => '正常',
//        '2' => '完成兑换'
//    ];

}