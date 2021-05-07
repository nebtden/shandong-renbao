<?php
/**
 * Created by PhpStorm.
 * User: 168
 * Date: 2017/10/23
 * Time: 14:00
 */

namespace console\controllers;


use common\components\DianDianOilCard;
use common\models\CarOilor;
use common\models\Redis;
use yii\console\Controller;


/**
 * @package console\controllers
 */
class DiandianController extends Controller
{

    public function actionPolling(){

        $model = new CarOilor();
        $list = $model->table()->where("bizorderid=0 and company_id=2 and status=0")->all();
        $diandian_oil = new DianDianOilCard();
        foreach($list as $order_info){
            try{
                print $order_info['id'];
                print "\n";
                //查询用户的电话
                $data = [];
                $data['cardNumber'] = $order_info['card_no'];
                $data['money'] = intval($order_info['amount']);
                $result = $diandian_oil->oil_recharge_1_1($order_info['uid'], $data);
                if (isset($result['success']) && $result['success'] == true) {
                    $order_id = $result['data']['orderId'];
                    $update = [];
                    $update['bizorderid'] = $order_id;
                    $model->myUpdate($update, ['id' => $order_info['id']]);
                } else {
                    throw new \Exception($result['message']);
                }

                $data = [];
                $data['payMethod'] = 5;
                $data['orderId'] = $order_id;
                $data['payPrice'] = DianDianOilCard::$facetopay_price[intval($order_info['amount'])];
                $result = $diandian_oil->pay_pre($order_info['uid'], $data);
                if (isset($result['success']) && $result['success'] == true) {
                    //更新订单
                    $update = [];
                    $update['status'] = 1;
                    $update['bizorderid'] = $order_id;

                    $model->myUpdate($update, ['id' => $order_info['id']]);

                } else {
                    throw new \Exception($result['message']);
                }
            }catch (\Exception $exception){
                $update = [];
                $update['status'] = -1;


                $model->myUpdate($update, ['id' => $order_info['id']]);
            }

        }
    }


    public function actionReset(){

        $model = new CarOilor();
        $list = $model->table()->where("bizorderid>0 and company_id=2 and status<0")->all();
        $diandian_oil = new DianDianOilCard();
        foreach($list as $order_info){
            try{


                $data = [];
                $data['payMethod'] = 5;
                $data['orderId'] = $order_info['bizorderid'];
                $data['payPrice'] = DianDianOilCard::$facetopay_price[intval($order_info['amount'])];
                $result = $diandian_oil->pay_pre($order_info['uid'], $data);
                if (isset($result['success']) && $result['success'] == true) {
                    //更新订单
                    $update = [];
                    $update['status'] = 1;
//                    $update['bizorderid'] = $order_info['bizorderid'];

                    $model->myUpdate($update, ['id' => $order_info['id']]);

                } else {
                    throw new \Exception($result['message']);
                }
            }catch (\Exception $exception){
                $update = [];
                $update['status'] = $order_info['status']-1;

                $model->myUpdate($update, ['id' => $order_info['id']]);
            }

        }
    }



}