<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/2 0002
 * Time: 下午 3:45
 */
namespace common\models;

use Yii;

class Car_withdrawals extends Base_model
{

    //1已申请，2已打款
    public static $status = [
        0=>'删除',
        1=>'已申请',
        2=>'已打款',
        3=>'已拒绝'
    ];

    //账户类型 1支付宝，2银行
    public static $ac_type = [
        1=>'支付宝',
        2=>'银行'
    ];

    public static function tableName()
    {
        return '{{%car_withdrawals}}';
    }

}