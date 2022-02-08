<?php
/**
 * 聚合油卡充值
 * User: 168
 * Date: 2017/10/23
 * Time: 14:00
 */

namespace console\controllers;


use common\components\Aiqiyi;
use common\components\DianDian;
use common\models\CarCoupon;
use common\models\CarCouponPackage;
use common\models\CarMobile;
use common\models\ErrorLog;
use common\models\FansAccount;
use GuzzleHttp\Client;
use yii\console\Controller;
use Yii;

/**
 * @package console\controllers
 */
class AiqiyiController extends Controller
{
    public $CCmodel;
    public $EddrApi;


    public function actionActive()
    {
        $list = \common\models\Aiqiyi::find()->where([
            'status'=>3,
            'id'=>1042,
        ])->asArray()->all();
        foreach ($list as $value){
            $this->send($value);
            echo $value['id'];
            echo '--------value_id_____';
        }
    }


    private function send($value){
        $aiqiyi_components = new Aiqiyi();

        //$uid = \Yii::$app->session['wx_user_auth']['uid'];
        $coupon =   (new CarCoupon())->table()->where(['id'=>$value['coupon_id'],'uid'=>$value['uid']])->one();
        if(!$coupon){
            throw new \Exception('优惠券不存在，请检查');
        }
        //(new CarCoupon())->myUpdate(['status'=>2],['id'=>$coupon['id']]);
        $bindid = $coupon['bindid'];



        //$order_no = $aiqiyi_params['partnerNo'].'-'.$uid.'-'.time();;
        $model =   \common\models\Aiqiyi::find()->where([
            'id'=>$value['id']
        ])->one();;
        $model->c_time = time();
        $model->save();

        $wakejin_params = \Yii::$app->params['wakejin'];
        $url = $wakejin_params['url'].'/Trade/Index/agentApi';
        $client = new Client();
        $data = [];
        $data['username'] = $wakejin_params['username'];
        $data['secretkey'] = $wakejin_params['secretkey'];
        //每个优惠券的code不一样
        $data['pcode'] = 'recharge_'.$coupon['bindid'];  //  ['recharge_IQYZCHJYK';
        $data['requestid'] = $model->order_no;
        $data['telphone'] =   $model->account;
        $data['recharge_no'] = $model->account;;
        $data['timestamp'] = time();
        $data['msgencrypt'] = '1';
        ksort($data);
        $data["sign"] = md5(implode("", $data));
        $result = $client->request('POST', $url,['form_params' =>$data] );
        $return_data = $result->getBody()->getContents();
        $return_array = \GuzzleHttp\json_decode($return_data,true);

        //写入日志
        (new DianDian())->requestlog($url, \GuzzleHttp\json_encode($data), $return_data, 'aiqiyi', $return_array['code'], 'aiqiyi');


         $model->code = $return_array['code'];
        echo '--code--';
        echo $return_array['code'];
        if(in_array($return_array['code'],['0000'])){
            $model->status = 2;
            $model->startTime = $result['startTime'];
            $model->deadline = $result['deadline'];
            $model->s_time = time();
            //(new CarCoupon())->myUpdate(['status'=>2],['id'=>$coupon['id']]);
        }else{
            $model->status = 3;
        }


        $model->save();
    }



}