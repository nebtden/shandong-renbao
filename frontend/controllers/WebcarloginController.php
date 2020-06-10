<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/25 0025
 * Time: 下午 1:47
 */
namespace frontend\controllers;

use Yii;
use yii\helpers\Url;
use common\components\Helpper;
use common\components\W;
use common\components\AlxgBase;
use common\models\CarChengtaiCode;
use common\models\CarPassword;
use common\models\Car_bank_code;
use common\models\CarCouponPackage;

class WebcarloginController extends WebbaseController
{

    public $menuActive = 'caruser';


    public function beforeAction($action = null)
    {

        parent::beforeAction($action);
        return true;
    }

    public function actionLogin(){

        $request = Yii::$app->request;
        if ($request->isPost) {
            $token=Yii::$app->session['token'] ;
            $mobile = $request->post('mobile', null);
            $code = $request->post('code', null);
            $url = $request->post('url',null);
            if(!isset($url)) Yii::$app->session['xxz_url'] = $url;
            if (!$mobile) return $this->json(0, '请输入手机号码！');
            $msg = 'ok';
            if (!Helpper::vali_mobile_code($mobile, $code, $msg)) return $this->json(0, $msg);

            $res = $this ->insertInfo($mobile,$token);
            if(!$res)return $this->json(0 , '系统错误');
            $result = $this ->layupCookie($mobile,'p_xxz_mobile');
            //if(!$result)return $this->json(0 , '系统错误');

            $session = Yii::$app->session;
            $curUrl = $session['xxz_url'] ;
            $session['xxz_mobile']=$mobile;
            if(!$curUrl) $curUrl=Url::to(['webcarhome/index']);
            unset($session['xxz_url']);
            return $this->json(1, '登录成功', [],$curUrl);
        }
        exit('error');
    }
 //客服专享登陆
    public function actionCustomerServiceLogin(){

        $request = Yii::$app->request;
        if ($request->isPost) {
            $mobile = $request->post('mobile', null);
            $password = $request->post('code', null);
            if (!$mobile) return $this->json(0, '请输入手机号码！');
            $fansaccountModel = new AlxgBase('fans_account', 'id');
            $res=$fansaccountModel->table()->select('*')->where(['mobile'=>$mobile,'is_web' => 1])->one();
            if(!$res)return $this->json(0 , '不存在此web端用户');
            $passModel = new CarPassword();
            $passinfo = $passModel->table()->select('*')->where(['password' => $password])->one();
            if(!$passinfo)return $this->json(0 , '无此密码！');
            if($passinfo['status'] == 2)return $this->json(0 , '此密码已禁用！');
            unset(Yii::$app->session['wx_user_auth'],Yii::$app->session['wx_user_auth_web']);
            $result = $this ->layupCookie($mobile,'p_xxz_mobile');
            Yii::$app->session['xxz_mobile']=$mobile;
            $curUrl=Url::to(['webcaruser/coupon']);
            $time = time();
            $db = Yii::$app->db;
            $trans = $db->beginTransaction();
            try{
                $fansaccountModel->myUpdate(['u_time'=>$time],['mobile'=>$mobile,'is_web' => 1]);
                $passModel->myUpdate(['u_time'=>$time],['password' => $password]);
            }catch (\Exception $e){
                $trans->rollBack();
                return false;
            }
            $trans->commit();
            return $this->json(1, '登录成功', [],$curUrl);
        }

        return $this->renderPartial('customer-service-login');
    }

    //储存cookie
    protected function layupCookie($mobile,$cookiename){
        $cookie = new \yii\web\Cookie();
        $cookie -> name = $cookiename;        //cookie的名称
        $cookie -> expire = time() + 3600*24;	   //存活的时间
        $cookie -> httpOnly = true;		   //无法通过js读取cookie
        $cookie -> value = $mobile;	   //cookie的值
        $res = \Yii::$app->response->getCookies()->add($cookie);
        return $res;
    }
    //生成用户数据
    protected function insertInfo($mobile,$token)
    {
        $now = time();
        $data = [
            'mobile'         => $mobile,
            'subscribe_time' => $now,
            'source'         => 'web端用户',
            'token'          => $token
        ];
        $data_ac = [
            'mobile'=>$mobile,
            'c_time'=>$now,
            'u_time'=>$now,
            'is_web'=>'1'
        ];

        $accountModel = new AlxgBase('fans_account', 'id');
        $fansModel = new AlxgBase('fans', 'id');
        $fansinfo=$fansModel->table()->select('*')->where(['mobile'=>$mobile,'source' => 'web端用户', 'token' => $token])->one();
        $uid =  $fansinfo['id'];
        if(!$fansinfo){
            $db = Yii::$app->db;
            $trans = $db->beginTransaction();
            try{
                $id = $fansModel->myInsert($data);
                $data_ac['uid']=$id;
                $uid = $id;
                $accountModel->myInsert($data_ac);

            }catch (\Exception $e){
                $trans->rollBack();
                return false;
            }
            $trans->commit();
        }
        return $uid;
    }
    public function actionChengtailogin(){
        $request = Yii::$app->request;
        $this->site_title = '诚泰洗车';
        if ($request->isPost) {
            $token=Yii::$app->session['token'] ;
            $mobile = $request->post('mobile', null);
            $code = $request->post('code', null);
            $url = $request->post('url',null);
            if(!isset($url)) Yii::$app->session['xxz_url'] = $url;
            if (!$mobile) return $this->json(0, '请输入手机号码！');
            $msg = 'ok';
            if (!Helpper::vali_mobile_code($mobile, $code, $msg)) return $this->json(0, $msg);
            unset(Yii::$app->session['wx_user_auth'],Yii::$app->session['wx_user_auth_web']);
            $res = $this ->insertInfo($mobile,$token);
            if(!$res)return $this->json(0 , '系统错误');
            $result = $this ->layupCookie($mobile,'p_xxz_mobile');
            //if(!$result)return $this->json(0 , $result);
            Yii::$app->session['xxz_mobile']=$mobile;

            $codeModle = new CarChengtaiCode();
            $list = $codeModle->table()->select()->where(['uid' => $res,'status'=>2])->one();
            $curUrl=Url::to(['webcaruser/coupon','footer'=>'hidden']);
            if(!$list) $curUrl=Url::to(['chengtai/accoupon']);
            return $this->json(1,  '登陆成功', [],$curUrl);
        }

        return $this->renderPartial('chengtailogin');
    }

