<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018\12\3 0003
 * Time: 13:32
 */

namespace common\components;
use common\models\CarUseraddr;
use Yii;

class CheYiXingInspection extends CheYiXing
{
    protected $data;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 查询支持年审办理城市列表
     */
    public function valiableList($postdata = array())
    {
        $url = $this->url . '/inspectionService/valiableList';
        $pdata = $this->getParam($postdata);
        $respon = $this->urlpost($url, $pdata);
        return $this->decryptData($respon);
    }

    /**
     * 获取年审有效期列表
     * @param string $registerDate 车辆注册时间，日期格式：yyyy-MM-dd
     * @return mixed
     */
    public function getCheckList($carInfo)
    {
        $postdata['registerDate'] = date('Y-m-d', $carInfo['rg_time']);
        $url = $this->url . '/inspectionService/getCheckList';
        $pdata = $this->getParam($postdata);
        $respon = $this->urlpost($url, $pdata);
        $returnData = $this->decryptData($respon);
        (new DianDian())->requestlog($url,json_encode($carInfo),json_encode($returnData),'cxy',$returnData['code'],'cxy_getCheckList');
        return $returnData;
    }

    /**
     * 车辆年检状态判断接口
     * @param array $postdata
     * @return mixed
     */
    public function checkInfo($carInfo = array())
    {
        $postdata = array(
            'carNumber' => $carInfo['card_province'] . $carInfo['card_char'] . $carInfo['card_no'],//车牌号
            'registerDate' => date('Y-m-d', $carInfo['rg_time']),//车辆注册日期，日期格式：yyyy-MM-dd
            'checkDate' =>$carInfo['checkDate'],//年审检验有效期，日期格式：yyyy-MM-dd，日期中的天，必须为月份的最后一天，如：2016-03-31
        );
        $res = Yii::$app->cache->get('checkInfo_'.json_encode($postdata));
        $url = $this->url . '/inspectionService/checkInfo';
        if(!$res || $res['code']){
            $pdata = $this->getParam($postdata);
            $respon = $this->urlpost($url, $pdata);
            $res = $this->decryptData($respon);
            Yii::$app->cache->set('checkInfo_'.json_encode($postdata),$res);
        }
        (new DianDian())->requestlog($url,json_encode($carInfo),json_encode($res),'cxy',$res['code'],'cxy_checkInfo');
        return $res;
    }

    /**
     * 年检报价接口
     * @param array $postdata
     * @return mixed
     */
    public function priceInfo($carInfo = array())
    {
        $carInfoRes = $this->checkInfo($carInfo)['data'];
        $postdata = array(
            'carNumber' => $carInfoRes['carNumber'],//车牌号
            'inspectionType' => $carInfoRes['inspectionType'],//年检类型 1=免检 2=上线检
            'transactType' => $carInfo['transactType'],//年检代办类型：1=免检(自主寄送资料)；2=免检(邮政上门取资料)；3=免检(顺丰上门)4=免检（无需寄送资料）7=上线检（代驾检测）6=上线检（自驾检测）
        );
        $res = Yii::$app->cache->get('priceInfo_'.json_encode($postdata));
        if(!$res || $res['code']){
            $url = $this->url . '/order/priceInfo';
            $pdata = $this->getParam($postdata);
            $respon = $this->urlpost($url, $pdata);
            $res = $this->decryptData($respon);
            Yii::$app->cache->set('priceInfo_'.json_encode($postdata),$res);
        }
        (new DianDian())->requestlog($url,json_encode($carInfo),json_encode($res),'cxy',$res['code'],'cxy_priceInfo');
        return $res;
    }

    /**
     * 年检下单接口
     * @param array $postdata
     * @return mixed
     */
    public function orderPay($postdata = array())
    {
        $url = $this->url . '/order/pay';
        $pdata = $this->getParam($postdata);
        $respon = $this->urlpost($url, $pdata);
        $returnData = $this->decryptData($respon);
        (new DianDian())->requestlog($url,json_encode($postdata),json_encode($returnData),'cxy',$returnData['code'],'cxy_orderPay');
        return $returnData;
    }

    /**
     * 查询订单接口
     * @param array $postdata
     * @return mixed
     */
    public function getOrder($postdata = array())
    {
        $url = $this->url . '/order/getOrder';
        $pdata = $this->getParam($postdata);
        $respon = $this->urlpost($url, $pdata);
        return $this->decryptData($respon);
    }

    /**
     * 上传更新资料图片
     * @param array $postdata
     * @return mixed
     */
    public function updateOrderRequirement($postdata = array())
    {
        $url = $this->url . '/order/updateOrderRequirement';
        $pdata = $this->getParam($postdata);
        $respon = $this->urlpost($url, $pdata);
        return $this->decryptData($respon);
    }

    /**
     * 订单资料确认接口
     * @param array $postdata
     * @return mixed
     */
    public function updateOrderRequirementStatus($postdata = array())
    {
        $url = $this->url . '/order/updateOrderRequirementStatus';
        $this->data['body'] = $this->getParam($postdata);
        return $this->urlpost($url, $this->data);
    }

    /**
     * 确认派单接口
     * 当该年检订单的所有资料都确认后，调用该接口进行最终确认订单为可派单状态
     * @param array $postdata
     * @return mixed
     */
    public function updateOrderConfirmStatus($postdata = array())
    {
        $url = $this->url . '/order/updateOrderConfirmStatus';
        $this->data['body'] = $this->getParam($postdata);
        return $this->urlpost($url, $this->data);
    }

    /**
     * 取消订单接口
     * @param array $postdata
     * @return mixed
     */
    public function cancelOrder($postdata = array())
    {
        $url = $this->url . '/order/cancelOrder';
        $pdata = $this->getParam($postdata);
        $respon = $this->urlpost($url, $pdata);
        return $this->decryptData($respon);
    }

    /**
     * 根据订单号查询退款申请单列表
     * @param  string $orderId
     * @return mixed
     */
    public function queryCancelOrder($orderId)
    {
        $postdata['orderId'] = $orderId;
        $url = $this->url . '/order/queryCancelOrder';
        $this->data['body'] = $this->getParam($postdata);
        return $this->urlpost($url, $this->data);
    }
}