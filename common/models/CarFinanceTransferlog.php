<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

class CarFinanceTransferlog extends ActiveRecord
{
    /**
     * @return string Active Record 类关联的数据库表名称
     */
    public static function tableName()
    {
        return '{{%car_finance_transferlog}}';
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

    public static function create_out_biz_no()
    {
        return 'T' . date("YmdHis") . mt_rand(10000, 99999);
    }

    public static function get_status_text($status)
    {
        $status_text = ['0' => '未转账', '1' => '转账成功', '2' => '转账失败'];
        return $status_text[(string)$status];
    }
}