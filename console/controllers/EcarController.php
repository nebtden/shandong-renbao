<?php
/**
 * Created by PhpStorm.
 * User: 168
 * Date: 2017/10/23
 * Time: 14:00
 */

namespace console\controllers;

use common\components\EDaiJia;
use common\components\Eddriving;
use common\models\CarCouponPackage;
use common\models\CarSubstituteDriving;
use common\models\FansAccount;
use common\models\Redis;
use yii\console\Controller;
use common\components\CarCouponAction;
use Yii;

/**

 * @package console\controllers
 */
class EcarController extends Controller
{

    public function actionBooking(){
        $e = new EDaiJia();
        $edr = new Eddriving();

        $model = new CarSubstituteDriving();
        $time = time()+5*60;
        $list = $model->table()->where("driver_id='' and status=-1  and booking_id='' and start_time<$time")->all();
        foreach($list as $order_info){
            $phone = $order_info['mobile'];
            $token = $edr->get_authen_token($phone);
//            $res = $edr->commit_order($token,$data['mobile'], $data['address'], $data['lng'], $data['lat'], $data['bonus_sn'], $data['dlat'], $data['dlng'], $data['daddress']);

            $res = $edr->commit_order($token,$phone, $order_info['departure'], $order_info['start_lng'], $order_info['start_lat'], $order_info['coupon_sn'], $order_info['end_lat'], $order_info['end_lng'], $order_info['destination']);
            if ($res === false) {
                if($edr->errCode == 1){
                    $token = $edr->get_authen_token($phone,true);
                    if($token === false){
                        throw new \Exception('认证失败，请重试');
                    }
                    $res = $edr->commit_order($token,$phone, $order_info['departure'], $order_info['start_lng'], $order_info['start_lat'],$order_info['coupon_sn'], $order_info['end_lat'], $order_info['end_lng'], $order_info['destination']);

                    if($res === false){
                        throw new \Exception($edr->errMsg);
                    }
                }else{
                    throw new \Exception($edr->errMsg);
                }
            }
            $update = [
                'status' => '101',
                'booking_id' => $res['bookingId'],

                'booking_type' => $res['bookingType']
            ];
            (new CarSubstituteDriving())->myUpdate($update, ['id' => $order_info['id']]);

        }
    }




