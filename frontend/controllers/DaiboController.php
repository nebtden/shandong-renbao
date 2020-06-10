<?php

namespace frontend\controllers;

use common\components\CarCouponAction;
use common\components\DiDi;
use common\components\W;
use common\models\CallbackLog;
use common\models\Car_coupon_explain;
use common\models\Car_paternalor;
use common\models\CarCoupon;
use common\models\CarSubstituteDriving;
use Yii;


class DaiboController extends CloudcarController
{
    public $menuActive = 'carhome';
    public $layout = 'cloudcarv2';
    public $site_title = '滴滴代驾';
    protected $uid;
    protected $user;


    private $didi_return = [
        'success' => true,
        'msg' => '',   //我本身增加的信息，便于典典分析
    ];


    //滴滴代驾主入口，每使用一张优惠券，调用一次获取token的操作，代表一次权益
    public function actionIndex(){
        $parms = Yii::$app->params['didi'];
        $channel = $parms['channel'];
        //进来这个页面，首先把优惠券使用掉，如果用掉了，则报错
        $user = $this->isLogin();
        $fans = $this->fans_account();
        $mobile = $fans['mobile'];

        $trans = Yii::$app->db->beginTransaction();

        $request = Yii::$app->request;
        $coupon_id = $request->get('coupon_id', null);

        try {

            //如果有没完成的滴滴订单，需要完成订单，才可以进入下一步
            $model = new CarSubstituteDriving();
            $didi_info = $model->table()->where("company_id = 1 and status in (0,301,302,303) and uid = ".$user['uid'])->one();
            if($didi_info){
                throw new \Exception('您有正在进行中的代驾订单，暂不能发起新的代驾服务。');
            }


            //使用优惠券
            $couponObj = new CarCouponAction($user);
            $coupon = $couponObj->useCoupon($coupon_id);

            if ($coupon === false) {
                throw new \Exception($couponObj->msg);
            }

            //每使用
            $partor = new Car_paternalor();
            $mData = $partor->main_order($user['uid'], $coupon['id'], $coupon['amount'],"E");
            if (!$mData) {
                throw new \Exception('[001]订单提交失败');
            }

            //测试免登录接口
            $aBizParams = [];
            $aBizParams['phone'] =  $mobile;
            $aBizParams['source'] = '12';
            $aBizParams['customerKey'] = $parms['customerKey'];
            $aBizParams['ttid'] = $parms['ttid'];
            //优惠券id 返回类型id
            $aBizParams['privilegeId'] = $coupon['bindid'];
            $aBizParams['outerOrderId'] = $mData['order_no'];


            $kopUtilTest = new DiDi();
            $result = $kopUtilTest->login($aBizParams);

            if(!$result) {
                throw new \Exception('滴滴代驾绑定失败！');
            }

            $url = $this->getUrl($result['token'],$result['pid'],$mobile,$channel);

            //生成订单，把token，pid插入数据库。。
            $obj = new CarSubstituteDriving();
            $data = [
                'uid' => $user['uid'],
                'mobile' => $mobile,
                'coupon_id' => $coupon['id'],
                'coupon_sn' =>  $coupon['coupon_sn'],
                'date_day' => date("Ymd"),
                'date_month' => date("Ym"),
                'm_id' => $mData['id'],
                'orderid' => $mData['order_no'],
                'start_time' => time(),
                'status' => '0',
                'company_id' => '1',  //1 表示滴滴
                'booking_id' => $result['token'],
                'order_id' => $result['pid'],
                'url' => $url,
                'booking_type' => '',
            ];

            $id = $obj->myInsert($data);

            $trans->commit();

            return $this->json(1,'',[],$url);

        } catch (\Exception $e) {
            $trans->rollBack();
            return $this->json(0,$e->getMessage());

        }

    }



    public function actionLoginByTp()
    {

        $request = Yii::$app->request;
        $data = $request->post();

        $fans = $this->fans_account();
        $phone = $fans['mobile'];

        $user = $this->isLogin();

        $uid = $user['uid'];

        $obj = new Car_paternalor();
        $order_no = $obj->create_order_no($uid, 'E');


        if($result){
            return $this->json(1, "ok");
        }else{
            return $this->json(0, "登录失败！");
        }
    }






    public function actionToken(){
        Yii::$app->session['didi_token'] = 'eyJwaWQiOjEzMjYxNjgzNDMsInNvdXJjZSI6MTIsInZhbHVlIjoiMDkxNjkwZWRlMjljOTk2NzYxZTVhNjcwNGEwNDQ1NTIifQ';
        Yii::$app->session['didi_pid'] = '1326168343';

    }

    //获取当前微信地图。。




