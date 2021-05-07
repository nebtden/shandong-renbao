<?php

namespace common\models;



use yii\db\Expression;

class Wash_shop extends Base_model
{
    public static function tableName()
    {
        return "{{%wash_shop}}";
    }



    public static $status = [
        0 => '冻结',
        1 => '已申请',
        2 => '申请通过',
        3 => '申请未通过'
    ];


    //核销后将订单金额写入wash_shop表，更新amount，gross_income，service_num字段
    public function recovery($shopOrder)
    {
        $data = [
            'amount' => new Expression('amount + '. (double) $shopOrder['promotion_price'].''), //可提现金额
            'gross_income' => new Expression('gross_income + '.(double) $shopOrder['promotion_price'].''), //总收入
            'service_num' => new Expression('service_num + 1')
        ];

        $res = self::myUpdate($data,'id = '.$shopOrder['shopId'].'');
        if(!$res){
            return false;
        }
        return true;
    }

    /**
     * 提现金额更新
     * @param $amount
     * @param $shop_id
     * @return bool
     */
    public function withdraw($amount,$shop_id)
    {
        $data = [
            'already_amount' => new Expression('already_amount + '. (double) $amount.''), //已提现金额
            'amount' => new Expression('amount - '.(double) $amount.'') //可提现金额
        ];

        $res = self::myUpdate($data,'id = '.$shop_id.'');
        if(!$res){
            return false;
        }
        return true;
    }

}