    public function actionIminfo(){
        $e = new EDaiJia();
        $model = new CarSubstituteDriving();
        $list = $model->table()->where("order_id!='' and status!=403 and status!=401 and status!=404 and id not in (2,4) and `status`!=501")->all();
        foreach($list as $order_info){
            $uid = $order_info['uid'];
            $fans_account = FansAccount::find()->where(['uid' => $uid])->one();
            $mobile = $fans_account['mobile'];
            $result = $e->get_order_info($order_info['booking_id'],$mobile,1);
            echo $order_info['id'];
            echo "\n";
            if($result['code']==0){
                if(isset($result['data']['drivers'][0]['orders'])){
                    $orders = $result['data']['drivers'][0]['orders'];
                    if(count($orders)==1){
                        $state = $orders[0]['orderStateCode'];
                        //更新状态
                        $update = [
                            'status' => $state,
                        ];
                        if(isset($orders[0]['dirverId'])){
                            $driverId = $orders[0]['dirverId'];
                            $update['driver_id'] = $driverId;
                        }
                        if(isset($orders[0]['name'])){
                            $drivername = $orders[0]['name'];
                            $update['drivername'] = $drivername;
                        }

                        if (in_array($state, [401, 402, 403, 404, 405, 501])) {
                            //订单结束
                            $update['end_time'] = time();
                            if (in_array($state, [401, 402, 403, 404, 405])) {
                                print_r($order_info['id']);
//                                (new CarCouponAction())->unuseCoupon($order_info['coupon_id']);
                            }
                        }
                        (new CarSubstituteDriving())->myUpdate($update, ['id' => $order_info['id']]);
                    }
                }
            }elseif ($result['code']==1 or $result['code']==5){
                $token = $e->get_authen_token($mobile,true);
                if($token === false){
                    throw new \Exception('认证失败，请重试');
                }
                $result = $e->get_order_info($order_info['booking_id'],$mobile,1);
                if($result['code']==0){
                    if(isset($result['data']['drivers'][0]['orders'])){
                        $orders = $result['data']['drivers'][0]['orders'];
                        if(count($orders)==1){
                            $state = $orders[0]['orderStateCode'];
                            //更新状态
                            $update = [
                                'status' => $state,
                            ];
                            if(isset($orders[0]['dirverId'])){
                                $driverId = $orders[0]['dirverId'];
                                $update['driver_id'] = $driverId;
                            }
                            if(isset($orders[0]['name'])){
                                $drivername = $orders[0]['name'];
                                $update['drivername'] = $drivername;
                            }

                            if (in_array($state, [401, 402, 403, 404, 405, 501])) {
                                //订单结束
                                $update['end_time'] = time();
                                if (in_array($state, [401, 402, 403, 404, 405])) {
                                    print_r($order_info['id']);
//                                (new CarCouponAction())->unuseCoupon($order_info['coupon_id']);
                                }
                            }
                            (new CarSubstituteDriving())->myUpdate($update, ['id' => $order_info['id']]);
                        }
                    }
                }
            }

            //根据司机id，查询信息,更新年龄
            if($order_info['driveryear']==0){
                if(isset($driverId)){

                }else{
                    $driverId = $order_info['driver_id'];
                }
                if($driverId){
                    $result = $e->getDriverInfo($driverId);
                    if($result['code']==0){
                        $update = [];
                       $year = $result['driverInfo']['year'];
                        $update['driveryear'] =$year;
                        (new CarSubstituteDriving())->myUpdate($update, ['id' => $order_info['id']]);
                    }
                }

            }
        }
    }

    /**
     * 订单轮询。。
     */
    public function actionPolling(){
        $e = new EDaiJia();
        $model = new CarSubstituteDriving();
        $list = $model->table()->where("status<401 and polling_state=0  and  id!=52 and  next<".time())->all();
//        $list = $model->table()->where("status=401 and order_id = '' ")->all();

        foreach($list as $order_info){
            $token = $e->get_authen_token($order_info['mobile']);

            $result = $e->polling_info($token,$order_info['booking_id'],$order_info['booking_type'],$order_info['polling_count']+1);
            if($result['code']==0){
                $update = [];
                $polling_state = $result['data']['pollingState'];
                if($polling_state==1){
                    $update = [
                        'status' => 506,
                        'polling_state' => 1,
                    ];
                    if(isset($result['data']['orderId'])){
                        $update['order_id'] = $result['data']['orderId'];
                    }
                }elseif($polling_state==2){
                    $update = [
                        'status' => 101,
                        'polling_state' => 2,
                        'receive_time' => time(),
                    ];
                    if(isset($result['data']['driverId'])){
                        $update['driver_id'] = $result['data']['driverId'];
                    }
                    if(isset($result['data']['orderId'])){
                        $update['order_id'] = $result['data']['orderId'];
                    }
                }elseif($polling_state==0){
                    if($result['data']['timeout']==270 && date('Ymd',time()-3600*24)>$order_info['date_day']){
//                        $update  = [
//                            'status'=>401
//                        ];
                        if(isset($result['data']['orderId'])){
                            $update['order_id'] = $result['data']['orderId'];
                        }
                    }else{
                        $update = [
                            'next' => time()+$result['data']['next'],
                            'polling_count' => $order_info['polling_count']+1,
                        ];
                    }

                }
                if($update){
                    (new CarSubstituteDriving())->myUpdate($update, ['id' => $order_info['id']]);
                }
                //考虑没有获取司机年龄
                if(!$order_info['driveryear']){

                }
                print($order_info['id']);
                print("\n");


            }elseif ($result['code']==1 or $result['code']==5){
                $token = $e->get_authen_token($order_info['mobile'],true);
                if($token === false){
                    throw new \Exception('认证失败，请重试');
                }
            }
        }
    }

