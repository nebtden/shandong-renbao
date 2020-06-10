<?php

namespace frontend\controllers;

use common\components\Eddriving;
use common\components\Helpper;
use common\models\CarCoupon;
use Yii;
use yii\web\Controller;
use common\models\CarCouponPackage;

class CartaskController extends Controller
{
    public function beforeAction($action = null)
    {
        parent::beforeAction($action);
        $at = Yii::$app->request->get('at');
        if ($at != 'a32kfaai3kf32akfo0f23') {
            echo '非法访问';
            return false;
        }
        return true;
    }

    /**
     * 检查卡包
     */
    public function actionCheckcouponpackage()
    {
        set_time_limit(0);
        //条件为正常状态下已过期的置为已过期
        $r = (new CarCouponPackage())->update_status_auto();
        return $r;
    }

    /**
     * 检查券
     */
    public function actionCheckecoupon()
    {
        set_time_limit(0);
        $r = (new CarCoupon())->update_status_auto_ecoupon();
        return $r;
    }

    /**
     * 检查救援券
     */
    public function actionChecksavecoupon()
    {
        set_time_limit(0);
        $r = (new CarCoupon())->update_status_auto_savecoupon();
        return $r;
    }

    /**
     * 检查洗车券
     */
    public function actionCheckwashcoupon()
    {
        set_time_limit(0);
        $r = (new CarCoupon())->update_status_auto_washcoupon();
        return $r;
    }

    /**
     * 检查代驾券是否使用或过期，通过接口
     */
    public function actionCheckecar()
    {
        $obj = new CarCoupon();
        $cache = Yii::$app->cache;
        $key = "ecar_sync_info";
        $info = $cache->get($key);
        if ($info === false) {
            $info['page'] = 1;
        }
        $page = $info['page'];
        $pagesize = 20;
        $list = $obj->table()
            ->select("id,coupon_sn,mobile,status,use_time")
            ->where(['coupon_type' => 1, 'status' => 1])
            ->page($page, $pagesize)
            ->all();
        if (!$list) {
            $info['page'] = 1;
            $cache->set($key, $info);
            return json_encode(['status' => 1, 'msg' => '已完成']);
        }
        $info['page']++;
        $cache->set($key, $info);

        $edr = new Eddriving();
        foreach ($list as $val) {
            $res = $edr->coupon_allinfo($val['coupon_sn'], $val['mobile']);
            if ($res) {
                if (empty($res['bind_info'])) continue;
                $temp = $res['bind_info'];
                if ($temp['status'] == 2) {
                    $val['status'] = 2;
                    $val['use_time'] = strtotime($temp['used_time']);
                } elseif ($temp['status'] == 3) {
                    $val['status'] = 3;
                }
                $obj->myUpdate($val);
            }
        }
        return json_encode(['status' => 0, 'msg' => '未完成']);
    }
}