<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/9/24 0024
 * Time: 下午 5:57
 */

namespace frontend\controllers;

use Yii;
use yii\web\Response;
use common\components\W;
use common\components\NoLogin;
use common\components\AlxgBase;
use common\models\CarApkuser;
use common\models\CarCompany;
use common\models\Fans;
use common\models\FansAccount;
use common\models\CarCouponPackage;
use common\components\CarCouponAction;
use frontend\util\PController;
use yii\helpers\Url;

class ApploginsxgsController extends PController
{

    private $errdesc = [
        '10' => '请求数据不能为空',
        '11' => '未注册的第三方用户',
        '12' => '签名错误',
        '13' => '数据生成失败',
        '14' => '手机号错误',
        '15' => '激活失败',
        '16' => '严重错误',
        '17' => '缺少关键数据',
    ];
    private $token = 'dhcarcard';

    public $enableCsrfValidation = false;

    public function beforeAction($action = null)
    {
        parent::beforeAction($action);
        Yii::$app->response->format = Response::FORMAT_JSON;
        return true;
    }

    public function actionFreelogin()
    {
        $request = Yii::$app->request;
        $apk = $request->get('appkey', null);
        $info = $this->info($apk);
        if (!$info) {
            return $this->response(11, $this->errdesc['11']);
        }
        $secret = $info['secret'];
        $obj = new NoLogin($apk, $secret);
        $data = $obj->reciever();
        if (!$data) {
            return $this->response(10, $this->errdesc['10']);
        }
        $sign = $data['sign'];
        unset($data['sign']);
        $r = $obj->check_sign($sign, $data);

        if (!$r) {
            return $this->response(12, $this->errdesc['12']);
        }
        if(! W::is_mobile($data['mobile'])){
            return $this->response(14, $this->errdesc['14']);
        }
        if(! $data['type']){
            return $this->response(17, $this->errdesc['17']);
        }
        if($data['type'] == 2){
            if(empty($data['activationcode'])){
                return $this->response(17, $this->errdesc['17']);
            }
            $package = (new CarCouponPackage())->table()->select(['id','companyid'])->where(['package_pwd'=>$data['activationcode'],'status'=>1])->one();
            if(empty($package)){
                return $this->response(17, '此兑换码不存在');
            }

        }
        //判断是否是重复生成
        $is_repeat = $this->check_repeat($info['id'],$data['mobile']);
        if($is_repeat){
            $key = $is_repeat['g_key'];
            $uesr = ['uid'=>$is_repeat['uid'],'mobile'=>$is_repeat['mobile']];
        }else{
            $data['apk_id'] = $info['id'];
            $key = $obj->generate_key($data);
            $uesr = $this->add_apk_user($info['id'], $data['mobile'], $key);
            if (!$uesr) {
                return $this->response(13, $this->errdesc['13']);
            }
        }
        $url = Yii::$app->params['url'];
        if($data['type'] == 1){
            $url .= Url::to(['change','key'=>$key]);
        }elseif($data['type'] == 2){
            $res =$this->jihuo($uesr,$data['activationcode']);
            if($res !== true) return $res;
            $url .= Url::to(['couponlist','key'=>$key]);
        }elseif($data['type'] == 3){
            $url .= Url::to(['couponlist','key'=>$key]);
        }

        return $this->response(0, 'success', ['url' => $url]);
    }