    //用户优惠券退回
    public function actionReturncoupon(){
        $action = new CarCouponAction();
        $action->unuseCoupon( 10905);

    }


    public function actionOrderDetail(){
        $e = new EDaiJia();
        $model = new CarSubstituteDriving();
//        $list = $model->table()->where("id  in (279,532,538,700,726,733,764,765,824,842,863,866,871,888,899,903,907,1008,1092,1120,1187,1231,1278,1298,1301,1416,1417,1506,1566,1612,1638)")->all();
        $list = $model->table()->where("status  in (101) ")->all();


        foreach($list as $order_info){
            $update = [];
            $uid = $order_info['uid'];
            $fans_account = FansAccount::find()->where(['uid' => $uid])->one();
           $mobile = $fans_account['mobile'];
           $token = $e->get_authen_token($mobile);
            $result = $e->get_order_detail($token,$order_info['order_id']);
            echo $order_info['id'];
            echo "\n";
            if($result['code']==0){

                if($result['data']['status']==1 ){
                       $update['status'] = 501;
                }elseif($result['data']['status']==2){
                    $update['status'] = 404;
                } elseif($result['data']['status']==6){
                    $update['status'] = 402;
                }elseif($result['data']['status']==7){
                    $update['status'] = 506;
                }
//                if($order_info['driveryear']==0 && $result['data']['status']){
//                    $driverId = $result['data']['driverId'];
//                    $update['driver_id'] =$driverId;
//                    if($driverId){
//                        $result = $e->getDriverInfo($driverId);
//                        if($result['code']==0){
//
//                            $year = $result['driverInfo']['year'];
//                            $name = $result['driverInfo']['name'];
//                            $update['driveryear'] =$year;
//                            $update['drivername'] =$name;
//
//                        }
//                    }
//                }
            }


            if($update){
                (new CarSubstituteDriving())->myUpdate($update, ['id' => $order_info['id']]);
            }

        }
    }


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


    public function actionUpdateU(){

    }




    public function actionUpdate()
    {
        $CCPmodel = new CarCouponPackage();
        //更新模型
        $now = time();

        $phone = '19965829738';
        $coupon_sn = '309566893613';
        $package_id = '3404';


        $user = FansAccount::find()->where([
            'mobile'=>$phone
        ])->one();


        $r = $this->getpost($coupon_sn,$phone);
      //  $r = \GuzzleHttp\json_decode();


        $EddrApi = new Eddriving();

        $val['bindsn'] = $coupon_sn;
        $val['active_time'] = $now;
        $val['uid'] = $user['uid'];
        $val['bindid'] = $r['bindid'];
        $val['bonusid'] = $r['bonusid'];
        $val['mobile'] = $phone;
        $val['status'] = 1;//将卡券改为已激活状态
        //这里主要获得过期时间
        $r = $EddrApi->coupon_allinfo($coupon_sn, $phone);
        if ($r === false) {
            //没有查到的情况下，默认过期时间为绑定后20天
            $expire = 0;
            if (!$val['expire_days']) $val['expire_days'] = 20;
            $val['use_limit_time'] = $now + $val['expire_days'] * 24 * 3600;
        } else {
            $val['bindsn'] = $r['bind_info']['sn'];
            $val['use_limit_time'] = strtotime($r['endDate']);
        }
        $val['package_id'] = $package_id;
        $r = $CCPmodel->myUpdate($val);
        print_r($r);


    }

    private function getpost($coupon_sn,$phone){
        $EddrApi = new Eddriving();
        $r = $EddrApi->coupon_bind($coupon_sn, $phone);
        if ($r === false) {
            echo 'fail';
        }else{
            return $r;
        }
    }


    private function updateCoupon(){

    }


}