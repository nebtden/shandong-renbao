<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/26 0026
 * Time: 下午 4:45
 */
namespace common\models;

use Yii;
use common\components\AlxgBase;
use common\components\W;

class CarCouponMeal extends AlxgBase
{
    protected $currentTable = '{{%car_coupon_meal}}';

    public static $status = [
        '0' => '禁用',
        '1' => '正常'
    ];
    public function checkCardNo( $package_pwd = '', $id = 0)
    {
        if (!$package_pwd) return false;
        $where = '   package_pwd = "' . $package_pwd . '"';
        if ($id) {
            $where .= ' and id <> ' . $id;
        }
        $res = $this->table()->select('count(id) as cot')->where($where)->one();
        return $res['cot'];
    }

    public function generateCardNo( $len = 8)
    {
        $package_pwd = W::createNonceCapitalStr($len);
        while ($this->checkCardNo($package_pwd)) {
            $package_pwd = W::createNonceCapitalStr($len);
        }
        return $package_pwd;
    }
}