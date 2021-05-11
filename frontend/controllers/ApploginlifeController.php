<?php
/**
 * 国寿电商对接控制器
 * User: 许雄泽
 * Date: 2019/12/3 0003
 * Time: 上午 10:41
 */

namespace frontend\controllers;

use Yii;
use yii\web\Response;
use common\components\W;
use common\components\NoLogin;
use common\components\AlxgBase;
use common\models\CarApkuser;
use common\models\CarCompany;
use common\models\CarCoupon;
use common\models\CarLifeCode;
use frontend\util\PController;
use yii\helpers\Url;

class ApploginlifeController extends PController
{
    public $CCmodel;
    public $EddrApi;
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
        if (!$info)return $this->response(11, $this->errdesc['11']);
        $secret = $info['secret'];
        $obj = new NoLogin($apk, $secret);
        $data = $obj->reciever();
        if (!$data) return $this->response(10, $this->errdesc['10']);
        $sign = $data['sign'];
        unset($data['sign']);
        $r = $obj->check_sign($sign, $data);
        if (!$r) return $this->response(12, $this->errdesc['12']);
        if(! W::is_mobile($data['mobile'])) return $this->response(14, $this->errdesc['14']);
        if(! $data['type']) return $this->response(17, $this->errdesc['17']);
        $conponModel = new CarCoupon();
        $lifecodeModel = new CarLifeCode();
        if(empty($data['is_online'])){
            return $this->response(17, '缺少必填参数is_online');
        }
        if($data['type'] == 2){
            if(empty($data['activationcode'])){
                return $this->response(17, '缺少必填参数activationcode');
            }
            if(empty($data['card_number'])){
                return $this->response(17, '缺少必填参数card_number');
            }
//            if(empty($data['kalman'])){
//                return $this->response(17, '卡密不能为空');
//            }
            if($data['is_online'] == 2){
                $is_you = $conponModel->table()->select('id')->where(['batch_no' => $data['activationcode'],'companyid'=>$info['id'],'mobile' =>$data['mobile']])->one();
                if($is_you)return $this->response(17, '该手机已兑换，不可重复兑换');
            }
            $codeinfo = $lifecodeModel->table()->select('*')->where(['card_number'=>trim($data['card_number'])])->one();
            if(!$codeinfo){
                return $this->response(17, '不存在该卡');
            }
            if($codeinfo['status'] == 2){
                return $this->response(17, '该卡已兑换，不可重复兑换');
            }
        }

        //判断是否是重复生成
        $is_repeat = $this->check_repeat($info['id'],$data['mobile']);
        $tmp = [];
        //如果有客户信息直接发券（tpy = 2）时
        if($is_repeat){
            $key = $is_repeat['g_key'];
            if($data['type'] == 2 && ! empty($data['activationcode'])){
                $tmp = $this->faquan($conponModel,$info,$data,$is_repeat);
                if(!$tmp) return $this->response(13, $this->errdesc['13']);
            }
        }else{//如果没有客户信息先产生用户再发券（tpy = 2）时
            $data['apk_id'] = $info['id'];
            $key = $obj->generate_key($data);
            $tmp = $this->add_apk_user($info['id'],$key,$data);
            if (!$tmp) return $this->response(13, $this->errdesc['13']);
        }
        $noticedata = [];
        $url = Yii::$app->params['url'];
        if($data['type'] == 1){
            $url .= Url::to(['change','key'=>$key]);
        }elseif($data['type'] == 2){
            $noticedata[0]['name'] = $tmp['name'];
            $noticedata[0]['activeTime'] = $tmp['active_time'];
            $noticedata[0]['useLimitTime'] = $tmp['use_limit_time'];
            $noticedata[0]['couponSn'] = $tmp['coupon_sn'];
            $noticedata[0]['mobile'] = $tmp['mobile'];
            $noticedata[0]['amount'] = $tmp['amount'];
            $url .= Url::to(['couponlist','key'=>$key]);
        }elseif($data['type'] == 3){
            $url .= Url::to(['couponlist','key'=>$key]);
        }elseif($data['type'] == 4){
            $url .= Url::to(['orderlist','key'=>$key]);
        }

