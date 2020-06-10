<?php

namespace frontend\controllers;

use common\components\BaiduMap;
use common\components\CarCouponAction;
use common\components\DiDi;
use common\components\Eddriving;
use common\components\Helpper;
use common\components\W;
use common\models\Car_coupon_explain;
use common\models\Car_paternalor;
use common\models\CarCommonAddress;
use common\models\CarCoupon;
use common\models\CarCouponPackage;
use common\models\CarPaternalor;
use common\models\CarSubstituteDriving;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use yii\helpers\Url;

class DididrivingController extends CloudcarController
{
    public $layout = "cloudcarv2";
    public $menuActive = 'carhome';

    public function beforeAction($action = null)
    {
        parent::beforeAction($action);
        return true;
    }


    public function actionIndex()
    {
        $user = $this->isLogin();
        $fans = $this->fans_account();
        $mobile = $fans['mobile'];
        $alxg_sign = W::getJSAPIShare(Yii::$app->session['token']);

        //从优惠券订单列表过来的，默认则选择优惠券  @todo simon.zhang
        $request = Yii::$app->request;
        $coupon_id = $request->get('coupon_id', 0);

        $list = (new CarCommonAddress())->get_user_address($user['uid']);
        $coupons = (new CarCoupon())->get_user_ecar_coupon($user['uid'], 1,1);
        $use_text = (new Car_coupon_explain())->get_use_text();
        return $this->render('index', [
            'mobile' => $mobile,
            'common_address' => $list,
            'coupons' => $coupons,
            'use_text' => $use_text,
            'coupon_id' => $coupon_id,
            'alxg_sign' => $alxg_sign,
        ]);
    }


    public function actionList(){
        $user = $this->isLogin();
        $model = new CarPaternalor();
        $main = $model->find()->where([
            'uid'=>$user['uid'],
            'type'=>2
        ])
            ->orderBy('id desc')
            ->asArray()->all();

        $car = new CarSubstituteDriving();
        $coupon = new CarCoupon();
        foreach ($main as &$p) {
                    $temp = $car->table()->where(['uid' => $user['uid'], 'm_id' => $p['id']])->one();
                    if ($temp) {
                        $coupon_info = $coupon->table()->where(['id' => $temp['coupon_id']])->one();
                        $p['status_text'] = CarSubstituteDriving::$status_text[(string)$temp['status']];
                        $p['coupon_name'] = $coupon_info['name'];
                        $p = array_merge($p,$temp);
                    }else{
                        $p['status_text'] = '';
                        $p['coupon_name'] = '';
                    }
        }

        return $this->render('list', [
            'list' => $main,
        ]);
    }

    /**
     * 目的地页面
     */
    public function actionDestination()
    {
        $request = Yii::$app->request;
        //检索范围控制在当前所在的市
        $region = $request->get('region', null);
        //获得用户已经设置的两个常用地址
        $user = $this->isLogin();
        $list = (new CarCommonAddress())->get_user_address($user['uid']);
        return $this->render('destination', ['region' => $region, 'list' => $list]);
    }

    /**
     * 地址检索
     */
    public function actionSearch()
    {
        $request = Yii::$app->request;
        if (!$request->isPost) return '非法访问';
        $q = $request->post('q');
        $r = $request->post('r');
        $list = BaiduMap::search($q, $r);
        if (!$list) return $this->json(0);
        $html = $this->renderPartial('search', ['list' => $list]);
        return $this->json(1, 'ok', $html);
    }

    /**
     * 添加常用地址
     */
    public function actionEditloc()
    {
        $request = Yii::$app->request;
        if (!$request->isPost) return '非法访问';
        $data = $request->post();
        $user = $this->isLogin();
        $data['uid'] = $user['uid'];

        $didi = new DiDi();
        $location = $didi->GetAmapLocation([$data['lat'],$data['lng']]);
        if(!$location){
            return $this->json(0, '用户地图位置或者失败');
        }
        $data = array_merge($data,$location);

        $id = (new CarCommonAddress())->update($data);
        if (!$id) return $this->json(0, '操作失败');
        return $this->json(1, 'ok');
    }

