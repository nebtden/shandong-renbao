<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

class CarFinanceUserAccount extends ActiveRecord
{
    /**
     * @return string Active Record 类关联的数据库表名称
     */
    public static function tableName()
    {
        return '{{%car_finance_user_account}}';
    }

    /**
     * Returns static class instance, which can be used to obtain meta information.
     * @param bool $refresh whether to re-create static instance even, if it is already cached.
     * @return static class instance.
     */
    public static function instance($refresh = false)
    {
        // TODO: Implement instance() method.
    }

    //账户类型
    public static $account_type = [
        1 => '支付宝',
        //2 => '银行卡'
    ];

    //返回账户类型的文字描述
    public static function AccountTypeText($type)
    {
        return self::$account_type[(int)$type];
    }
}