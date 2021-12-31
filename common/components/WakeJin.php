<?php

namespace common\components;

use common\models\AiqiyiType;
use GuzzleHttp\Client;

//挖金客  爱奇艺对接平台

class WakeJin
{


    public function __construct()
    {


    }


    public function make_sign($data,$key)
    {
        ksort($data);
        $str = http_build_query( $data);
        $str .= $key;
        $sign = md5($str);

        return $sign;
    }

    public function encrypt($input)
    {

    }

    public function decrypt($input)
    {

    }

    public function sendOrder($product_id,$account,$order_no,$amount){
        $aiqiyi_params = \Yii::$app->params['aiqiyi'];
        $url = $aiqiyi_params['url'];
        $data = [];

        $aiqiyi = AiqiyiType::findOne($product_id);

        $data['partnerNo'] = $aiqiyi_params['partnerNo'];
        $data['timestamp'] = time();
        $data['orderNo'] = $order_no;
        $data['mobile'] = $account;
        $data['amount'] = $amount;
        $data['sum'] = $aiqiyi['price']*$amount*100;
        $data['item'] = $product_id;
        $data['sign'] = $this->make_sign($data,$aiqiyi_params['key']);


        $client = new Client();
        $result = $client->request('POST', $url,  ['form_params' =>$data]);
        $return = $result->getBody()->getContents();

        //写入日志
        (new DianDian())->requestlog($url, \GuzzleHttp\json_encode($data), $return, 'aiqiyi', $return['code'], 'aiqiyi');

        return \GuzzleHttp\json_decode($return,true);
    }

}