    protected function check_repeat($companyid,$mobile){
        $map['apk_id'] = $companyid;
        $map['mobile'] = $mobile;
        $info = (new CarApkuser())->table()->where($map)->one();
        if($info){
            return $info;
        }
        return false;
    }
    /*
     * 产生用户*/
    protected function add_apk_user($companyid, $mobile, $key)
    {
        $now = time();
        $token = $this->token;
        $data = [
            'apk_id' => $companyid,
            'mobile' => $mobile,
            'g_key' => $key,
            'status' => 0,
            'c_time' => time()
        ];
        $data_fans = [
            'mobile'         => $mobile,
            'subscribe_time' => $now,
            'source'         => 'web端用户',
            'token'          => $token
        ];
        $data_ac = [
            'mobile'         => $mobile,
            'c_time'         => $now,
            'u_time'         => $now,
            'is_web'         => '1'
        ];
        $source  = 'web端用户';
        $accountModel = new AlxgBase('fans_account', 'id');
        $fansModel = new AlxgBase('fans', 'id');
        $fansinfo=$fansModel->table()->select('*')->where(['mobile'=>$mobile,'source' => $source, 'token' => $token])->one();
        if(!$fansinfo){
            $db = Yii::$app->db;
            $trans = $db->beginTransaction();
            try{
                $id = $fansModel->myInsert($data_fans);
                $data_ac['uid']=$id;
                $accountModel->myInsert($data_ac);
                $data['uid']=$id;
                (new CarApkuser())->myInsert($data);

            }catch (\Exception $e){
                $trans->rollBack();
                return false;
            }
            $trans->commit();
        }else{
            $id = $fansinfo['id'];
            $data['uid']=$id;
            $result = (new CarApkuser())->myInsert($data);
            if(! $result) return false;
        }

        $res = ['uid'=>$id,'mobile'=>$mobile];
        return $res;
    }

    protected function info($apk)
    {
        $info = (new CarCompany())->table()->select('*')->where(['appkey'=>$apk])->one();
        return $info;
    }

    protected function response($errno = 0, $errmsg = 'success', $data = [])
    {
        $result = [
            'errno' => $errno,
            'errmsg' => $errmsg,
        ];
        if ($data) {
            $result['data'] = $data;
        }
        return $result;
    }
    private function  jihuo($user,$pwd){
        $couponObj = new CarCouponAction($user);
        $coupons = $couponObj->activate($pwd);
        if ($coupons === false) {
            return $this->response(15, $this->errdesc['15']);
        }
        if (empty($coupons)) {
            return $this->response(16, $this->errdesc['16']);
        }
        return true;
    }

    protected function storeInfo($mobile)
    {
        $token = Yii::$app->session['token'];
        $user = (new AlxgBase('fans', 'id'))->table()->select("id,nickname,headimgurl,sex,pid")->where(['mobile'=>$mobile,'source' => 'web端用户', 'token' => $token])->one();
        if ($user) {
            $user_auth = [
                'uid' => $user['id'],
                'nickname' => $mobile,
                'headimgurl' => $user['headimgurl'],
                'mobile' => $mobile,
                'sex' => $user['sex'],
                'pid' => $user['pid']
            ];
            Yii::$app->session['wx_user_auth_web'] = $user_auth;
        }

    }
    public function actionChange(){
        $request = Yii::$app->request;
        $apk = $request->get('key', null);
        unset(Yii::$app->session['xxz_mobile']);
        $apkuserinfo = (new CarApkuser())->table()->select('*')->where(['g_key'=>$apk])->one();
        if(!$apkuserinfo) exit('非法访问');
        $session = Yii::$app->session;
        $session['xxz_mobile']=$apkuserinfo['mobile'];
        $this->storeInfo($apkuserinfo['mobile']);
        $url = Url::to(['webcaruser/accoupon']);
        return $this->redirect($url);


    }
    public function actionCouponlist(){
        $request = Yii::$app->request;
        $apk = $request->get('key', null);
        unset(Yii::$app->session['xxz_mobile']);
        $apkuserinfo = (new CarApkuser())->table()->select('*')->where(['g_key'=>$apk])->one();
        if(!$apkuserinfo) exit('非法访问');
        $session = Yii::$app->session;
        $session['xxz_mobile']=$apkuserinfo['mobile'];
        $this->storeInfo($apkuserinfo['mobile']);
        $url = Url::to(['caruser/coupon']);
        return $this->redirect($url);


    }

}