    public function actionCheckuser(){

        $request = Yii::$app->request;
        if ($request->isPost) {
            if (!Yii::$app->session['xxz_mobile']) {
                return $this->json(0, '请您先验证手机');
            } else {
                $curUrl = Url::to(['webcaruser/coupon','footer'=>'hidden']);
                return $this->json(1, '成功', [], $curUrl);
            }
        }
    }


    //获取验证码
    public function actionSmscode(){
        $request = Yii::$app->request;
        if($request->isPost){
            $mobile = $request->post('mobile',null);
            if(!$mobile){
                return $this->json(0,'请输入您的手机号码！');
            }
            $f = W::sendSms($mobile);

            if(!$f)return $this->json(0, '验证码发送失败，请重试！');
            return $this->json(1);
        }
        return '非法访问';
    }
    //获取验证码

    //诚泰首页
    public function actionChengtaiIndex(){

        $url = Url::to(['chengtailogin']);
        return $this->renderPartial('chengtai-index',['url'=>$url,'footer'=>'hidden']);
    }

    /*
     * 人保兑换码添加入库
     * @param $redeemCode兑换码  $password密码验证用
     * @return  $json 成功或失败
     * */
    public function actionOther(){
        $request = Yii::$app->request;
        if($request->isPost){
            $redeemCode = trim($request->post('redeemCode', null));
            $password = $request->post('code', null);
            $is_add = $request->post('is_add');
            if (!$redeemCode || !$password) return $this->json(0, '请输入兑换码或密码！');
            $pattern = W::is_renbaocode($redeemCode);
            if(!$pattern)return $this->json(0,'兑换码格式不正确');

            $codeModel = new Car_bank_code();
            $code = $codeModel->table()->where(['bank_code' => $redeemCode])->one();
            if($is_add == 1){
                if($code)return $this->json(0,'此兑换码已存在，无需再添加！');
            }elseif($is_add == 2){
                if($code){
                    return $this->json(0,'此兑换码已添加进系统，可用于兑换！');
                }else{
                    return $this->json(0,'此兑换码不存在，可添加！');
                }
            }

            $passModel = new CarPassword();
            $passinfo = $passModel->table()->select('*')->where(['password' => $password])->one();
            if(!$passinfo)return $this->json(0 , '无此密码！');
            if($passinfo['status'] == 2)return $this->json(0 , '此密码已禁用！');
            $batch_no=W::createNonceCapitalStr(8);
            $coupon_batch_no = '2G4CSY2Y';
            $company_id=(new CarCouponPackage())->table()->select('companyid')->where(['batch_nb'=>$coupon_batch_no,'status'=>1,'is_redeem'=>1])->one();
            if(empty($coupon_batch_no)){
                return $this->json(0,'不存在此批券包或此批券包已用完');
            }
            //构造数据
            $time = time();
            $datacode['bank_code']=$redeemCode;
            $datacode['package_batch_no']=$coupon_batch_no;
            $datacode['batch_no']=$batch_no;
            $datacode['company_id']=$company_id['companyid'];
            $datacode['c_time']=$time;

            //插入数据库
            $db = Yii::$app->db;
            $trans = $db->beginTransaction();
            try{
                $codeModel->myInsert($datacode);
                $passModel->myUpdate(['u_time'=>$time],['password' => $password]);
            }catch (\Exception $e){
                $trans->rollBack();
                return $this->json(0, '系统繁忙！');
            }
            $trans->commit();
            return $this->json(1, '添加成功');
        }
        return $this->renderPartial('other');
    }

    /**
     * 退出登陆
     *
     **/
    public function actionOutlogin(){
        $request = Yii::$app->request;
        if($request->isPost){

//            $cookie = \Yii::$app->request->cookies;
//            $cookie->remove('p_xxz_mobile');
            unset(Yii::$app->session['wx_user_auth']);
            unset(Yii::$app->session['wx_user_auth_web']);
            unset(Yii::$app->session['xxz_mobile']);
            Yii::$app->session['isshow'] = 'yes';
            $curUrl = Url::to(['webcarhome/index']);
            return $this->json(1, '成功', Yii::$app->session['xxz_mobile'], $curUrl);
        }
    }

}