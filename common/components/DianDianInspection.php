<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018\8\23 0023
 * Time: 15:33
 */

namespace common\components;

use common\models\CallbackLog;
use yii\log\Logger;

class DianDianInspection extends DianDian
{
    protected $data;
    public static $paytype = [
        'alipay' => 2,
        'kuaiqian' => 5,
        'hebao' => 22,
    ];

    public function __construct($user_id)
    {
        parent::__construct();
        $this->data = [
            'headers' => [
                'Content-Type' => 'application/json',
                'token' => $this->token($user_id),
                'app_key' => \Yii::$app->params['ddyc_app_key']
            ]
        ];
    }

    /**
     * 新增或修改车辆信息
     * @param $postdata
     * @return mixed
     */
    public function vehicleAdd($postdata)
    {
        $url = $this->url . '/inspection/v2/vehicle/replace';
        $postData = json_encode($postdata);
        $url = $url . '?' . $this->getParam([], $postData);
        $this->data['body'] = $postData;
        return $this->urlpost($url, $this->data);
    }

    /**
     * 获取用户绑定所有车辆的年检信息
     * @param $getdata
     * @return mixed
     */
    public function vehicleInspectionGet($getdata = [])
    {
        $url = $this->url . '/inspection/v2/vehicle/list';
        $url = $url . '?' . $this->getParam($getdata);
        return $this->urlget($url, $this->data);
    }

    /**
     * 可用优惠券列表
     * @param $getdata
     * @return mixed
     */
    public function couponInspectionGet($getdata)
    {
        $url = $this->url . '/coupon/inspection/list';
        $url = $url . '?' . $this->getParam($getdata);
        return $this->urlget($url, $this->data);
    }

    /**
     * 兑换券
     * @param $postdata
     * @return mixed
     */
    public function couponExchange($postdata)
    {
        $url = $this->url . '/coupon/doExchange';
        $postData = json_encode($postdata);
        $url = $url . '?' . $this->getParam([], $postData);
        $this->data['body'] = $postData;
        return $this->urlpost($url, $this->data);
    }

    /**
     * 订单列表
     * @param $getdata
     * @return mixed
     */
    public function orderList($getdata)
    {
        $url = $this->url . '/orderCenter/order/list';
        $url = $url . '?' . $this->getParam($getdata);
        return $this->urlget($url, $this->data);
    }

    /**
     * 订单预支付
     * @param $postdata
     * @return mixed
     */
    public function orderPre($postdata)
    {
        $url = $this->url . '/pay/trade/pre';
        $postData = json_encode($postdata);
        $url = $url . '?' . $this->getParam([], $postData);
        $this->data['body'] = $postData;
        return $this->urlpost($url, $this->data);
    }

    /**
     * 支付结果
     * @param $getdata
     * @return mixed
     */
    public function orderPayStatus($getdata)
    {
        $url = $this->url . '/orderCenter/order/status';
        $url = $url . '?' . $this->getParam($getdata);
        return $this->urlget($url, $this->data);
    }

    /**
     * 免检待确认订单
     * @param $getdata
     * @return mixed
     */
    public function inspectionOrderPre($getdata)
    {
        $url = $this->url . '/inspection/v2/order/pre';
        $url = $url . '?' . $this->getParam($getdata);
        return $this->urlget($url, $this->data);
    }

    /**
     * 免检确定订单
     * @param $postdata
     * @return mixed
     */
    public function inspectionOrderCreate($postdata)
    {
        $url = $this->url . '/inspection/v2/order/create';
        $postData = json_encode($postdata);
        $url = $url . '?' . $this->getParam([], $postData);
        $this->data['body'] = $postData;
        return $this->urlpost($url, $this->data);
    }

    /**
     * 免检订单详情
     * @param $getdata
     * @return mixed
     */
    public function inspectionOrderDetail($getdata)
    {
        $url = $this->url . '/inspection/v2/order/detail';
        $url = $url . '?' . $this->getParam($getdata);
        return $this->urlget($url, $this->data);
    }
}