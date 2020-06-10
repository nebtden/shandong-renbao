<?php

namespace frontend\controllers;


use common\components\CarCouponAction;
use common\models\Car_bank_code;
use common\models\CarCouponPackage;
use common\models\Code;
use Yii;
use yii\helpers\Url;
use common\models\CarCoupon;
use common\models\ErrorLog;
use common\components\W;

class Drivingh5Controller extends WebcloudcarController
{
    public $menuActive = 'carhome';
    public $layout = 'webcloudcarv2';

    public function beforeAction($action = null)
    {
        //login页面执行父类beforeAction
         if($this->action->id == 'login'){
             return parent::beforeAction($action);
         }
         return true;

    }

    public function actionIndex()
    {
        $session = Yii::$app->session;
        //获取客户端cookies,如果没有，则重新登录
        $cookies = Yii::$app->request->cookies;
        $p_xxz_mobile = $cookies->getValue('p_xxz_mobile');
        if(!$p_xxz_mobile){
            $user = '';
        }else{
            //获取session,如果没有session,则自动登录
            $user = $session['wx_user_auth_web'];
            if(!$user){
                $this->autoLogin();
                $user = $this->isLogin();
            }
        }
        //代驾
        $drivingCoupon = [];
        if(!empty($user['uid'])){
            $temp = (new CarCoupon())->get_user_bind_coupon_list($user['uid'], 1);
            $now = time();
            foreach ($temp as $cp) {
                if ($cp['status'] == 1 && $now < $cp['use_limit_time']) {
                    $drivingCoupon[] = $cp;
                }
            }
        }
        return $this->renderPartial('index',[
            'user' => $user,
            'drivingCoupon'=>$drivingCoupon,
        ]);
    }

    //调用WEB端登录，登录成功后跳转index页面
    public function actionLogin()
    {
        $url = Yii::$app->request->get('url');
        return $this->redirect(Url::to(['drivingh5/'.$url.'']));
    }

    //国寿代驾服务
    public function actionGsdriving()
    {
        $session = Yii::$app->session;
        $user = $session['wx_user_auth_web'];
        //获取客户端cookies,如果没有，则重新登录
        $cookies = Yii::$app->request->cookies;
        $p_xxz_mobile = $cookies->getValue('p_xxz_mobile');
        if(!$p_xxz_mobile){
            $user = '';
        }else{
            //获取session,如果没有session,则自动登录
            $user = $session['wx_user_auth_web'];
            if(!$user){
                $this->autoLogin();
                $user = $this->isLogin();
            }
        }
        //代驾
        $drivingCoupon = [];
        if(!empty($user['uid'])){
            $temp = (new CarCoupon())->get_user_bind_coupon_list($user['uid'], 1);
            $now = time();
            foreach ($temp as $cp) {
                if ($cp['status'] == 1 && $now < $cp['use_limit_time']) {
                    $drivingCoupon[] = $cp;
                }
            }
        }
        return $this->renderPartial('gsdriving',[
            'user' => $user,
            'drivingCoupon'=>$drivingCoupon,
        ]);
    }

    //国寿洗车服务
    public function actionGswash()
    {
        $session = Yii::$app->session;
        $user = $session['wx_user_auth_web'];
        //获取客户端cookies,如果没有，则重新登录
        $cookies = Yii::$app->request->cookies;
        $p_xxz_mobile = $cookies->getValue('p_xxz_mobile');
        if(!$p_xxz_mobile){
            $user = '';
        }else{
            //获取session,如果没有session,则自动登录
            $user = $session['wx_user_auth_web'];
            if(!$user){
                $this->autoLogin();
                $user = $this->isLogin();
            }
        }
        //洗车
        $washCoupon = [];
        if(!empty($user['uid'])){
            $washCoupon = (new CarCoupon())->get_user_bind_coupon_list($user['uid'], 4);
            $washCoupon = array_filter($washCoupon, function($v, $k) {
                return $v['show_coupon_all'] >0;
            }, ARRAY_FILTER_USE_BOTH);
        }
        return $this->renderPartial('gswash',[
            'user' => $user,
            'washCoupon'=>$washCoupon,
        ]);
    }

    public function actionGsairport()
    {
        return $this->renderPartial('gsairport');
    }

