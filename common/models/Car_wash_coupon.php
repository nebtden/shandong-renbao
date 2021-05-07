<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/20 0020
 * Time: 下午 3:52
 */

namespace common\models;

use Yii;
use common\components\AlxgBase;

class Car_wash_coupon extends AlxgBase
{
    protected $currentTable = '{{%car_wash_coupon}}';

    //0未绑定，1已绑定，2已使用
    public static $status = [
        '0' => '未绑定',
        '1' => '已绑定',
        '2' => '已使用'
    ];

    /**
     * @param int $coupon_id
     * @param int $uid
     * @return array    ['sn','status']
     */
    public function get_service_sn($uid = 0, $coupon_id = 0)
    {
        //step1,查询当月是否绑定了卡
        $month = date("Ym");
        $map['uid'] = $uid;
        $map['month'] = $month;
        $map['coupon_id'] = $coupon_id;
        $info = $this->table()->where($map)->one();
        if ($info) {
            return ['sn' => $info['servicecode'], 'status' => $info['status']];
        }
        //如果没有，则进行当月的绑定
        return $this->bind_wash_coupon($uid, $coupon_id);
    }

    /**
     * @param int $uid
     * @param int $coupon_id
     */
    public function bind_wash_coupon($uid = 0, $coupon_id = 0)
    {
        $month = date("Ym");
        $map['month'] = $month;
        $map['status'] = 0;
        $sql = $this->table()->where($map)->limit(1)->getLastSql();
        $sql .= " FOR UPDATE";

        $info = [];
        $error = false;
        $db = Yii::$app->db;
        $trans = $db->beginTransaction();
        try{
            $info = $db->createCommand($sql)->queryOne();
            if(!$info){
                throw new \Exception('没卡券了');
            }
            $info['uid'] = $uid;
            $info['coupon_id'] = $coupon_id;
            $info['status'] = 1;
            $this->myUpdate($info);
        }catch (\Exception $e){
            $error = true;
            $trans->rollBack();
        }
        if($error) return false;
        $trans->commit();
        return ['sn' => $info['servicecode'], 'status' => $info['status']];
    }

}