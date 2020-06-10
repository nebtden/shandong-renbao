<?php

namespace frontend\controllers;

use common\models\Car_oraddlog;
use common\models\Car_paternalor;
use common\models\Car_rescueor;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use yii\helpers\Url;
use common\components\Helpper;
use common\components\W;
use common\components\AlxgBase;
use common\models\CarUserCarno;
use common\components\CarCouponAction;
use common\models\CarCoupon;
use common\models\Car_coupon_explain;

class WebcarrescueController extends WebcloudcarController
{
    public $menuActive = 'carhome';

    public function beforeAction($action = null)
    {
        parent::beforeAction($action);
        return true;
    }

    public function actionIndex()
    {
        //获得用户的车牌信息，如果没有车辆，需要先添加车牌信息才可使用
        $user = $this->isLogin();
        $carlist = (new CarUserCarno())->get_user_bind_car($user['uid']);
        $rescueItems = CarCoupon::$coupon_faulttype;
        return $this->render('index', [
            'rescueItems' => $rescueItems,
            'carlist' => $carlist,
            'user' => $user
        ]);
    }

    /**
     * 优惠券列表
     */
    public function actionRescuecp()
    {
        $request = Yii::$app->request;
        $scene = $request->get('scene', 0);
        $user = $this->isLogin();
        $temp = (new CarCoupon())->get_user_bind_coupon_list($user['uid'], 2);
        $list = [];
        $now = time();
        foreach ($temp as $cp) {
            //选取相关场景的券，再判断是否过期
            if ((int)$cp['use_scene'] === (int)$scene) {
                if ($cp['status'] == 1 && $now < $cp['use_limit_time']) {
                    $list[] = $cp;
                }
            }
        }
        $use_text = (new Car_coupon_explain())->get_use_text();
        return $this->render('rescuecp', ['list' => $list, 'use_text' => $use_text]);
    }

    /**
     * 派单
     */
    public function actionPaidan()
    {
        $request = Yii::$app->request;
        if (!$request->isPost) return '非法访问';
        $user = $this->isLogin();
        $fans = $this->fans_account();
        $user['mobile'] = $fans['mobile'];
        $data = $request->post();
        //print_r($data);
        //初步验证数据
        $rescue_way = $data['rescue_way'];
        $rescueWays = array_keys(CarCoupon::$coupon_faulttype);
        if (!in_array($rescue_way, $rescueWays)) {
            return $this->json(0, '请选择服务项目');
        }
        $car_id = $data['car_id'];
        if (!$car_id) return $this->json(0, '请选择故障车辆');

        $fault_address = $data['fault_address'];
        $longitude = $data['longitude'];
        $latitude = $data['latitude'];
        if (!$fault_address || !$longitude || !$latitude) {
            return $this->json(0, '请重新定位故障发生地点');
        }
        $coupon_id = $data['coupon'];
        if (!$coupon_id) return $this->json(0, '请选择服务券');

        $remark = $data['remark'];
        unset($data);

        //获得车辆信息
        $car = (new CarUserCarno())->table()->where(['id' => $car_id, 'uid' => $user['uid'], 'status' => 1])->one();
        if (!$car) return $this->json(0, '请选择故障车辆');

        $now = time();
        $detail = [];
        $db = Yii::$app->db;
        $trans = $db->beginTransaction();
        try {
            //使用优惠券
            $couponObj = new CarCouponAction($user);
            $rs = $couponObj->useCoupon($coupon_id);
            if ($rs === false) {
                throw new \Exception($couponObj->msg);
            }
            //写入数据
            //主订单
            $parentOrder = (new Car_paternalor())->place_an_order($user['uid'], $coupon_id);
            if ($parentOrder === false) {
                throw new \Exception('001:下单失败');
            }
            //通过接口下单
            $info = [
                'orderid' => $parentOrder['order_no'],
                'calltime' => date("YmdHis", $parentOrder['c_time']),
                'customername' => $user['nickname'],
                'phone' => $user['mobile'],
                'carno' => $car['card_province'] . $car['card_char'] . $car['card_no'],
                'carbrand' => $car['card_brand'],
                'carmodel' => $car['car_model_small_fullname'],
                'faultaddress' => $fault_address,
                'longitude' => $longitude,
                'latitude' => $latitude,
                'rescueway' => $rescue_way,
                'remark' => $remark,
                'status' => 0,
                'c_time' => $now,
                'uid' => $parentOrder['uid'],
                'coupon_id' => $parentOrder['coupon_id'],
                'm_id' => $parentOrder['id'],
            ];
            $rs = $this->send_rescue_order($info);
            if (!$rs) {
                throw new \Exception('002:下单失败');
            }
            //分订单，订单详情部分
            $detail = (new Car_rescueor())->plac_an_order($info);
            if ($detail === false) {
                throw new \Exception('003:下单失败');
            }
        } catch (\Exception $e) {
            $trans->rollBack();
            return $this->json(0, $e->getMessage());
        }
        $trans->commit();
        return $this->json(1, 'ok', ['id' => $detail['id'], 'orderno' => $detail['orderid']]);
    }

