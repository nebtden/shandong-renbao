<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/4/21
 * Time: 14:29
 */

namespace common\models;

use common\components\AlxgBase;

class CarRenbaoOrder extends AlxgBase
{
    protected $currentTable = '{{%car_renbao_order}}';

    public static $status = [
        1 => '已下单',
        2 => '已使用'
    ];

    public static $service = [
        'DHYCCJ001' => [
            'type'=>10,
            'name'=>'臭氧杀菌服务',
            'tableName'=>'car_disinfection_order',
            'field'=>'coupon_id'
        ],
        'DHYCXC001' => [
            'type'=>4,
            'name'=>'洗车服务',
            'tableName'=>'car_wash_order',
            'field'=>'couponId',
            ]
    ];

}