    public function actionCoupon()
    {
        $request = Yii::$app->request;
        $user = $this->isLogin();
        $temp = (new CarCoupon())->get_user_bind_coupon_list($user['uid'], 1);
        $list = [];
        $now = time();
        foreach ($temp as $cp) {
            if ($cp['status'] == 1 && $now < $cp['use_limit_time']) {
                $list[] = $cp;
            }
        }
        $use_text = (new Car_coupon_explain())->get_use_text();
        return $this->render('coupon', ['list' => $list, 'use_text' => $use_text]);
    }

    public function actionGetnearbydrivers()
    {
        $request = Yii::$app->request;
        if (!$request->isPost) return '非法访问';
        $data = $request->post();
        $edr = $this->get_edd();
        $res = $edr->get_nearby_drivers($data['lng'], $data['lat']);
        $list = $res['driverList'];
        $points = [];
        foreach ($list as $val) {
            $points[] = ['lng' => (float)$val['longitude'], 'lat' => (float)$val['latitude']];
        }
        return $this->json(1, 'ok', $points);
    }

    //预估费用
    public function actionCostestimate()
    {
        $request = Yii::$app->request;
        if (!$request->isPost) return '非法访问';
        //startlat,startlng,endlat,endlng,bonus_sn
        $data = $request->post();
        $edr = $this->get_edd();
        //$user = $this->isLogin();
        $fans = $this->fans_account();
        $phone = $fans['mobile'];

        $token = $edr->get_authen_token($phone);

        if ($token === false) {
            return $this->json(0, '用户授权失败');
        }

        $res = $edr->costestimate($token, $data['startlat'], $data['startlng'], $data['endlat'], $data['endlng'], $data['bonus_sn']);
        if ($res === false) {
            return $this->json(0, $edr->errMsg);
        }
        return $this->json(1, 'ok', ['fee' => $res['fee'], 'deduct_money' => $res['deductMoney']]);
    }

    protected function order_cache($uid, $phone, $data = null)
    {
        $cache = Yii::$app->cache;
        $key = "ecar_order_cache_{$uid}_{$phone}";
        if ($data) {
            $cache->set($key, $data, 1800);
            return true;
        }
        return $cache->get($key);
    }

    //下单
    public function actionPlaceorder()
    {
        $request = Yii::$app->request;
        if (!$request->isPost) return '非法访问';
        $data = $request->post();
        $edr = $this->get_edd();
        $fans = $this->fans_account();
        $phone = $fans['mobile'];
        $user = $this->isLogin();
        $user['mobile'] = $phone;

        $token = $edr->get_authen_token($phone);

        if ($token === false) {
            return $this->json(0, '用户授权失败');
        }

        $didi = new DiDi();
        $location = $didi->GetAmapLocation([$data['lat'],$data['lng']]);
        if(!$location){
            return $this->json(0, '用户地图位置或者失败');
        }
        $data = array_merge($data,$location);

        $trans = Yii::$app->db->beginTransaction();
        try {
            //使用优惠券
            $couponObj = new CarCouponAction($user);
            $coupon = $couponObj->useCoupon($data['coupon_id']);
            if ($coupon === false) {
                throw new \Exception($couponObj->msg);
            }
            $mData = $this->main_order($user['uid'], $coupon['id'], $coupon['amount']);
            if (!$mData) {
                throw new \Exception('[001]订单提交失败');
            }
            $res = $edr->commit_order($token,$data['mobile'], $data['address'], $data['lng'], $data['lat'], $data['bonus_sn'], $data['dlat'], $data['dlng'], $data['daddress']);
            if ($res === false) {
                if($edr->errCode == 1){
                    $token = $edr->get_authen_token($phone,true);
                    if($token === false){
                        throw new \Exception('认证失败，请重试');
                    }
                    $res = $edr->commit_order($token,$data['mobile'], $data['address'], $data['lng'], $data['lat'], $data['bonus_sn'], $data['dlat'], $data['dlng'], $data['daddress']);
                    if($res === false){
                        throw new \Exception($edr->errMsg);
                    }
                }else{
                    throw new \Exception($edr->errMsg);
                }
            }





            $start = [
                'address' => $data['address'],
                'lat' => $location['lat'],
                'lng' => $location['lng']
            ];

            $end_location = $didi->GetAmapLocation([$data['dlat'],$data['dlng']]);
            if(!$end_location){
                throw new \Exception('用户地图位置或者失败');
            }

            $end = [
                'address' => $data['daddress'],
                'lat' => $end_location['lat'],
                'lng' => $end_location['lng']
            ];
            $sData = $this->sub_order($user['uid'], $data['mobile'], $data['coupon_id'], $data['bonus_sn'], $mData['id'], $mData['order_no'], $start, $end, $res['bookingId'], $res['bookingType'],$coupon['companyid']);
            $trans->commit();
            //加入缓存
            $this->order_cache($user['uid'], $phone, ['id' => $sData['id'], 'bookingId' => $res['bookingId'], 'bookingType' => $res['bookingType'], 'coupon_id' => $data['coupon_id']]);
            return $this->json(1, 'ok', $res);
        } catch (\Exception $e) {
            $trans->rollBack();
            return $this->json(0, $e->getMessage());
        }
    }

