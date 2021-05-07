<?php

namespace common\components;

use Yii;

class WxPay
{
    /**
     * 微信js发起支付
     * @param $data
     * @desc data包含参数 openid,body,attach,orderno,total_fee,goods_tag,notify_url
     * @return string
     * @throws \WxPayException
     */
    public static function WxJsApi($data){
        require_once './../../vendor/WxPayV3/WxPay.JsApiPay.php';
        $jsApi = new \JsApiPay();
        $openid = $data['openid'];

        $input = new \WxPayUnifiedOrder();
        $input->SetBody($data['body']);
        if(isset($data['attach'])){
            $input->SetAttach($data['attach']);
        }
        $input->SetOut_trade_no($data['orderno']);
        $input->SetTotal_fee(intval($data['total_fee']*100));

        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 600));
        if(isset($data['goods_tag'])){
            $input->SetGoods_tag($data['goods_tag']);
        }
        $input->SetNotify_url($data['notify_url']);
        $input->SetTrade_type("JSAPI");
        $input->SetOpenid($openid);
        $prepay_id = \WxPayApi::unifiedOrder($input);
        $jsApiParameters = $jsApi->GetJsApiParameters($prepay_id);
        return $jsApiParameters;
    }
}