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
class DidiController extends Controller
{

    public function actionIminfo(){
        $e = new EDaiJia();
        $model = new CarSubstituteDriving();
        $list = $model->table()->where("status!=403 and status!=404 and id not in (2,4) and `status`!=501")->all();
        foreach($list as $order_info){
            //查询用户的电话
            $uid = $order_info['uid'];
            $fans_account = FansAccount::find()->where(['uid' => $uid])->one();
            $mobile = $fans_account['mobile'];
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
    }

    /**
     * 订单轮询。。
     */
    public function actionPolling(){
        $e = new EDaiJia();
        $model = new CarSubstituteDriving();
        $list = $model->table()->where("status<401 and polling_state=0  and  id!=52 and  next<".time())->all();
        foreach($list as $order_info){
            $token = $e->get_authen_token($order_info['mobile']);
            $result = $e->polling_info($token,$order_info['booking_id'],$order_info['booking_type'],$order_info['mobile'],$order_info['polling_count']+1);
            if($result['code']==0){
                $update = [];
                $polling_state = $result['data']['pollingState'];
                if($polling_state==1){
                    $update = [
                        'status' => 401,
                        'polling_state' => 1,
                    ];
                }elseif($polling_state==2){
                    $update = [
                       'status' => 101,
                        'polling_state' => 2,
                        'receive_time' => time(),
                    ];
                }elseif($polling_state==0){
                    $update = [
                        'next' => time()+$result['data']['next'],
                        'polling_count' => $order_info['polling_count']+1,
                    ];
                }
                if($update){
                    (new CarSubstituteDriving())->myUpdate($update, ['id' => $order_info['id']]);
                }



            }
        }
    }

    //yong
    public function actionReturncoupon(){
        $action = new CarCouponAction();
        $action->unuseCoupon( 10905);

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