    /**
     * 拉取订单信息
     */
    public function actionPolling()
    {
        $request = Yii::$app->request;
        if (!$request->isPost) return '非法访问';
        $data = $request->post();
        $edr = $this->get_edd();
        $fans = $this->fans_account();
        $phone = $fans['mobile'];
        $user = $this->isLogin();

        $token = $edr->get_authen_token($phone);

        if ($token === false) {
            return $this->json(0, '用户授权失败');
        }
        $pollingCount = $data['polling_count'];
        $res = $edr->order_polling($token, $data['booking_id'], $data['booking_type'], $pollingCount);
        if ($res === false) {
            return $this->json(0, '下单失败');
        }
        $oCache = $this->order_cache($user['uid'], $phone);
        if ($res['pollingState'] != $oCache['pollingState']) {
            $oCache['pollingState'] = $res['pollingState'];
            $oCache['orderId'] = $res['orderId'];
            $oCache['driverId'] = $res['driverId'];
            $this->order_cache($user['uid'], $phone, $oCache);
            $update = [
                'polling_state' => $res['pollingState'],
                'order_id' => $res['orderId'],
                'driver_id' => $res['driverId'],
                'u_time' => time(),
            ];

            if((int)$res['pollingState'] == 2){
                $update['receive_time'] = time();
            }

            if ((int)$res['pollingState'] == 1) {
                $update['end_time'] = time();
                $update['status'] = '401';
                (new CarCouponAction())->unuseCoupon($oCache['coupon_id']);
            }
            (new CarSubstituteDriving())->myUpdate($update, [
                'id' => $oCache['id']
            ]);
        }
        return $this->json(1, 'success', $res);
    }

    //获得当前订单司机位置及订单信息
    public function actionPosition()
    {
        $request = Yii::$app->request;
        if (!$request->isPost) return '非法访问';
        $data = $request->post();
        $edr = $this->get_edd();
        $fans = $this->fans_account();
        $phone = $fans['mobile'];
        $user = $this->isLogin();
        $token = $edr->get_authen_token($phone);

        if ($token === false) {
            return $this->json(0, '用户授权失败');
        }
        $pollingCount = $data['polling_count'] + 1;
        $res = $edr->driver_position($token, $data['booking_id'], $data['driver_id'], $data['order_id'], $pollingCount);
        if ($res === false) {
            return $this->json(0, '下单失败');
        }
        $oCache = $this->order_cache($user['uid'], $phone);
        $driver = $res['driver'];
        if ($driver['orderStateCode'] != $oCache['orderStateCode']) {
            $state = $oCache['orderStateCode'] = (int)$driver['orderStateCode'];
            $this->order_cache($user['uid'], $phone, $oCache);
            $updata = [
                'drivername' => $driver['name'],
                'driveryear' => $driver['year'],
                'status' => $state,
                'cancel_type' => $driver['cancelType'],
                'u_time' => time(),
            ];
            if (in_array($state, [401, 402, 403, 404, 405, 501])) {
                //订单结束
                $updata['end_time'] = time();
                if (in_array($state, [401, 402, 403, 404, 405])) {
                    (new CarCouponAction())->unuseCoupon($oCache['coupon_id']);
                }
            }
            (new CarSubstituteDriving())->myUpdate($updata, ['id' => $oCache['id']]);
        }
        return $this->json(1, 'success', $res);
    }

