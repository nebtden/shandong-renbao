<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018\9\25
 * Time: 15:13
 */
namespace common\components;
use common\components\DianDian;

class DianDianWash extends DianDian
{
    protected $data;
    public function __construct()
    {
        parent::__construct();
        $this->data = [
            'headers' => [
                'Content-Type' => 'application/json',
                'app_key' => \Yii::$app->params['ddyc_app_key']
            ]
        ];
    }

    /**
     *商家列表
     * @param $pageIndex
     * @param $lat
     * @param $lng
     * @return mixed
     */
    public function offlineNearbyShopList($getdata)
    {
        $url = $this->url . '/offline/nearby/shop/list/';
        $url = $url . '?' . $this->getParam($getdata);
        return $this->urlget($url, $this->data);
    }

    /**
     *商家详情
     * @param $getdata
     * @return mixed
     */
    public function offlineShopDetail($getData)
    {
        $url = $this->url . '/offline/shop/detail';
        $url = $url . '?' . $this->getParam($getData);
        return $this->urlget($url, $this->data);
    }

    /**
     * 创建订单
     * @param $postdata
     * @return mixed
     */
    public function offlineCreateOrder($postdata)
    {
        $url = $this->url . '/offline/create/order';
        $postData = json_encode($postdata);
        $url = $url . '?' . $this->getParam([], $postData);
        $this->data['body'] = $postData;

        return $this->urlpost($url, $this->data);
    }

    /**
     * 取消订单
     * @param $postdata
     * @return mixed
     */
    public function offlineCancelOrder($postdata)
    {
        $url = $this->url . '/offline/cancel/order';
        $postData = json_encode($postdata);
        $url = $url . '?' . $this->getParam([], $postData);
        $this->data['body'] = $postData;

        return $this->urlpost($url, $this->data);
    }

    public function offlineAreaShoplist($postdata)
    {
        $url = $this->url . '/offline/area/shoplist';
        $postData = json_encode($postdata);
        $url = $url . '?' . $this->getParam([], $postData);
        $this->data['body'] = $postData;

        return $this->urlpost($url, $this->data);
    }



}