        return $this->response(0, 'success', ['url' => $url,'info'=>$noticedata]);
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
    protected function add_apk_user($companyid,$key,$applydata)
    {
        $now = time();
        $token = $this->token;
        $data = [
            'apk_id' => $companyid,
            'mobile' => $applydata['mobile'],
            'g_key' => $key,
            'status' => 0,
            'c_time' => $now
        ];
        $data_fans = [
            'mobile'         => $applydata['mobile'],
            'subscribe_time' => $now,
            'source'         => 'web端用户',
            'token'          => $token
        ];
        $data_ac = [
            'mobile'         => $applydata['mobile'],
            'c_time'         => $now,
            'u_time'         => $now,
            'is_web'         => '1'
        ];
//优惠券
        $tmp = [];
        $conponModel = new CarCoupon();
        if($applydata['type'] == 2 && ! empty($applydata['activationcode']))$tmp = $this->couponArray($conponModel,$companyid,$applydata['mobile'],$applydata['activationcode']);

        $source  = 'web端用户';
        $accountModel = new AlxgBase('fans_account', 'id');
        $fansModel = new AlxgBase('fans', 'id');
        $fansinfo=$fansModel->table()->select('*')->where(['mobile'=>$applydata['mobile'],'source' => $source, 'token' => $token])->one();
        $db = Yii::$app->db;
        $trans = $db->beginTransaction();
        try{
            if(!$fansinfo){
                $id = $fansModel->myInsert($data_fans);
                $data_ac['uid']=$id;
                $accountModel->myInsert($data_ac);
                $data['uid']=$id;
                (new CarApkuser())->myInsert($data);
                if($applydata['type'] == 2 && ! empty($applydata['activationcode']) && ! empty($tmp) ){
                    $tmp['uid'] = $id;
                    $coupon_id = $conponModel->myInsert($tmp);
                    $codedata = $this->lifeCodeData($tmp);
                    $codedata['coupon_id'] = $coupon_id;
                    (new CarLifeCode())->myUpdate($codedata,['card_number'=>$applydata['card_number']]);
                }
            }else{
                $id = $fansinfo['id'];
                $accountinfo = $accountModel->table()->select('id')->where(['uid' => $id])->one();
                if (!$accountinfo) {
                    $data_ac['uid'] = $id;
                    $accountModel->myInsert($data_ac);
                }
                $data['uid'] = $id;
                (new CarApkuser())->myInsert($data);
                if ($applydata['type'] == 2 && !empty($applydata['activationcode']) && !empty($tmp)) {
                    $tmp['uid'] = $id;
                    $coupon_id = $conponModel->myInsert($tmp);
                    $codedata = $this->lifeCodeData($tmp);
                    $codedata['coupon_id'] = $coupon_id;
                    (new CarLifeCode())->myUpdate($codedata,['card_number'=>$applydata['card_number']]);
                }

            }
        }catch (\Exception $e){
            $trans->rollBack();
            return false;
        }
        $trans->commit();
        return $tmp;
    }
    /**
      * 20210127 许雄泽 修改
      * 180天有效期，同时改为非月卡
      *
     **/
    protected function couponArray($model,$companyid,$mobile,$batch)
    {
        $tmp = [];
        $coupon_sn = $model -> generateCardNoxu(10);
        $now = time();
        $tmp['coupon_type'] = 4;
        $tmp['name'] = '2次洗车服务';
        $tmp['amount'] = 2;
        $tmp['expire_days'] = 180;
        $tmp['c_time'] = $now;
        $tmp['coupon_sn'] = $coupon_sn;
        $tmp['mobile'] = $mobile;
        $tmp['coupon_pwd'] = '';
        $tmp['batch_no'] = $batch;
        $tmp['companyid'] = $companyid;
        $tmp['active_time'] = $now;
        $tmp['company'] = 2;
        $tmp['is_mensal']  = 0;
        $tmp['status']  = 1;
        $endDate = $now + $tmp['expire_days']*3600*24;
        $tmp['use_limit_time'] = $endDate;
        //如果是3月1号前激活过期时间就是8月31号
        if($now < 1614528000)$tmp['use_limit_time'] = 1630425599;
        return $tmp;
    }
    protected function lifeCodeData($data)
    {

        $codeData['uid'] = $data['uid'];
        $codeData['coupon_batch_no'] = $data['batch_no'];
        $codeData['u_time'] = $data['active_time'];
        $codeData['status'] = 2;
        return $codeData;
    }
    protected function faquan($conponModel,$info,$data,$is_repeat)
    {
        $tmp = $this->couponArray($conponModel,$info['id'],$data['mobile'],$data['activationcode']);
        $tmp['uid'] = $is_repeat['uid'];
        $codedata = $this->lifeCodeData($tmp);
        $db = Yii::$app->db;
        $trans = $db->beginTransaction();
        try{
            $coupon_id = $conponModel->myInsert($tmp);
            $codedata['coupon_id'] = $coupon_id;
            (new CarLifeCode())->myUpdate($codedata,['card_number'=>trim($data['card_number'])]);
        }catch (\Exception $e){
            $trans->rollBack();
            return false ;
        }
        $trans->commit();
        return $tmp;
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
        $url = Url::to(['webcaruser/accoupon','footer'=>'hidden']);
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
        $url = Url::to(['webcaruser/coupon','footer'=>'hidden']);
        return $this->redirect($url);
    }

    public function actionOrderlist(){
        $request = Yii::$app->request;
        $apk = $request->get('key', null);
        unset(Yii::$app->session['xxz_mobile']);
        $apkuserinfo = (new CarApkuser())->table()->select('*')->where(['g_key'=>$apk])->one();
        if(!$apkuserinfo) exit('非法访问');
        $session = Yii::$app->session;
        $session['xxz_mobile']=$apkuserinfo['mobile'];
        $this->storeInfo($apkuserinfo['mobile']);
        $url = Url::to(['webcaruorder/index','footer'=>'hidden']);
        return $this->redirect($url);
    }
    public function actionLifecode(){
        $request = Yii::$app->request;
        $apk = $request->get('appkey', null);
        $info = $this->info($apk);
        if (!$info)return $this->response(11, $this->errdesc['11']);
        $secret = $info['secret'];
        $obj = new NoLogin($apk, $secret);
        $data = $obj->reciever();
        if (!$data) return $this->response(10, $this->errdesc['10']);
        $sign = $data['sign'];
        unset($data['sign']);
        $r = $obj->check_sign($sign, $data);
        if (!$r) return $this->response(12, $this->errdesc['12']);
        if(empty($data['card_number']) || empty($data['time'])){
            return $this->response(17, $this->errdesc['12']);
        }
        $codeModel = new CarLifeCode();
        $result =  $codeModel->table()->select('id')->where(['card_number'=>$data['card_number']])->one();
        if(!empty($result)){
            return $this->response(17, '此券已存在');
        }
        $dbdata['card_number'] = $data['card_number'];
        $dbdata['batch_no'] = 'XIANSHANG';
        $dbdata['company_id'] = $info['id'];
        $dbdata['c_time'] = time();
        $dbdata['apply_time'] = $data['time'];

        $res = $codeModel->myInsert($dbdata);
        if(!$res)return $this->response(13, $this->errdesc['13']);

        return $this->response(0, 'success');
    }

}