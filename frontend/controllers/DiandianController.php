<?php

namespace frontend\controllers;

use common\components\CarCouponAction;
use common\components\CheYiXing;
use common\components\DianDian;
use common\components\DianDianOilCard;
use common\components\DianDianWash;
use common\components\W;
use common\models\CallbackLog;
use common\models\CarCoupon;
use common\models\CarInsorder;
use common\models\CarOilor;
use common\models\CarSegorder;
use common\models\WashOrder;
use frontend\util\PController;
use Yii;
use common\models\FansAccount;


class DiandianController extends PController
{
    private $diandian_return = [
        'success' => true,
        'msg' => '',   //我本身增加的信息，便于典典分析
    ];

    //记录回调日志
    private function logold($type)
    {
        $newlog = new CallbackLog();
        $newlog->post_body = yii::$app->getRequest()->getRawBody();
        $newlog->url = Yii::$app->request->queryString;
        $newlog->input = '';
        $newlog->type = CallbackLog::$type[$type];
        $newlog->company = 'diandian';
        $newlog->status = intval($this->diandian_return['success']);
        $newlog->return = json_encode($this->diandian_return);
        $newlog->c_time = time();
        $newlog->save();
    }

    private function log($type)
    {
        $savePath = Yii::$app->getBasePath() . '/web/log/diandian/' . date('Y-m') . '/';
        if (!is_dir($savePath)) {
            mkdir($savePath, 0777, true);
            chmod($savePath,0777);
        }
        $f = fopen($savePath . $type . '_' . date('Ymd') . ".txt", 'a+');
        fwrite($f, 'post_body:' . yii::$app->getRequest()->getRawBody() . "\n");
        fwrite($f, 'url:' . Yii::$app->request->queryString . "\n");
        fwrite($f, 'status:' . $this->diandian_return['success'] . "\n");
        fwrite($f, 'return:' . json_encode($this->diandian_return,JSON_UNESCAPED_UNICODE) . "\n");
        fwrite($f, 'c_time:' . date('Y-m-d H:i:s') . "\n");
        fwrite($f, "===========================================\n");
        fclose($f);
    }

    public function actionOilOrderStatus()
    {
        $baseurl = Yii::$app->request->queryString;
        //对baseurl进行处理，把&符号前面的去掉
        $params = strstr($baseurl, "&");
        $params = substr($params, 1);
        $post_data = yii::$app->getRequest()->getRawBody();
        try {
            $diandian = new DianDianOilCard();
            $result = $diandian->checkToken($params, $post_data);
            if (!$result) {
                throw new \Exception('token 验证失败');
                //处理相关接口相关逻辑
            }
            $data = json_decode($post_data, true);
            if (!isset($data['success']) && $data['success']) {
                throw new \Exception('success  参数不正确');
            }
            $order_id = $data['data']['orderId'];
            $order_status = $data['data']['orderStatus'];
            $time = $data['data']['modifyTime'];
            $oilor = new CarOilor();
            $order = $oilor->table()->where(['company_id' => CarOilor::$oil_company['diandian'], 'bizorderid' => $order_id])->one();
            if (!$order) {
                throw new \Exception('订单不存在');
            }
            //更改状态
            $order['status'] = DianDian::$order_status[$order_status];
//            if($order['status']==1){
//                $fans_account = FansAccount::find()->where([
//                        'uid' => $order['uid']
//                 ])->one();
//
//                $content =  \Yii::$app->params['oil_append_success'];
//                $mobile = $fans_account['mobile'];
//                if($mobile){
//                    W::sendSms($mobile,$content);
//                }
//            }


            if ($order['status'] == 2) {
//                $order['s_time'] = time();
            }
            if ($order['status'] == 3) {

                $update = [];
                $update['company'] = 1;
                $model= new CarCoupon();
                $model->myUpdate($update, ['id' => $order['coupon_id']]);

                //恢复优惠券
                $r = (new CarCouponAction())->unuseCoupon($order['coupon_id']);
                if ($r === false) {
                    throw new \Exception('优惠券恢复失败');
                }
            }
            $r = $oilor->myUpdate($order);
            if ($r === false) {
                throw new \Exception('订单状态更新失败');
            }
        } catch (\Exception $e) {
            $this->diandian_return['success'] = false;
            //这个字段为我添加
            $this->diandian_return['msg'] = $e->getMessage();
        }
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $this->log('oil');
        return $this->diandian_return;

    }

