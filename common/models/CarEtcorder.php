<?php

namespace common\models;


class CarEtcorder extends Base_model
{
    public static function tableName()
    {
        return '{{%car_etc_order}}';
    }

    //1：取消 0：待收货 1:进行中 2：成功 3：失败 4:待激活
    public static $status_text = [
        '-1' => '已取消',
        '0' => '待收货',
        '1' => '进行中',
        '2' => '成功',
        '3' => '失败',
        '4' => '待激活'
    ];

    //收货状态
    public static $receiving_text = [
        '0' => '未发货',
        '1' => '已发货',
        '2' => '已收货',
        '3' => '取消收货',
        '4' => '退货'
    ];

    //状态改变
    public static $old_status_to_new = [
        '0' => '2',
        '1' => '2',
        '2' => '3',
        '3' => '0',
        '4' => '0',
    ];

    //ETC机构订单状态描述
    public static $etc_status = [
        '1'=> '订单提交（待审核）',
        '2'=> '订单取消（用户取消）',
        '3'=> '取消中',
        '4'=> '订单取消（后台取消）',
        '5'=> '审核成功',
        '6'=> '审核不通过',
        '7'=> '信息采集失败',
        '8'=> '信息采集成功',
        '9'=> '开卡失败',
        '10'=> '开卡成功',
        '11'=> '已发货',
        '12'=> '已激活',
    ];

}