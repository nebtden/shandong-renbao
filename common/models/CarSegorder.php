<?php

namespace common\models;

use common\components\AlxgBase;

class CarSegorder extends AlxgBase
{
    const ORDER_UNPATCH = 1;
    const ORDER_PATCHING = 2;
    const ORDER_UNBEGIN = 3;
    const ORDER_BEGIN = 4;
    const ORDER_ARRIVE = 5;
    const ORDER_SERVE = 6;
    const ORDER_FINISH = 7;
    const ORDER_CANCEL = 8;

    public static $orderStatusText = [
        self::ORDER_UNPATCH  => '未派单',//待接单
        self::ORDER_PATCHING => '派单中',//待服务
        self::ORDER_UNBEGIN  => '未发车',//待服务
        self::ORDER_BEGIN    => '已发车',//待服务
        self::ORDER_ARRIVE   => '已到达',//待服务
        self::ORDER_SERVE    => '服务中',//全部
        self::ORDER_FINISH   => '已完成',//待评价
        self::ORDER_CANCEL   => '已取消',//全部
    ];
    public static $sd_order_to_local = [
        self::ORDER_UNPATCH  => ORDER_UNSURE,
        self::ORDER_PATCHING => ORDER_SURE,
        self::ORDER_UNBEGIN  => ORDER_UNHAND,
        self::ORDER_BEGIN    => ORDER_HANDLING,
        self::ORDER_ARRIVE   => ORDER_WAITING,
        self::ORDER_SERVE    => ORDER_SERVEING,
        self::ORDER_FINISH   => ORDER_SUCCESS,
        self::ORDER_CANCEL   => ORDER_CANCEL,
    ];

    public static $orderStatusTextxu = [

        ORDER_UNSURE   => '未派单',//待接单
        ORDER_SURE     => '派单中',//待服务
        ORDER_UNHAND   => '未发车',//待服务
        ORDER_HANDLING => '已发车',//待服务
        ORDER_WAITING  => '已到达',//待服务
        ORDER_SERVEING => '服务中',//全部
        ORDER_SUCCESS  => '已完成',//待评价
        ORDER_CANCEL   => '已取消'//全部
    ];

    public static $order_doc = [
        ORDER_UNSURE => ['class' => 'apply-order', 'html' => '<p>您的预约已提交</p><p>请等待客服确认，保持电话畅通</p>', 'img' => 'apply-icon.png', 'new' => 1, 'sd' => self::ORDER_UNPATCH],
        ORDER_SURE => ['class' => 'get-order', 'html' => '<p>代步车预约已接单</p><p></p>', 'img' => 'get-order-icon.png', 'new' => 2, 'sd' => self::ORDER_PATCHING],
        ORDER_UNHAND => ['class' => 'get-order', 'html' => '<p>代步车预约未发车</p><p></p>', 'img' => 'get-order-icon.png', 'new' => 2, 'sd' => self::ORDER_UNBEGIN],
        ORDER_HANDLING => ['class' => 'get-order', 'html' => '<p>代步车预约已发车</p><p>即将为您提供服务</p>', 'img' => 'get-order-icon.png', 'new' => 2, 'sd' => self::ORDER_BEGIN],
        ORDER_WAITING => ['class' => 'arrived', 'html' => '<p>您预约的代步车</p><p>已到达指定地点</p>', 'img' => 'arrive-icon.png', 'new' => 2, 'sd' => self::ORDER_ARRIVE],
        ORDER_SERVEING => ['class' => 'using', 'html' => '<p>您的代步车服务</p><p>正在使用中</p>', 'img' => 'using-icon.png', 'new' => 0, 'sd' => self::ORDER_SERVE],
        ORDER_SUCCESS => ['class' => 'using', 'html' => '<p>您的代步车服务已完成</p><p></p>', 'img' => 'finished-icon.png', 'new' => 3, 'sd' => self::ORDER_FINISH],
        ORDER_CANCEL => ['class' => 'cancel-order', 'html' => '<p>代步车预约已取消</p><p></p>', 'img' => 'cancel-icon.png', 'new' => 0, 'sd' => self::ORDER_CANCEL],
    ];

    protected $currentTable = '{{%car_segorder}}';
}