    public function actionOilPayStatus()
    {
        $baseurl = Yii::$app->request->queryString;
        //对baseurl进行处理，把&符号前面的去掉
        $params = strstr($baseurl, "&");
        $params = substr($params, 1);

        $post_data = yii::$app->getRequest()->getRawBody();
        try {
            $diandian = new DianDianOilCard();
            $result = $diandian->checkToken($params, $post_data);

            if (!$result) {
                throw new \Exception('token 验证失败');
                //处理相关接口相关逻辑
            }
            $data = json_decode($post_data, true);
            if (!isset($data['success']) && $data['success']) {
                throw new \Exception('success  参数不正确');
            }
            $order_id = $data['data']['orderId'];
            $status = $data['data']['status'];
            $time = $data['data']['modifyTime'];

            $oilor = new CarOilor();
            $order = $oilor->table()->where(['company_id' => CarOilor::$oil_company['diandian'], 'bizorderid' => $order_id])->one();

            if (!$order) {
                throw new \Exception('订单不存在');
            }

            //更改状态
            if ($status == 'SUCCESS') {
                $order['status'] = 2;
                $order['s_time'] = time();
            } else {
                $order['status'] = 3;
                //恢复优惠券
                $r = (new CarCouponAction())->unuseCoupon($order['coupon_id']);
                if ($r === false) {
                    throw new \Exception('优惠券恢复失败');
                }
            }

            $r = $oilor->myUpdate($order);

            //没有放到上面是因为，发送短信可能延迟，故更改状态后，再发送
            if ($status == 'SUCCESS') {
                $fans_account = FansAccount::find()->where([
                    'uid' => $order['uid']
                ])->one();

                $content = \Yii::$app->params['oil_pay_success'];
                $mobile = $fans_account['mobile'];
                $content = str_replace("amount", $order['amount'], $content);
                if ($mobile) {
                    W::sendSms($mobile, $content);
                }
            }
            if ($r === false) {
                throw new \Exception('订单状态更新失败');
            }

        } catch (\Exception $e) {
            $this->diandian_return['success'] = false;
            //这个字段为我添加
            $this->diandian_return['msg'] = $e->getMessage();
        }
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $this->log('oil');
        return $this->diandian_return;
    }

    public function actionSegwayStatus()
    {
        $time = $_SERVER['REQUEST_TIME'];
        $resArr = array(
            'code' => 0,
            'errormsg' => '成功'
        );
        $post_data = yii::$app->getRequest()->getRawBody();
        $post_data = json_decode($post_data, JSON_UNESCAPED_UNICODE);
        try {
            if (!isset($post_data['externalTerminalOrderNo'])||!isset($post_data['orderState'])){
                throw new \Exception('访问出错');
            }
            $orderid = $post_data['externalTerminalOrderNo'];
            $order_status = CarSegorder::$sd_order_to_local[$post_data['orderState']];
            $segorder = new CarSegorder();
            $order = $segorder->table()->where(['orderid' => $orderid])->one();
//            $isSendMsg = $order['sendmsg'];//存储“是否发送过短信”
            if (!$order) {
                throw new \Exception('订单不存在');
            }
            //更改状态
//            $orderstatus = CarSegorder::$orderStatusText[$post_data['orderState']];
            if ($order_status == ORDER_SUCCESS) {
                $order['s_time'] = $time;
            }
            if ($order_status == ORDER_SURE) {
                $order['r_time'] = $time;
            }
            $order['status'] = $order_status;
            if (isset($post_data['reservationTime']) && $post_data['reservationTime']){
                $order['pre_u_time'] = strtotime($post_data['reservationTime']);
            }
            if (isset($post_data['customerPhone']) && $post_data['customerPhone']){
                $order['telphone'] = $post_data['customerPhone'];
            }
            if (isset($post_data['secondarySupplierCode']) && $post_data['secondarySupplierCode']){
                $order['prestorecode'] = $post_data['secondarySupplierCode'];
            }
            if (isset($post_data['endDate']) && $post_data['endDate']){
                $order['pre_r_time'] = strtotime($post_data['endDate']);
            }
//            $order['sendmsg'] = 1;
            $r = $segorder->myUpdate($order);
            if ($r === false) {
                throw new \Exception('订单状态更新失败');
            }
//            //订单正常返回才发送给客户短信提示start
//            $fans_account = FansAccount::find()->where(['uid' => $order['uid']])->one();
//            $content = \Yii::$app->params['ins_finish_order'];
//            $mobile = $fans_account['mobile'];
//            $content = str_replace("orderstatus", $orderstatus, $content);
//            //没有发送过短信并且存在手机号时，发送短信
//            if ($isSendMsg == 0 && $mobile) {
//                W::sendSms($mobile, $content);
//            }
//            //end

        } catch (\Exception $e) {
            $resArr['code'] = -2;
            $resArr['errormsg'] = $e->getMessage();
        }
        $this->diandian_return['success'] = $resArr['code'];
        $this->diandian_return['msg'] = $resArr['errormsg'];
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $this->log('segway');
        return $resArr;
    }