    private function getUrl($token,$pid,$mobile,$channel){
        $url = \Yii::$app->params['didi']['h5_url'];

        $params = [];
        $params['daijia_pid'] = $pid;
        $params['daijia_token'] = $token;
//        $params['lat'] = '28.215541';
//        $params['lng'] = '112.896809';
        $params['mob'] = $mobile;
        $params['sc'] = $channel;
        $params['c'] = 4;
        $code = http_build_query($params);

        $url = $url.'?'.$code;
        return $url;

    }


    public function actionTest(){


        $token =  Yii::$app->session['didi_token'];
        $pid =  Yii::$app->session['didi_pid'];


        $fans = $this->fans_account();
        $mobile = $fans['mobile'];



    }

    public function actionPublish(){
        $params = [];

        $params['bizType'] = 0;  //0 普通代驾
        $params['Type'] = 0;  //0 普通代驾  1预约单
        $params['startLat'] = 112.954669;   //起点经纬度
        $params['startLng'] = 28.217067;   //yes
        $params['startPoiName'] = '鼎翰大厦';
//        $params['startPoiAddress'] = 0;
        $params['endLat'] = 112.954669;   //起点经纬度
        $params['endLng'] = 30.217067;   //yes
        $params['endPoiName'] = '嘉顺苑';
        $params['channel'] = '4';  //第三方
//        $params['subChannel'] = '4';  //第三方
        $params['publishLat'] = 112.954669;   //发单经纬度，可以跟出发一样
        $params['publishLng'] = 28.217067;   ;  //

        //处理订单，添加到数据，生成id，再更新到逻辑



        $kopUtilTest = new DiDi();
        $result = $kopUtilTest->Publish($params);
        print_r($result);
    }

    public function actionCancel(){
        $params = [];

        $params['oid'] = 111;  //0 普通代驾
        $params['reasonType'] = 11;  //11 默认原因，可直接写这个原因
        $params['when'] = 0;   //0，等待应答页面取消  1，等待接驾取消


        $kopUtilTest = new DiDi();
        $result = $kopUtilTest->Cancel($params);
        print_r($result);
    }


    /**
     * 一般情况下可以不考虑这个原因，直接用默认取消原因，即可
     */
    public function actionCancelReason(){
        $params = [];

        $params['orderId'] = 2222;  //0 普通代驾
        $kopUtilTest = new DiDi();
        $result = $kopUtilTest->CancelReasons($params);
    }

    public function actionOrderDetail(){
        $params = [];

        $params['orderId'] = 2222;  //0 普通代驾
        $kopUtilTest = new DiDi();
        $result = $kopUtilTest->OrderDetail($params);
    }

    public function actionStatus(){
        $params = [];

        $params['oid'] = 2222;  //订单号
        $kopUtilTest = new DiDi();
        $result = $kopUtilTest->Status($params);
    }



    /**
     * 页面类型不一样，导致类型不一致
     */
   public function actionForFeeDetail(){
       $params = [];

       $params['oid'] = 111;  //0 普通代驾
       $params['type'] = 2222;  //类型 1等待页面  2行驶中页面
       $kopUtilTest = new DiDi();
       $result = $kopUtilTest->ForFeeDetail($params);
       print_r($result);

   }

    public function actionCityOpen(){
        $request = Yii::$app->request;
        $data = $request->post();

        $params = [];
        $params['bizType'] = 1;
        $params['lat'] = $data['latitude'];
        $params['lat'] = $data['latitude'];
//       测试的时候，可以使用下面替代
        $params['lat'] = 28.217067;   //yes
        $params['lng'] = 112.954669;   //yes

        $params['source'] = 0;
        $params['phone'] = 13365802535;  //yes

        $kopUtilTest = new DiDi();
        $result = $kopUtilTest->cityOpen($params);
        //之前js逻辑，可能开通，也可能没开通
        if($result){
            return $this->json(1, "ok");
        }else{
            return $this->json(0, "您所在城市还没有开通代驾！");
        }

    }


   public function actionEstimateFee(){
       $params = [];

       $params['slat'] = 112.954669;   //起点经纬度
       $params['slng'] = 28.217067;   //yes
       $params['startPoiName'] = '鼎翰大厦';
//        $params['startPoiAddress'] = 0;
       $params['elat'] = 112.954669;   //起点经纬度
       $params['elng'] = 29.217067;   //yes



       $kopUtilTest = new DiDi();
       $result = $kopUtilTest->EstimateFee($params);
       print_r($result);
   }


   public function actionQueryOrderBill(){
       $params = [];
       $params['oid'] = 112954669;   //订单号

       $kopUtilTest = new DiDi();
       $result = $kopUtilTest->QueryOrderBill($params);
   }

    /**
     * 获取司机状态
     */
   public function actionGetDriverStatusMsg(){
       $params = [];
       $params['oid'] = 112954669;   //订单号

       $kopUtilTest = new DiDi();
       $result = $kopUtilTest->GetDriverStatusMsg($params);
   }








}
