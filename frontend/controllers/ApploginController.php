<?php

namespace frontend\controllers;

use common\models\CarApk;
use common\models\CarApkuser;
use Yii;
use frontend\util\PController;
use yii\web\Response;
use common\components\NoLogin;

class ApploginController extends PController
{

    private $errdesc = [
        '11' => '未注册的第三方用户',
        '12' => '签名错误',
        '13' => '数据生成失败'
    ];

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
        //valid apk
        $info = $this->info($apk);
        if (!$info) {
            return $this->response(11, $this->errdesc['11']);
        }
        $secret = $info['secret'];
        $obj = new NoLogin($apk, $secret);
        $data = $obj->reciever();
        $sign = $data['sign'];
        unset($data['sign']);
        $r = $obj->check_sign($sign, $data);
        if (!$r) {
            return $this->response(12, $this->errdesc['12']);
        }
        //判断是否是重复生成
        $is_repeat = $this->check_repeat($info['id'],$data['openid'],$data['mobile']);
        if($is_repeat){
            $key = $is_repeat;
        }else{
            $data['apk_id'] = $info['id'];
            $key = $obj->generate_key($data);
            $insert = $this->add_apk_user($info['id'], $data['openid'], $data['mobile'], $key);
            if (!$insert) {
                return $this->response(13, $this->errdesc['13']);
            }
        }
        return $this->response(0, 'success', ['key' => $key]);
    }

    protected function check_repeat($apk_id,$openid,$mobile){
        $map['apk_id'] = $apk_id;
        $map['openid'] = $openid;
        $map['mobile'] = $mobile;
        $info = (new CarApkuser())->table()->where($map)->one();
        if($info){
            return $info['g_key'];
        }
        return false;
    }

    protected function add_apk_user($apk_id, $openid, $mobile, $key)
    {
        $data = [
            'apk_id' => $apk_id,
            'openid' => $openid,
            'mobile' => $mobile,
            'g_key' => $key,
            'status' => 0,
            'c_time' => time()
        ];
        $r = (new CarApkuser())->myInsert($data);
        return $r;
    }

    protected function info($apk)
    {
        $info = (new CarApk())->info($apk);
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

}