    /**
     * 车行易年检订单状态变更通知回调
     * @return array
     */
    public function actionCxyorderstatus()
    {
//        $my_sign = strtoupper(md5('TEST02190304000036'.'2'.'8'.'dinghantest'));
//        var_dump($my_sign);exit;
        $resArr = array(
            'code'=>0,
            'errormsg'=>'成功'
        );
        $post_data = yii::$app->getRequest()->getRawBody();
        $post_data = json_decode($post_data,JSON_UNESCAPED_UNICODE);
        try {
            $cyx = new CheYiXing();
            $result = $cyx->checkSign($post_data);
            if (!$result) {
                throw new \Exception('token 验证失败');
                //处理相关接口相关逻辑
            }
            $order_id = $post_data['orderId'];
            $order_status = CheYiXing::$orderStatus[$post_data['orderStatus']];
            $insorder = new CarInsorder();
            $order = $insorder->table()->where(['company_id' => COMPANY_CHEXINGYI, 'bizorderid' => $order_id])->one();
            $isSendMsg = $order['sendmsg'];//存储“是否发送过短信”
            if (!$order) {
                throw new \Exception('订单不存在');
            }
            //更改状态
            if ($order_status == ORDER_SUCCESS || $order_status == ORDER_FAIL) {
                if ($order_status == ORDER_SUCCESS) {
                    $orderstatus = '成功';
                } elseif ($order_status == ORDER_FAIL) {
                    $orderstatus = '失败';
                }
                $order['s_time'] = time();
                $order['status'] = $order_status;
                $order['sendmsg'] = 1;
                $r = $insorder->myUpdate($order);
                if ($r === false) {
                    throw new \Exception('订单状态更新失败');
                }
                //订单正常返回才发送给客户短信提示start
                $fans_account = FansAccount::find()->where(['uid' => $order['uid']])->one();
                $content = \Yii::$app->params['ins_finish_order'];
                $mobile = $fans_account['mobile'];
                $content = str_replace("orderstatus", $orderstatus, $content);
                //没有发送过短信并且存在手机号时，发送短信
                if ($isSendMsg == 0 && $mobile) {
                    W::sendSms($mobile, $content);
                }
                //end
            }
        } catch (\Exception $e) {
            $resArr['code'] = -2;
            $resArr['errormsg'] = $e->getMessage();
        }
        $this->diandian_return['success']=$resArr['code'];
        $this->diandian_return['msg']=$resArr['errormsg'];
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $this->log('cxyinspection');
        return $resArr;
    }

