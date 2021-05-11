<?php

namespace common\models;

use frontend\controllers\SegwayController;
use Yii;
use common\components\AlxgBase;

class Car_paternalor extends AlxgBase
{
    protected $currentTable = '{{%car_paternalor}}';

   // 1道路救援，2代驾，3在线洗车券购买,5油卡订单 7年检 8代步
    public static $type = [
        1=>'道路救援',
        2=>'代驾服务',
        3=>'洗车服务',
        4=>'',
        5=>'油卡充值',
        6=>'',
        INSPECTION=>'年检服务',
        SEGWAY=>'代步服务',
        10=>'臭氧杀菌服务',
    ];



    public static $cloudv2img = [
        1=>"/frontend/web/cloudcarv2/images/load-rescue.png",
        2=>"/frontend/web/cloudcarv2/images/super-driving.png",
        3=>"/frontend/web/cloudcarv2/images/wash-car.png",
        4=>"/frontend/web/cloudcarv2/images/wash-car.png",
        5=>"/frontend/web/cloudcarv2/images/oil-card-recharge.png",
        6=>"",
        INSPECTION=>"/frontend/web/cloudcarv2/images/agency-yearly-inspection.png",
        SEGWAY=>"/frontend/web/cloudcarv2/segway/img/daibu.png",
        10=>"/frontend/web/cloudcarv2/images/disinfect.png",
    ];

    /**
     * 通进时间与用户id生成订单号
     * @param int $uid
     * @param string $prefix
     * @return string
     */
    public function create_order_no($uid = 0,$prefix = 'R')
    {
        $str = date("YmdHis");
        $rd = mt_rand(1000, 9999);
        return $prefix . $str . $rd . $uid;
    }


    public function main_order($uid, $coupon_id, $amount,$prefix)
    {

        $order_no = $this->create_order_no($uid, $prefix);
        $data = [
            'order_no' => $order_no,
            'uid' => $uid,
            'type' => 2,
            'coupon_id' => $coupon_id,
            'coupon_amount' => $amount,
            'c_time' => time()
        ];
        $id = $this->myInsert($data);
        if ($id) {
            $data['id'] = $id;
            return $data;
        }
        return false;
    }

    /**
     * 下救援单
     * @param int $uid
     * @param int $coupon_id
     * @return array|bool
     */
    public function place_an_order($uid = 0, $coupon_id = 0)
    {
        $data = [
            'order_no' => $this->create_order_no($uid),
            'uid' => $uid,
            'type' => 1,
            'coupon_id' => $coupon_id,
            'coupon_amount' => 0,
            'c_time' => time(),
        ];
        $id = $this->myInsert($data);
        if (!$id) return false;
        $data['id'] = $id;
        return $data;
    }
}