    protected function send_rescue_order($info)
    {
        $data = [
            'orderId' => $info['orderid'],
            'callTime' => $info['calltime'],
            'customerName' => $info['customername'],
            'phone' => $info['phone'],
            'carNo' => $info['carno'],
            'carBrand' => $info['carbrand'],
            'carModel' => $info['carmodel'],
            'faultAddress' => $info['faultaddress'],
            'longitude' => $info['longitude'],
            'latitude' => $info['latitude'],
            'rescueWay' => $info['rescueway'],
        ];
        if ($info['remark']) $data['remark'] = $info['remark'];
        $res = W::rescueapi($data);
        if ((int)$res['errorCode'] === 0) return true;
        return false;
    }

    /**
     * 取消订单
     * @return array|string
     */
    public function actionCancel()
    {
        $request = Yii::$app->request;
        if (!$request->isPost) return '非法访问';
        $user = $this->isLogin();
        $order_no = $request->post('order_no', null);
        $map['uid'] = $user['uid'];
        $map['orderid'] = $order_no;
        $order = (new Car_rescueor())->table()->where($map)->one();
        if (!$order) return $this->json(0, '订单不存在');

        $data = [
            'orderId' => $order['orderid'],
            'reason' => '已无需救援',
        ];
        $trans = Yii::$app->db->beginTransaction();
        $sta = 1;
        try {
            (new Car_rescueor())->myUpdate(['status' => 7], ['id' => $order['id']]);
            $cObj = new CarCouponAction($user);
            $rs = $cObj->unuseCoupon($order['coupon_id']);
            if ($rs === false) {
                throw new \Exception('取消订单失败');
            }
            $rs = W::cancelororder($data);
            if (0 !== (int)$rs['errorCode']) {
                throw new \Exception($rs['msg']);
            }
        } catch (\Exception $e) {
            $sta = 0;
            $msg = $e->getMessage();
            $trans->rollBack();
        }
        $trans->commit();
        return $this->json($sta, $msg);
    }

    /**
     * 同步订单信息
     */
    public function actionSyncorder()
    {
        $request = Yii::$app->request;
        if (!$request->isPost) return '非法访问';
        $user = $this->isLogin();
        $order_no = $request->post('order_no', null);

        $map['orderid'] = $order_no;
        $point = (new Car_oraddlog())->table()->select('longitude as lng,latitude as lat')->where($map)->one();

        $map['uid'] = $user['uid'];

        $s = (new Car_rescueor())->table()->select('status')->where($map)->one();

        $res['status'] = $s['status'];
        $res['lbs'] = $point ? [$point] : [];

        return $this->json(1, '0k', $res);
    }
}