    /**
     * 年检订单状态变更通知回调
     * @return array
     */
    public function actionInsOrderStatus()
    {
        $baseurl = Yii::$app->request->queryString;
        //对baseurl进行处理，把&符号前面的去掉
        $params = strstr($baseurl, "&");
        $params = substr($params, 1);
        $post_data = yii::$app->getRequest()->getRawBody();
        try {
            $diandian = new DianDian();
            $result = $diandian->checkToken($params, $post_data);
            if (!$result) {
                throw new \Exception('token 验证失败');
                //处理相关接口相关逻辑
            }
            $data = json_decode($post_data, true);
            if (!isset($data['success']) && $data['success']) {
                throw new \Exception('success  参数不正确');
            }
            $order_id = $data['data']['orderId'];
            $order_status = $data['data']['orderStatus'];
            $time = $data['data']['modifyTime'];
            $insorder = new CarInsorder();
            $order = $insorder->table()->where(['company_id' => COMPANY_DIANDIAN, 'bizorderid' => $order_id])->one();
            $isSendMsg = $order['sendmsg'];//存储“是否发送过短信”
            if (!$order) {
                throw new \Exception('订单不存在');
            }
            //更改状态
            if ($order_status == 3 || $order_status == 4) {
                if ($order_status == 3) {
                    $orderstatus = '成功';
                    $order['status'] = ORDER_SUCCESS;
                } elseif ($order_status == 4) {
                    $orderstatus = '失败';
                    $order['status'] = ORDER_FAIL;
                }
                $order['s_time'] = time();
                $order['sendmsg'] = 1;
                $r = $insorder->myUpdate($order);
                if ($r === false) {
                    throw new \Exception('订单状态更新失败');
                }
                //订单正常返回才发送给客户短信提示start
                $fans_account = FansAccount::find()->where(['uid' => $order['uid']])->one();
                $content = \Yii::$app->params['ins_finish_order'];
                $mobile = $fans_account['mobile'];
                $content = str_replace("orderstatus", $orderstatus, $content);
                //没有发送过短信并且存在手机号时，发送短信
                if ($isSendMsg == 0 && $mobile) {
                    W::sendSms($mobile, $content);
                }
                //end
            }
        } catch (\Exception $e) {
            $this->diandian_return['success'] = false;
            //这个字段为我添加
            $this->diandian_return['msg'] = $e->getMessage();
        }
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $this->log('inspection');
        return $this->diandian_return;
    }

    /**
     * 典典洗车核销回调处理
     * @return array
     */
    public function actionWashorderstatus()
    {
        $baseurl = Yii::$app->request->queryString;
        //对baseurl进行处理，把&符号前面的去掉
        $params = strstr($baseurl, "&");
        $params = substr($params, 1);
        $post_data = yii::$app->getRequest()->getRawBody();
        try {
            $diandian = new DianDian();
            $result = $diandian->checkToken($params, $post_data);
            if (!$result) {
                throw new \Exception('token 验证失败');
             //处理相关接口相关逻辑
            }
            $data = json_decode($post_data, true);
            if (!isset($data['success']) && $data['success']) {
                throw new \Exception('success  参数不正确');
            }
            $consumerCode = $data['data']['consumerCode'];
            $time = strtotime($data['data']['eventTime']);
            $outOrderNo = $data['data']['outOrderNo'];
            $orderObj = new WashOrder();
            $order = $orderObj->table()->select()->where(['consumerCode' => $consumerCode, 'status' => ORDER_HANDLING])->one();
            if (!$order) {
                throw new \Exception('订单不存在');
            }
            if($order['status'] == ORDER_SUCCESS){
                throw new \Exception('服务码已经核销过');
            }
            //变更洗车订单状态
            $order['status'] = ORDER_SUCCESS;
            $order['s_time'] = $time;
            $res = $orderObj->myUpdate($order);
            if (!$res) {
                throw new \Exception('订单写入失败');
            }
        } catch (\Exception $e) {
            $this->diandian_return['success'] = false;
            $this->diandian_return['msg'] = $e->getMessage();
        }
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $this->log('wash');
        return $this->diandian_return;
    }


    public function actionChargeCoupon()
    {
        $baseurl = Yii::$app->request->queryString;
        //对baseurl进行处理，把&符号前面的去掉
        $params = strstr($baseurl, "&");
        $params = substr($params, 1);
        parse_str($params, $query);
        $mysign = $query['sign'];

        $post_data = yii::$app->getRequest()->getRawBody();
        $postData = json_decode($post_data, true);
        ksort($postData);
        $str = '';
        foreach ($postData as $k => $v) {
            $str .= $k . $v;
        }
        $sign = md5('e9d39ee51af677d' . $str . '79aca94c9e9d39ee51af677de34a94f8');
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        if ($mysign != $sign) {
            $this->diandian_return['success'] = false;
            //这个字段为我添加
            $this->diandian_return['msg'] = '测试';
        }
        return $this->diandian_return;
    }

}
