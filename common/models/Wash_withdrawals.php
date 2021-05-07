<?php

namespace common\models;



use yii\db\Expression;

class Wash_withdrawals extends Base_model
{
    public static function tableName()
    {
        return "{{%wash_withdrawals}}";
    }

    //1已申请，2已打款
    public static $status = [
        0=>'删除',
        1=>'已申请',
        2=>'已打款',
        3=>'已拒绝'
    ];

    // 账户类型 1支付宝，2银行
    public static $ac_type = [
        1=>'支付宝',
        2=>'银行'
    ];

}