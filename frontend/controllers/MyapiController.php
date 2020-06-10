<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/27 0027
 * Time: 上午 11:21
 */

namespace frontend\controllers;

use Yii;
use common\components\W;
use common\models\CarDemand;
use common\models\CarCompany;
use common\components\NoLogin;
use common\components\BaseController;

class MyapiController extends BaseController {

    /*
     * 第三方发送需求接口
     * 数据 @param  $Appkey我方分给第三方的唯一标识，$packageNum券包数量,$timestamp请求时间,$packageInfo券包信息，$sign签名
     * 返回 @param json $code错误代码，$msg错误信息
     *
     * */

    public function  actionDemand(){


        $response = file_get_contents("php://input");
        error_log($response, 3, APP_PATH . '/demand/' . date('Ymd') . 'demandlog.txt');
        $data = array_filter(json_decode($response, true));
        $sign = $data['sign'];
        $packageInfo=json_encode($data['packageInfo']);
        $Model=new CarDemand();

        //判断参数合法性
        if(empty($data['Appkey'])) return $Model->aderrinfo(10,'缺少必填参数Appkey');
        if(empty($data['packageNum']))return $Model->aderrinfo(10,'缺少必填参数packageNum');
        if(empty($data['timestamp']))return $Model->aderrinfo(10,'缺少必填参数timestamp');
        if(empty($data['packageInfo']))return $Model->aderrinfo(10,'缺少必填参数packageInfo');
        if(empty($data['orderNo']))return $Model->aderrinfo(10,'缺少必填参数orderNo');
        if(strlen($data['Appkey']) != 16)return $Model->aderrinfo(11,'Appkey不合法');
        if(strlen($data['orderNo']) > 32)return $Model->aderrinfo(11,'orderNo不合法');
        $checkno=$Model->table()->select(['id'])->where(['order_no'=>$data['orderNo']])->one();
        if($checkno)return $Model->aderrinfo(11,'orderNo重复');
        if(!is_numeric($data['packageNum']))return $Model->aderrinfo(11,'packageNum不是一个数字');
        if($data['packageNum']>100000 || $data['packageNum']<0 )return $Model->aderrinfo(11,'packageNum不不合法');
        if(strlen($data['packageInfo']) >500)return $Model->aderrinfo(11,'packageInfo字符过长');
        $time=time();
        $companyid=(new CarCompany())->getCompanyOne(['id','secret'],['appkey'=>$data['Appkey']]);
        if(!$companyid)return $Model->aderrinfo(13);
        unset($data['sign'],$data['packageInfo']);
        $obj = new NoLogin('', $companyid['secret']);
        $str = $obj->make_str($data);
        $str.='packageInfo'.$packageInfo;
        $mysign=md5($str);
        //验证签名
        if($mysign != $sign)return $Model->aderrinfo(12);

        //插入数据库
        $dbdata['package_num']=$data['packageNum'];
        $dbdata['package_info']=$packageInfo;
        $dbdata['companyid']=$companyid['id'];
        $dbdata['timestamp']=$data['timestamp'];
        $dbdata['order_no']=$data['orderNo'];
        $dbdata['c_time']=$time;
        $res=$Model->myInsert($dbdata);
        if(!$res)return $Model->aderrinfo(13);
        return $Model->aderrinfo();
    }


}