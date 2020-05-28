<?php
/**
 * Created by PhpStorm.
 * User: zhangzhen
 * Date: 2019/11/12
 * Time: 16:35
 */

namespace console\controllers;

use common\components\Eddriving;
use common\models\CarCoupon;
use common\models\CarCouponPackage;
use common\models\TravelCompany;
use common\models\TravelUsers;
use yii\console\Controller;
use Yii;

class RepairController  extends Controller {

    //修复代驾的优惠券
    public function actionEcar(){
        //
        $map = [
            'remark' => '0526update',
            'coupon_type' => 1,
            'amount' => 20,
            'status' => 1,
            'uid' => 54622,
        ];
        $package = new CarCoupon();
        $sql = $package->table()->where($map)->limit((int)15)->getLastSql();
        $db = Yii::$app->db;
        $list = $db->createCommand($sql)->queryAll();
        $now = time();

        $EddrApi = new Eddriving();

        foreach ($list as $key=> $val) {
            print_r($key);

            if($val['company']==0){
                $r = $EddrApi->coupon_bind($val['coupon_sn'], $val['mobile']);
                if ($r === false) {
                    $abnormal[] = $val['id'];
                    continue;
                }
                $val['active_time'] = $now;
               // $val['uid'] = $this->user['uid'];
                $val['bindid'] = $r['bindid'];
                $val['bonusid'] = $r['bonusid'];
                //$val['mobile'] = $this->user['mobile'];
                $val['status'] = 1;//将卡券改为已激活状态
                //这里主要获得过期时间
                $r = $EddrApi->coupon_allinfo($val['coupon_sn'], $val['mobile']);
                if ($r === false) {
                    //没有查到的情况下，默认过期时间为绑定后20天

                    if (!$val['expire_days']) $val['expire_days'] = 20;
                    $val['use_limit_time'] = $now + $val['expire_days'] * 24 * 3600;
                } else {
                    $val['bindsn'] = $r['bind_info']['sn'];
                    if($val['companyid']==10 && $val['c_time']>1556640000  ){

                    } elseif( $val['expire_days']!==0 && strtotime($r['endDate'])>(time()+$val['expire_days']*24*60*60)){
                        $val['use_limit_time'] = time()+$val['expire_days']*24*60*60;
                    }else{
                        $val['use_limit_time'] = strtotime($r['endDate']);
                    }
                }

                $val['companyid'] = 33;
                $r = $package->myUpdate($val);

            }


        }

    }
}