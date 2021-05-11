<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/3/23
 * Time: 15:52
 */


namespace frontend\controllers;

use common\models\CarCouponPackage;
use Yii;
use yii\web\Response;
use common\components\W;
use common\components\NoLogin;
use common\components\AlxgBase;
use common\models\CarApkuser;
use common\models\CarCompany;
use common\models\CarCoupon;
use common\models\CarLifeCode;
use common\models\CarNationalLife;
use frontend\util\PController;
use yii\helpers\Url;


class ApplifeController extends PController
{
    public $CCmodel;
    public $EddrApi;
    private $errdesc = [
        0  => 'SUCCESS',
        10 => '请求数据不能为空',
        11 => '未注册的第三方用户',
        12 => '签名错误',
        13 => '数据生成失败',
        14 => '手机号错误',
        15 => '发码次数已用完，请联系商务！',
        16 => '贵方暂时只合作一次券',
        17 => '缺少关键数据',
        18 => '订单号重复',
    ];
    private $token = 'dhcarcard';

    public $enableCsrfValidation = false;

    public function beforeAction($action = null)
    {
        parent::beforeAction($action);
        Yii::$app->response->format = Response::FORMAT_JSON;
        return true;
    }

    public function actionGetCoupon()
    {
        $request = Yii::$app->request;
        $apk = $request->get('appkey', null);
        $info = $this->info($apk);
        if (!$info)return $this->response(11);
        $secret = $info['secret'];
        $obj = new NoLogin($apk, $secret);
        $data = $obj->reciever();
        if (!$data) return $this->response(10);
        $sign = $data['sign'];
        unset($data['sign']);
        $r = $obj->check_sign($sign, $data);
        if (!$r) return $this->response(12);
        if(! W::is_mobile($data['mobile'])) return $this->response(14);
        $check = (new CarNationalLife())->table()->select('id')->where(['third_orderid'=>$data['thirdOrderId']])->one();
        if($check) return $this->response(18);
        if(!$data['amount'] || !$data['thirdOrderId'] ) return $this->response(17);
        if($info['codemaxnum']-$info['codeusenum'] < 1) return $this->response(15);
        if($data['amount'] != 1 ) return $this->response(16);
        $res  = $this->faquan($info,$data['amount'],$data['mobile'],$data['thirdOrderId']);
        if(!$res) return $this->response(13);

        $noticedata = [
            'packageSn'=>$res['package_sn'],
            'packagePwd'=>$res['package_pwd'],
            'useLimitTime'=>$res['use_limit_time'],
            'useNum'=>$info['codemaxnum']-$info['codeusenum']-1
        ];
        return $this->response(0, 'SUCCESS', $noticedata);
    }

    protected function couponArray($companyid,$amount)
    {
        $tmp = [];
        $coupon_sn = (new CarCoupon()) -> generateCardNoxu(10);
        $now = time();
        $tmp['coupon_type'] = 4;
        $tmp['name'] = '1次洗车服务';
        $tmp['amount'] = $amount;
        $tmp['expire_days'] = $amount*30+15;
        $tmp['c_time'] = $now;
        $tmp['coupon_sn'] = $coupon_sn;
        $tmp['batch_no'] = Yii::$app->params['natLife']['batch_no'];
        $tmp['companyid'] = $companyid;
        $tmp['company'] = 2;
        $tmp['is_mensal']  = 0;
        $tmp['status']  = 0;
        return $tmp;
    }
    protected function packageArray($companyinfo,$coupondata,$amount)
    {

        $model = new CarCouponPackage();
        $str='08'.$companyinfo['id'].'0001';
        $now = time();
        $tmp = [];
        $flowcode = $model->generateCardNo(8, 8);
        $tmp['meal_info']= \GuzzleHttp\json_encode($coupondata,true);
        $sourceNumber = $companyinfo['codeusenum']+1;
        $newNumber = substr(strval($sourceNumber+1000000),1,6);
        $tmp['package_sn'] =$str.$newNumber;
        $tmp['package_pwd'] = $flowcode['package_pwd'];
        $tmp['batch_nb'] = Yii::$app->params['natLife']['batch_nb'];
        $time = $now+($amount+1)*30*86400;
        $end = mktime(23,59,59,date("m",$time),date("d",$time),date("Y",$time));
        $tmp['use_limit_time'] = $end;
        $tmp['c_time'] = $now;
        $tmp['companyid'] = $companyinfo['id'];
        return $tmp;
    }
    protected function faquan($info,$amount,$mobile,$thirdOrderId)
    {
        //[{"amount":"5","type":"1","num":"1","batch_no":"M1GU9C2F"}]
        $coupon = $this->couponArray($info['id'],$amount);
        $meal_info = [
            [
                'amount'=>$coupon['amount'],
                'type'=>$coupon['coupon_type'],
                'num'=>1,
                'batch_no'=>$coupon['batch_no']
            ]
        ];


        $package = $this->packageArray($info,$meal_info,$amount);

        $life_log = [
            'mobile'=>$mobile,
            'coupon_type'=>$coupon['coupon_type'],
            'amount'=>$coupon['amount'],
            'company_id'=>$info['id'],
            'third_orderid'=>$thirdOrderId
        ];

        $db = Yii::$app->db;
        $trans = $db->beginTransaction();
        try{
            $coupon_id  = (new CarCoupon())->myInsert($coupon);
            $package_id = (new CarCouponPackage())->myInsert($package);
            $logid      = (new CarNationalLife)->myInsert($life_log);
            (new CarCompany())->myUpdate(['codeusenum' => ($info['codeusenum']  + 1)],['id' =>$info['id']]);


        }catch (\Exception $e){
            $trans->rollBack();
            return false ;
        }
        $trans->commit();
        return $package;
    }
    protected function info($apk)
    {
        $info = (new CarCompany())->table()->select('*')->where(['appkey'=>$apk])->one();
        return $info;
    }

    protected function response($errno = 0, $errmsg = 'SUCCESS', $data = [])
    {
        $result = [
            'errno' => $errno,
            'errmsg' => $this->errdesc[$errno],
        ];
        if ($data) {
            $result['data'] = $data;
        }
        return $result;
    }

}