    public function actionRbdriving()
    {
        $session = Yii::$app->session;
        $user = $session['wx_user_auth_web'];
        //获取客户端cookies,如果没有，则重新登录
        $cookies = Yii::$app->request->cookies;
        $p_xxz_mobile = $cookies->getValue('p_xxz_mobile');
        if(!$p_xxz_mobile){
            $user = '';
        }else{
            //获取session,如果没有session,则自动登录
            $user = $session['wx_user_auth_web'];
            if(!$user){
                $this->autoLogin();
                $user = $this->isLogin();
            }
        }
        //代驾
        $drivingCoupon = [];
        if(!empty($user['uid'])){
            $temp = (new CarCoupon())->get_user_bind_coupon_list($user['uid'], 1);
            $now = time();
            foreach ($temp as $cp) {
                if ($cp['status'] == 1 && $now < $cp['use_limit_time']) {
                    $drivingCoupon[] = $cp;
                }
            }
        }
        return $this->renderPartial('rbdriving',[
            'user' => $user,
            'drivingCoupon'=>$drivingCoupon,
        ]);
    }


    //卡券激活
    public function actionAccoupon()
    {
        $request = Yii::$app->request;
        $this->autoLogin();

        if($request->isPost){
            $code = $request->post('code',null);
            $code = trim($code);
            $user = $this->isLogin();
            $codeModle = new Code();
            $time = time();
            $bankCodeObj = new Car_bank_code();
            $packageObj = new CarCouponPackage();

            if(!$code){
                return $this->json(0,'兑换码不能为空');
            }
            $pattern = W::is_renbaocode($code);
            if(!$pattern)return $this->json(0,'兑换码格式不正确');

            if(!$user){
                return $this->json(0,'请登录后重试');
            }
            $list = $bankCodeObj->table()->where(['bank_code' => $code])->one();
            if(!$list){
                $coderes = $codeModle->table()->where(['code' => $code])->one();
                $codedata=[];
                if(!$coderes){
                    $codedata['code']=$code;
                    $codedata['date']=date("YmdH",$time);
                    $codedata['c_time']=$time;
                    $codeModle->myInsert($codedata);
                }
                return $this->json(0,'兑换码尚未录入系统，请稍后再试');
            }
            if($list['status']==2){
                return $this->json(0,'兑换码已兑换');
            }
            //对这个批次中的优惠券，取最近一条记录，进行激活
            $package_info = $packageObj->table()->where(
                [
                    'uid' => 0,
                    'batch_nb' => $list['package_batch_no']
                ]
            )->one();
            if (!$package_info) {
                return $this->json(0,'没有相应的优惠券！');
            }
            try {
                $db = Yii::$app->db;
                $trans = $db->beginTransaction();
                //兑换卡券
                $couponObj = new CarCouponAction($user);
                $result = $couponObj->activateByPackage($package_info);
                if(!$result){
                    throw new \Exception('兑换失败！');
                }
                //存入记录到Car_bank_code表
                $data = [
                    'uid' => $user['uid'],
                    'package_id' => $package_info['id'],
                    'u_time' => time(),
                    'status' => 2
                ];
                $res = $bankCodeObj->myUpdate($data,['id'=>$list['id'],'status'=>1]);
                if(!$res){
                    throw new \Exception('兑换码已兑换，数据更新失败');
                }

                $trans->commit();
            } catch(\Exception $exception){
                $trans->rollBack();
                $error = [
                    'couponType' => 1,
                    'uid' => $user['uid'],
                    'content' => '兑换优惠券失败,错误：' . $exception->getMessage(),
                    'code' => $code
                ];
                $this->log(1,$error);
                return $this->json(0,$exception->getMessage());
            }
                $codeModle->myUpdate(['u_time'=>$time,'status'=>2],['code'=>$code,'status'=>-1]);
                return $this->json(1,'兑换成功');
            }

        return $this->renderPartial('accoupon');
    }

    private function log($type,$error)
    {
        $savePath = Yii::$app->getBasePath() . '/web/log/accoupon/' . date('Y-m') . '/';
        if (!is_dir($savePath)) {
            mkdir($savePath, 0777, true);
            chmod($savePath,0777);
        }
        $f = fopen($savePath . $type . '_' . date('Ymd') . ".txt", 'a+');
        fwrite($f, 'couponType:' . $error['couponType'] . "\n");
        fwrite($f, 'uid:' . $error['uid'] . "\n");
        fwrite($f, 'content:' . $error['content'] . "\n");
        fwrite($f, 'code:' . $error['code'] . "\n");
        fwrite($f, 'c_time:' . date('Y-m-d H:i:s') . "\n");
        fwrite($f, "===========================================\n");
        fclose($f);
    }
}