    //获得订单费用详情
    public function actionFee()
    {
        $request = Yii::$app->request;
        if (!$request->isPost) return '非法访问';
        $data = $request->post();
        $edr = $this->get_edd();
        $fans = $this->fans_account();
        $phone = $fans['mobile'];
        $user = $this->isLogin();

        $token = $edr->get_authen_token($phone);

        if ($token === false) {
            return $this->json(0, '用户授权失败');
        }
        $res = $edr->orderpay($token, $data['order_id']);
        if ($res === false) {
            return $this->json(0, '下单失败');
        }
        $oCache = $this->order_cache($user['uid'], $phone);
        if (!isset($oCache['amount'])) {
            $updata['amount'] = $oCache['amount'] = $res['income'];
            $updata['cast'] = $oCache['cast'] = $res['cast'];
            $updata['collection_fee'] = json_encode($res['collectionFee'], JSON_UNESCAPED_UNICODE);
            $updata['settle_fee'] = json_encode($res['settleFee'], JSON_UNESCAPED_UNICODE);
            $updata['u_time'] = time();
            (new CarSubstituteDriving())->myUpdate($updata, ['id' => $oCache['id']]);
            $this->order_cache($user['uid'], $phone, $oCache);
        }
        return $this->json(1, 'success', $res);
    }

    public function actionCancel()
    {
        $request = Yii::$app->request;
        if (!$request->isPost) return '非法访问';
        $data = $request->post();
        $edr = $this->get_edd();
        $fans = $this->fans_account();
        $phone = $fans['mobile'];
        $user = $this->isLogin();

        $token = $edr->get_authen_token($phone);

        if ($token === false) {
            return $this->json(0, '用户授权失败');
        }
        $res = $edr->order_cancel($token, $data['booking_id'], 105, '暂时不需要代驾了', $data['order_id']);
        if ($res === false) {
            return $this->json(0, $edr->errMsg);
        }
        $oCahce = $this->order_cache($user['uid'], $phone);
        $update = [
            'status' => 403,
            'end_time' => time(),
            'u_time' => time(),
        ];
        (new CarCouponAction())->unuseCoupon($oCahce['coupon_id']);
        (new CarSubstituteDriving())->myUpdate($update, ['id' => $oCahce['id']]);

        return $this->json(1, 'success');
    }

    protected function main_order($uid, $coupon_id, $amount)
    {
        $obj = new Car_paternalor();
        $order_no = $obj->create_order_no($uid, 'E');
        $data = [
            'order_no' => $order_no,
            'uid' => $uid,
            'type' => 2,
            'coupon_id' => $coupon_id,
            'coupon_amount' => $amount,
            'c_time' => time()
        ];
        $id = $obj->myInsert($data);
        if ($id) {
            $data['id'] = $id;
            return $data;
        }
        return false;
    }

    protected function sub_order($uid, $mobile, $coupon_id, $coupon_sn, $m_id, $orderid, $start, $end, $booking_id, $booking_type,$company_id,$companyid)
    {
        $obj = new CarSubstituteDriving();
        $data = [
            'uid' => $uid,
            'mobile' => $mobile,
            'coupon_id' => $coupon_id,
            'coupon_sn' => $coupon_sn,
            'date_day' => date("Ymd"),
            'date_month' => date("Ym"),
            'm_id' => $m_id,
            'orderid' => $orderid,
            'departure' => $start['address'],
            'start_lat' => $start['lat'],
            'start_lng' => $start['lng'],
            'destination' => $end['address'],
            'end_lat' => $end['lat'],
            'end_lng' => $end['lng'],
            'start_time' => time(),
            'booking_id' => $booking_id,
            'booking_type' => $booking_type,
            'company_id' => 1,
            'companyid' => $companyid,
        ];
        $id = $obj->myInsert($data);
        if ($id) {
            $data['id'] = $id;
            return $data;
        }
        return false;
    }

    public function actionTest(){
        $edr = $this->get_edd();
        $res = $edr->get_authen_token('13875826539');
        var_dump($res);
        var_dump($edr->errMsg);
    }
}