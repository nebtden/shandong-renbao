<?php

namespace common\models;

use common\components\AlxgBase;

class CarInsorder extends AlxgBase
{
    public static $status_text = [
        '0' => '未处理',
        '1' => '未处理',
        '2' => '处理中',
        '3' => '已处理',
        '4' => '已关闭'
    ];
    public static $order_status = [
        ORDER_CANCEL => '已取消',
        ORDER_HANDLING => '处理中',
        ORDER_SUCCESS => '成功',
        ORDER_FAIL => '失败',
        ORDER_LOCK => '处理中',
        ORDER_CANCELING => '退单中',
        ORDER_UNSURE => '待确认'
    ];


    public static $old_status_to_new = [
        ORDER_CANCEL => '0',
        ORDER_HANDLING => '2',
        ORDER_SUCCESS => '3',
        ORDER_FAIL => '0',
        ORDER_LOCK => '2',
        ORDER_UNSURE => '1',
//        '0' => '1',
//        '1' => '1',
//        '2' => '2',
//        '3' => '3',
//        '4' => '0'
    ];

    public static $ins_company = [
        'diandian' => 2
    ];


    protected $currentTable = '{{%car_insorder}}';
}