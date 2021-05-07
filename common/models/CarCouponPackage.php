<?php

namespace common\models;

use Yii;
use common\components\AlxgBase;
use common\components\W;

class CarCouponPackage extends AlxgBase
{
    protected $currentTable = '{{%car_coupon_package}}';

//0禁用,1正常,2已使用,3已过期
    public static $status = [
        '0' => '禁用',
        '1' => '正常',
        '2' => '已激活',
        '3' => '已过期'
    ];

    public function checkCardNo( $package_pwd = '', $id = 0)
    {
        if (!$package_pwd) return false;

        $where = '  package_pwd = "' . $package_pwd . '"';
        if ($id) {
            $where .= ' and id <> ' . $id;
        }

        $res = $this->table()->select('count(id) as cot')->where($where)->one();
        return $res['cot'];
    }

    public function generateCardNo( $len = 8)
    {
        $data = [];

        $data['package_pwd'] = W::createNonceCapitalStr($len);
        while ($this->checkCardNo( $data['package_pwd'])) {
            $data['package_pwd'] = W::createNonceCapitalStr($len);
        }
        return $data;
    }

    /**
     * 将状态为正常的，但已过期的卡包置为已过期
     */
    public function update_status_auto()
    {
        $now = time();
        $condition = "`status` = 1 AND `use_limit_time` > 0 AND `use_limit_time` < {$now}";
        return $this->myUpdate(['status' => 3],$condition);
    }
}