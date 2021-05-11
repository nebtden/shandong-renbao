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
use common\models\FansAccount;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use yii\helpers\Url;

class CarecarnewController extends CloudcarController
{
    public $layout = "cloudcarv2";
    public $menuActive = 'carhome';

    public function beforeAction($action = null)
    {
        parent::beforeAction($action);
        return true;
    }

    protected function get_edd()
    {
        //$edd = new Eddriving('61000136', '54d94833-9f63-4ad7-93a5-70592d2d0b91', '01051583');
        $edd = new Eddriving();
        //$edd->set_env(true);
        return $edd;
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

        $coupon_model = new CarCoupon();
        if($coupon_id){
            $coupon =  $coupon_model->getCouponById($coupon_id);
            Yii::$app->session->set('driving_company_id', $coupon['company']);
        }else{
            Yii::$app->session->set('driving_company_id',0);
        }


        $company_id = 0;
        $list = (new CarCommonAddress())->get_user_address($user['uid']);
        $coupons = $coupon_model->get_user_ecar_coupon($user['uid'], 1,$company_id);
        $use_text = (new Car_coupon_explain())->get_use_text();
        $is_weixin = $this->is_weixin();
        return $this->render('index', [
            'mobile' => $mobile,
            'common_address' => $list,
            'coupons' => $coupons,
            'use_text' => $use_text,
            'coupon_id' => $coupon_id,
            'alxg_sign' => $alxg_sign,
            'is_weixin' => $is_weixin,
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
         $user = $this->isLogin();
//        $fans = $this->fans_account();
//        $phone = $fans['mobile'];

        $bonus_sn = $data['bonus_sn'];
        $coupon = (new CarCoupon())->table()->where(['coupon_sn' => $bonus_sn,'uid'=>$user['uid']])->one();
        $token = $edr->get_authen_token($coupon['mobile']);

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

    //预定下单
    public function actionReserve()
    {
        $request = Yii::$app->request;
        if (!$request->isPost) return '非法访问';
        $data = $request->post();
        $edr = $this->get_edd();
        $user = $this->isLogin();

        $estimated_cost =   floatval($request->post('fee')) ;

//        $bonus_id = $data['coupon_id'];
//        $coupon_info = (new CarCoupon())->table()->where(['id' => $bonus_id,'uid'=>$user['uid']])->one();


        $trans = Yii::$app->db->beginTransaction();
        try {
            //使用优惠券
            $couponObj = new CarCouponAction($user);
            $coupon = $couponObj->useCoupon($data['coupon_id']);
            $user['mobile'] = $phone =  $coupon['mobile'];
            $token = $edr->get_authen_token($phone);

            if ($token === false) {
                return $this->json(0, '用户授权失败');
            }

            if ($coupon === false) {
                throw new \Exception($couponObj->msg);
            }
            $mData = $this->main_order($user['uid'], $coupon['id'], $coupon['amount']);
            if (!$mData) {
                throw new \Exception('[001]订单提交失败');
            }

            $start = [
                'address' => $data['address'],
                'lat' => $data['lat'],
                'lng' => $data['lng']
            ];
            $end = [
                'address' => $data['daddress'],
                'lat' => $data['dlat'],
                'lng' => $data['dlng']
            ];

            $obj = new CarSubstituteDriving();
            $data = [
                'uid' => $user['uid'],
                'mobile' => $coupon['mobile'],
                'coupon_id' => $data['coupon_id'],
                'coupon_sn' =>  $data['bonus_sn'],
                'date_day' => date("Ymd"),
                'date_month' => date("Ym"),
                'm_id' => $mData['id'],
                'orderid' => $mData['order_no'],
                'departure' => $start['address'],
                'start_lat' => $start['lat'],
                'start_lng' => $start['lng'],
                'destination' => $end['address'],
                'end_lat' => $end['lat'],
                'end_lng' => $end['lng'],
                'start_time' => strtotime($data['begin']),
                'status' => '-1',
                'booking_id' => '',
                'booking_type' => '',
                'companyid' => $coupon['companyid'],
                'estimated_cost' => $estimated_cost,
            ];
            $id = $obj->myInsert($data);

            $trans->commit();
           return $this->json(1, 'ok');
        } catch (\Exception $e) {
            $trans->rollBack();
            return $this->json(0, $e->getMessage());
        }
    }

    //下单
    public function actionPlaceorder()
    {
        $request = Yii::$app->request;
        if (!$request->isPost) return '非法访问';
        $data = $request->post();
        $edr = $this->get_edd();
        $user = $this->isLogin();
        //风险控制  20210106 许雄泽
        $riskmsg = $this->riskManagement($user);
        if($riskmsg['status'] === 1)  return $this->json(0,$riskmsg['msg']);

        $estimated_cost =   floatval($request->post('fee')) ;
        $couponModel = new CarCoupon();
        $coupon = $couponModel->table()->where([
            'id'  => $data['coupon_id'],
            'uid' => $user['uid'],
            'status' => 1,
            ])->one();
        if(!$coupon){
            return '优惠券不存在或者已经使用';
        }
        $token = $edr->get_authen_token($coupon['mobile']);
        $key = $user['uid'].'eddriving_coupon_phone';
        Yii::$app->cache->set($key,$coupon['mobile']);

        if ($token === false) {
            return $this->json(0, '用户授权失败');
        }

        //查询优惠券是否可用；
        $checkres2 = $edr->checkCouponAll($coupon['mobile'],2);
        if(in_array($coupon['coupon_sn'],$checkres2['code']) ){
            $couponModel->myUpdate(['status'=>2,'remark'=>'其它渠道使用','use_time'=>$checkres2['used'][$coupon['coupon_sn']]],['coupon_sn'=>$coupon['coupon_sn']]);
            return $this->json(0, '此券已在其它渠道使用,不可重复使用！');
        }
        $checkres3 = $edr->checkCouponAll($coupon['mobile'],3);
        if(in_array($coupon['coupon_sn'],$checkres3['code'])){
            $couponModel->myUpdate(['status'=>3,'use_limit_time'=>strtotime($checkres3['limitTime'][$coupon['coupon_sn']])],['coupon_sn'=>$coupon['coupon_sn']]);
            return $this->json(0, '卡券已过期');
        }
        
        $trans = Yii::$app->db->beginTransaction();
        try {
            //使用优惠券
            $couponObj = new CarCouponAction($user);

            $coupon = $couponObj->useCoupon($data['coupon_id']);
            $user['mobile'] = $coupon['mobile'];
            $token = $edr->get_authen_token($coupon['mobile']);
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
                    $token = $edr->get_authen_token($coupon['mobile'],true);
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
                'lat' => $data['lat'],
                'lng' => $data['lng']
            ];
            $end = [
                'address' => $data['daddress'],
                'lat' => $data['dlat'],
                'lng' => $data['dlng']
            ];
            $sData = $this->sub_order($user['uid'], $coupon['mobile'], $data['coupon_id'], $data['bonus_sn'], $mData['id'], $mData['order_no'], $start, $end, $res['bookingId'], $res['bookingType'],$estimated_cost,$coupon['companyid']);
            $trans->commit();
            //加入缓存
            $this->order_cache($user['uid'], $coupon['mobile'], ['id' => $sData['id'], 'bookingId' => $res['bookingId'], 'bookingType' => $res['bookingType'], 'coupon_id' => $data['coupon_id']]);
            return $this->json(1, 'ok', $res);
        } catch (\Exception $e) {
            $trans->rollBack();
            return $this->json(0, $e->getMessage());
        }
    }
    /**
     * 风险控制针对代驾订单用户 20210106 许雄泽
     * @param 用户信息  $user
     * @return 风控信息 $riskmsg
     * 风控状态    0 => '派单中',
                101 => '派单中',
                102 => '开始派单',
                201 => '派单中',
                301 => '司机已接单',
                302 => '司机已就位',
                303 => '司机已开车',
                304 => '代驾结束',
                501 => '服务结束',
     **/
    private function riskManagement($user){
        $riskmsg=[
            'status'=> 0,
            'msg' => '未风控'
        ] ;
        $account=(new FansAccount())->select('status',['uid'=>$user['uid']])->one();
        if($account['status'] == 0){
            $riskmsg['status'] = 1;
            $riskmsg['msg'] = '鉴于之前的违规操作，此账号不可核销卡券';
        }
        $daiModel = new CarSubstituteDriving();
        $now = time();
        $map2 = ['between','start_time',strtotime(date('Y-m-d 00:00:00',$now)),strtotime(date('Y-m-d 23:59:59',$now))];
        $riskstatus = [0,101,102,201,301,302,303,304,501];
        $conductobj = $daiModel->table()->where(['uid' => $user['uid']])->andWhere(['status'=>$riskstatus]);
        $conduct = $conductobj->andWhere($map2)->count();
        //每人每天最多呼叫1次代驾，针对分控名单
        if($conduct >= CarSubstituteDriving::DAY_LIMIT && $account['status'] == 2){
            $riskmsg['status'] = 1;
            $riskmsg['msg'] = '您已进入风控名单，每天最多叫'.CarSubstituteDriving::DAY_LIMIT.'次代驾';
        }

        //每人每月最多呼叫10次代驾，针对分控名单
        $mapmonth = ['date_month'=>date('Ym')];
        $mobthconduct = $daiModel->table()
            ->where(['uid' => $user['uid']])
            ->andWhere(['status'=>$riskstatus])
            ->andWhere($mapmonth)
            ->count();
        if($mobthconduct >= CarSubstituteDriving::MONTH_LIMIT  && $account['status'] == 2 ){
            $riskmsg['status'] = 1;
            $riskmsg['msg'] = '您本月呼叫的代驾次数过多，请下月再来！';
        }

        return $riskmsg;
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
        $user = $this->isLogin();


        $coupon = (new CarCoupon())->table()->where(['id' => $data['coupon_id'],'uid'=>$user['uid']])->one();
        $token = $edr->get_authen_token($coupon['mobile']);

//        $token = $edr->get_authen_token($phone);

        if ($token === false) {
            return $this->json(0, '用户授权失败');
        }
        $pollingCount = $data['polling_count'];
        $res = $edr->order_polling($token, $data['booking_id'], $data['booking_type'], $pollingCount);
        if ($res === false) {
            return $this->json(0, '下单失败');
        }
        $oCache = $this->order_cache($user['uid'], $coupon['mobile']);
        if ($res['pollingState'] != $oCache['pollingState']) {
            $oCache['pollingState'] = $res['pollingState'];
            $oCache['orderId'] = $res['orderId'];
            $oCache['driverId'] = $res['driverId'];
            $this->order_cache($user['uid'], $coupon['mobile'], $oCache);
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

        $user = $this->isLogin();

        $order_id = $data['order_id'];
        $order_info = (new CarSubstituteDriving())->table()->where(['order_id' => $order_id,'uid'=>$user['uid']])->one();
        $token = $edr->get_authen_token($order_info['mobile']);
        $phone = $order_info['mobile'];


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
//        $fans = $this->fans_account();
//        $phone = $fans['mobile'];
        $user = $this->isLogin();
        $order_id = $data['order_id'];
        $order_info = (new CarSubstituteDriving())->table()->where(['order_id' => $order_id,'uid'=>$user['uid']])->one();
        $token = $edr->get_authen_token($order_info['mobile']);
        $phone = $order_info['mobile'];


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
        $driving =  new CarSubstituteDriving();


        //根据这里情况，判断是否为预定订单
        if(isset($data['orderid']) and $data['orderid']){
            $driving_info  = $driving->table()->select()->where(['orderid'=>$data['orderid']])->one();
            if($driving_info['status']==-1){
                //表示预定订单取消，直接更改为403即可，取消
//                $oCahce = $this->order_cache($driving_info['uid'], $driving_info['mobile']);
                $update = [
                    'status' => 403,
                    'end_time' => time(),
                    'u_time' => time(),
                ];
                (new CarCouponAction())->unuseCoupon($driving_info['coupon_id']);
                $driving->myUpdate($update, ['id' => $driving_info['id']]);

                return $this->json(1, 'success');
            }
        }
        $edr = $this->get_edd();

        $user = $this->isLogin();
        $order_id = $data['order_id'];
        $order_info = (new CarSubstituteDriving())->table()->where(['order_id' => $order_id,'uid'=>$user['uid']])->one();
        $token = $edr->get_authen_token($order_info['mobile']);
        $phone = $order_info['mobile'];


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
        $driving->myUpdate($update, ['id' => $oCahce['id']]);

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

    protected function sub_order($uid, $mobile, $coupon_id, $coupon_sn, $m_id, $orderid, $start, $end, $booking_id, $booking_type,$estimated_cost=0,$companyid)
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
            'companyid' => $companyid,
            'booking_type' => $booking_type,
            'estimated_cost' => $estimated_cost,
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

    }
}