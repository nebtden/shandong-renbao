<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018\8\23 0023
 * Time: 15:33
 */
namespace common\components;



class DianDianOilCard extends DianDian {

    public static $facetopay_price =[
        100=>108,
        200=>216,
        300=>324,
        500=>540,
        1000=>1080,
        2000=>2160,
    ];


    public function __construct()
    {
        parent::__construct();
        $this->key = \Yii::$app->params['ddyc_oil_key'] ;
        $this->secret = \Yii::$app->params['ddyc_oil_secret'];
    }



    public function oil_card_list(){
        $url = $this->url.'/oil/card/list';
        $url = $url .'?'.$this->getParam([],'','oil');

        $request_data = [
            'headers' => [
                'Content-Type'     => 'application/json',
                'app_key'      => $this->key
            ],
        ];
        return $this->urlget($url,$request_data);

   }


    public function oil_card_instore($data){
        $url = $this->url.'/oil/card/inStore';
        $url = $url .'?'.$this->getParam($data);


        $request_data = [
            'headers' => [
                'Content-Type'     => 'application/json',
                'app_key'      => $this->key
            ],
        ];

        $result = $this->urlget($url,$request_data);
//        $result =  json_decode($result,true);
        return $result;

    }

    public function oil_recharge_1_1($user_id,$data){
        $url = $this->url.'/oil/recharge/1.1';
        $postData = json_encode($data);
        $url = $url .'?'.$this->getParam([],$postData);

        $token = $this->token($user_id);

        $request_data = [
            'headers' => [
                'Content-Type'     => 'application/json',
                'token'       => $token,
                'app_key'      => $this->key
            ],
            'body' => $postData
        ];
        return $this->urlpost($url,$request_data);
    }

//    public function pay_pre($user_id,$data){
//        $url = $this->url.'pay/trade/pre';
//        $postData = json_encode($data);
//        $url = $url .'?'.$this->getParam([],$postData);
//
//        $token = $this->token($user_id);
//
//        $request_data = [
//            'headers' => [
//                'Content-Type'     => 'application/json',
//                'token'      => $token,
//                'app_key'      => $this->key
//            ],
//            'body' => $postData
//        ];
//        return $this->urlpost($url,$request_data);
//    }
}