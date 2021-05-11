<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/21 0021
 * Time: 上午 8:37
 */


namespace frontend\controllers;

use common\components\DianDian;
use common\components\EDaiJia;
use common\components\Eddriving;
use common\components\Oilcard;
use common\components\Openssl;
use common\components\ShengDaCarWash;
use common\models\Car_bank_code;
use common\models\Car_wash_order_taibao;
use common\models\CarCompany;
use common\models\CarCouponPackage;
use common\models\CarOilor;
use common\models\CarSubstituteDriving;
use common\models\Company;
use common\models\Wash_shop;
use common\models\WashOrder;
use common\models\WashShop;
use Yii;
use common\models\User;
use frontend\util\FController;
use common\components\W;
use common\components\CarCouponAction;
use common\components\BaiduMap;
use frontend\util\PController;
use common\models\Car_rescueor;
use common\models\Car_type;
use common\models\Car_oraddlog;
use common\models\Car_orstatuslog;
use common\models\Car_tuhunotice;
use common\models\Car_wash_coupon;
use common\models\CarCoupon;
use common\models\Car_washarea;
use common\models\CarDisinfectionOrder;
use Exception;
use yii\db\Expression;
use yii\helpers\Url;
use yii\web\Response;
use common\components\NationalLife;
use common\models\WashShopShandongTaibao;

class NoticeController extends PController
{

    private function log($type,$return)
    {
        $savePath = Yii::$app->getBasePath() . '/web/log/notice/' . date('Y-m') . '/';
        if (!is_dir($savePath)) {
            mkdir($savePath, 0777, true);
            chmod($savePath,0777);
        }
        $f = fopen($savePath . $type . '_' . date('Ymd') . ".txt", 'a+');
        fwrite($f, 'post_body:' . yii::$app->getRequest()->getRawBody() . "\n");
        fwrite($f, 'url:' . Yii::$app->request->queryString . "\n");
        fwrite($f, 'return:' . $return . "\n");
        fwrite($f, 'c_time:' . date('Y-m-d H:i:s') . "\n");
        fwrite($f, "===========================================\n");
        fclose($f);
    }


    //九天汽车救援订单状态通知接口
    public function actionJiutiannotice()
    {
        $response = file_get_contents("php://input");
        error_log($response, 3, './rescuestatus/' . date('Ymd') . 'rescuelog.txt');
        $info = json_decode($response, true);

        if (empty($info['orderId'])) {
            $code = 402;
            $msg = '缺少必填参数，订单ID';
            $jsonarr = ['errorCode' => $code, 'msg' => $msg];
            return json_encode($jsonarr);
        }

        if (empty($info['sign'])) {
            $code = 402;
            $msg = '缺少必填参数，签名';
            $jsonarr = ['errorCode' => $code, 'msg' => $msg];
            return json_encode($jsonarr);
        }

        if (empty($info['orderStatus'])) {
            $code = 402;
            $msg = '缺少必填参数，订单状态';
            $jsonarr = ['errorCode' => $code, 'msg' => $msg];
            return json_encode($jsonarr);
        }
        if (empty($info['responseTime'])) {
            $code = 402;
            $msg = '缺少必填参数，动作时间';
            $jsonarr = ['errorCode' => $code, 'msg' => $msg];
            return json_encode($jsonarr);
        }


        $sign = $info['sign'];
        unset($info['sign']);
        ksort($info);
        $ossstr = implode("", $info);
        $mysign = md5($ossstr . Yii::$app->params['jserverKey']);
        $code = 0;

        if ($mysign != $sign) {
            $code = 404;
            $msg = '签名错误';
        } else {
            if (!empty($info['orderId'])) {
                $model = new Car_rescueor();
                $res = $model->table()->select('id,coupon_id')->where(['orderid' => $info['orderId']])->one();
                if (!$res) {
                    $code = 407;
                    $msg = '回调失败，订单不存在';
                } else {
                    $status = $info['orderStatus'];
                    if ($status > -1 && $status < 10) {
                        $arrtime = [0 => 'order_time', 1 => 'admissible_time', 2 => 'acceptance_time', 3 => 'sendcar_time', 4 => 'back_time', 5 => 'arrive_time', 6 => 'complete_time', 7 => 'cancel_time', 8 => 'cancel_time', 9 => 'cancel_time'];
                        $nv_time = time();
                        $status = intval($status);
                        $data['status'] = $status;
                        $timekey = 'c_time';
                        if ($arrtime[$status]) $timekey = $arrtime[$status];
                        $data[$timekey] = $nv_time;
                        $data_m['order_id'] = $res['id'];
                        $data_m['order_no'] = $info['orderId'];
                        $data_m['orderstatus'] = $status;
                        $data_m['responsetime'] = $info['responseTime'];
                        $data_m['remark'] = $info['remark'];
                        $data_m['c_time'] = $nv_time;
                        $db = Yii::$app->db;
                        $transaction = $db->beginTransaction();
                        try {
                            $model->myUpdate($data, ['orderid' => $info['orderId']]);
                            (new Car_orstatuslog())->myInsert($data_m);
                            if ($status == 8 && $status == 9) {
                                (new CarCouponAction())->unuseCoupon($res['coupon_id']);
                            }
                            $msg = '回调成功';
                        } catch (\Exception $e) {
                            $transaction->rollBack();
                            $code = 407;
                            $msg = '回调失败，系统错误';
                        }
                        $transaction->commit();


                    } else {
                        $code = 401;
                        $msg = '回调失败，但订单类型不存在';
                    }
                }
            }
        }

        $jsonarr = ['errorCode' => $code, 'msg' => $msg];
        //var_dump($jsonarr);
        return json_encode($jsonarr);
    }

    //中联汽车救援订单状态通知接口
    public function actionAutonotice()
    {//orderStatus remark  responseTime
        $info = Yii::$app->request->post();
        $jsonstr = json_encode($info, JSON_UNESCAPED_UNICODE) . PHP_EOL;
        error_log($jsonstr, 3, './rescuestatus/' . date('Ymd') . 'rescuelog.txt');
        $sign = $info['sign'];
        unset($info['sign']);
        ksort($info);
        $ossstr = implode("", $info);
        $mysign = md5($ossstr . Yii::$app->params['serverKey']);
        $code = 0;
        if ($mysign != $sign) {
            $code = 1;
            $msg = '回调收到，但签名错误';
        } else {
            if (!empty($info['orderId'])) {
                $model = new Car_rescueor();
                $res = $model->table()->select('id,coupon_id')->where(['orderid' => $info['orderId']])->one();
                if (!$res) {
                    $code = 2;
                    $msg = '回调收到，但订单不存在';
                } else {
                    $status = $info['orderStatus'];
                    if ($status > -1 && $status < 10) {
                        $arrtime = [0 => 'order_time', 1 => 'admissible_time', 2 => 'acceptance_time', 3 => 'sendcar_time', 4 => 'back_time', 5 => 'arrive_time', 6 => 'complete_time', 7 => 'cancel_time', 8 => 'cancel_time', 9 => 'cancel_time'];
                        $nv_time = time();
                        $status = intval($status);
                        $data['status'] = $status;
                        $timekey = 'c_time';
                        if ($arrtime[$status]) $timekey = $arrtime[$status];
                        $data[$timekey] = $nv_time;
                        $data_m['order_id'] = $res['id'];
                        $data_m['order_no'] = $info['orderId'];
                        $data_m['orderstatus'] = $status;
                        $data_m['responsetime'] = $info['responseTime'];
                        $data_m['remark'] = $info['remark'];
                        $data_m['c_time'] = $nv_time;
                        $db = Yii::$app->db;
                        $transaction = $db->beginTransaction();
                        try {
                            $model->myUpdate($data, ['orderid' => $info['orderId']]);
                            (new Car_orstatuslog())->myInsert($data_m);
                            if ($status == 8 && $status == 9) {
                                (new CarCouponAction())->unuseCoupon($res['coupon_id']);
                            }
                            $msg = '回调成功';
                        } catch (\Exception $e) {
                            $transaction->rollBack();
                            $code = 3;
                            $msg = '回调失败，系统错误';
                        }
                        $transaction->commit();


                    } else {
                        $code = 4;
                        $msg = '回调收到，但订单类型不存在';
                    }
                }
            }
        }

        $jsonarr = ['errorCode' => $code, 'msg' => $msg];
        //var_dump($jsonarr);
        return json_encode($jsonarr);
    }

    //实时获取地理位置
    public function actionAddressnotice()
    {//orderStatus remark  responseTime
        $info = Yii::$app->request->post();
        $jsonstr = json_encode(serialize($info), JSON_UNESCAPED_UNICODE) . PHP_EOL;

        error_log($jsonstr, 3, './rescueaddress/' . date('Ymd') . 'rescuelog.txt');
        $sign = $info['sign'];
        unset($info['sign']);
        ksort($info);
        $ossstr = implode("", $info);
        $mysign = md5($ossstr . Yii::$app->params['serverKey']);
        $code = 0;
        $msg = '回调成功';
        if ($mysign != $sign) {
            $code = 1;
            $msg = '回调收到，但签名错误';
        } else {
            if (!empty($info['orderId'])) {
                $model = new Car_rescueor();
                $addModel = new Car_oraddlog();
                $res = $model->table()->select('id')->where(['orderid' => $info['orderId']])->one();
                $resadd = $addModel->table()->select('id')->where(['orderid' => $info['orderId']])->orderBy('id desc')->one();
                if (!$res) {
                    $code = 2;
                    $msg = '回调收到，但订单不存在';
                } else {
                    $data['orderid'] = $info['orderId'];
                    $address = json_decode($info['lblData'], true);
                    $address_res = BaiduMap::coords($address);
                    $addre = end($address_res);
                    if ($addre['x'] > 180 || $addre['x'] < -180 || $addre['y'] > 90 || $addre['y'] < -90) {
                        $code = 5;
                        $msg = '回调收到，经纬度错误';
                    } else {
                        $data['longitude'] = $addre['x'];
                        $data['latitude'] = $addre['y'];
                        $data['or_id'] = $res['id'];

                        if (!$resadd) {
                            $data['c_time'] = time();
                            $res = $addModel->myInsert($data);
                            if (!$res) {
                                $code = 3;
                                $msg = '回调失败，系统错误';
                            }
                        } else {
                            $data['u_time'] = time();
                            $addModel->myUpdate($data, ['orderid' => $info['orderId']]);
                            if (!$res) {
                                $code = 3;
                                $msg = '回调失败，系统错误';
                            }
                        }
                    }
                }
            }
        }

        $jsonarr = ['errorCode' => $code, 'msg' => $msg];
        return json_encode($jsonarr);
    }

    //途虎核销回调接口  示例{"code":"10000","msg":"成功"}
    /*
          * 洗车卡券核销错误代码
          * 100000	成功
          * 400001	传入参数错误
          * 400002	缺少方法名参数
          * 400003	参数不合法
          * 400004	缺少Sign参数
          * 400005	签名验证错误
          * 400006	访问太频繁
          */
    public function actionTuhunotice()
    {

        $response = file_get_contents("php://input");
        error_log($response, 3, APP_PATH . '/tuhu/' . date('Ymd') . 'tuhulog.txt');
        $data = array_filter(json_decode($response, true));
        $errmsg = Car_tuhunotice::$errmsg;
        $err['code'] = '100000';
        $err['msg'] = $errmsg[$err['code']];
        $th_sign = $data['signature'];

        if (empty($data['serviceCode'])) {
            $err['code'] = '400001';
            $err['msg'] = $errmsg[$err['code']];
            return json_encode($err);
        }
        if (empty($th_sign)) {
            $err['code'] = '400004';
            $err['msg'] = $errmsg[$err['code']];
            return json_encode($err);
        }
        unset($data['signature']);
        ksort($data);
        $str = '';
        foreach ($data as $key => $val) {
            $str .= $key . '=' . $val . '&';
        }
        $str .= hash("sha256", Yii::$app->params['signKey']);
        $my_sign = hash("sha256", $str);
        if ($th_sign != $my_sign) {
            $err['code'] = '400005';
            $err['msg'] = $errmsg[$err['code']];
            return json_encode($err);
        }

        $noticeModel = new Car_tuhunotice();
        $res = $noticeModel->table()->select('id')->where(['servicecode' => $data['serviceCode']])->one();
        $rescoupon = (new Car_wash_coupon())->table()->select('id,coupon_id,uid')->where(['servicecode' => $data['serviceCode']])->one();
        if (!$rescoupon) {
            $err['code'] = '400001';
            $err['msg'] = '本平台无此服务码';
            return json_encode($err);
        }
        $datadb = [];
        $datadb['uid'] = $rescoupon   ['uid'];
        $datadb['shopid'] = $data['shopId'];
        $datadb['shopname'] = $data['shopName'];
        $datadb['serviceid'] = $data['serviceId'];
        $datadb['servicename'] = $data['serviceName'];
        $datadb['verifytime'] = $data['verifyTime'];
        $datadb['price'] = $data['price'];
        $datadb['region'] = $data['region'];
        $datadb['servicecode'] = $data['serviceCode'];
        $datadb['wash_coupon_id'] = $rescoupon['id'];
        $datadb['c_time'] = time();
        $couponModel = new CarCoupon();
        $companyid=$couponModel->table()->select('companyid')->where(['id' => $rescoupon['coupon_id']])->one();
        $datadb['companyid'] = $companyid['companyid'];

        if (!$res) {
            $db = Yii::$app->db;
            $transaction = $db->beginTransaction();
            try {
                $noticeModel->myInsert($datadb);
                (new Car_wash_coupon())->myUpdate(['status' => '2', 'use_time' => $datadb['c_time']], ['servicecode' => $datadb['servicecode']]);
                $couponModel->myUpdate(['used_num' => new Expression("used_num + 1"), 'use_time' => $datadb['c_time']], ['id' => $rescoupon['coupon_id']]);
                $numarr = $couponModel->table()->select('used_num,amount')->where(['id' => $rescoupon['coupon_id']])->one();
                if (intval($numarr['used_num']) === intval($numarr['amount']) && $numarr['used_num'] > 0) {
                    $couponModel->myUpdate(['status' => '2'], ['id' => $rescoupon['coupon_id']]);
                }
                $transaction->commit();
            } catch (\Exception $e) {
                $transaction->rollBack();
                $err['code'] = '400001';
                $err['msg'] = '系统异常';
            }
        } else {
            $err['code'] = '400006';
            $err['msg'] = $errmsg[$err['code']];
        }
        return json_encode($err);
    }

    public function actionTice()
    {

        // {"serviceCode":"FW0307598252","shopId":"30468","shopName":"福建省泉州市南安市店","verifyTime":"20180620142727","region":"福建省泉州市南安市","serviceId":"FU-MD-DKHBZXC|1","serviceName":"鼎翰-标准洗车","price":"2700",
        //"signature":"40688bfad280035a3ca78ef214f3c404afb37cd5fc0569bff94dd156cefd3639"}

        $url = Yii::$app->params['url'] . '/frontend/web/notice/tuhunotice.html';
        $data = [];
        $data['shopId'] = "30468";
        $data['shopName'] = "福建省泉州市南安市店";
        $data['serviceId'] = "FU-MD-DKHBZXC|1";
        $data['serviceName'] = "鼎翰-标准洗车";
        $data['verifyTime'] = "20180620142727";
        $data['price'] = "2700";
        $data['region'] = "福建省泉州市南安市";
        $data['serviceCode'] = "FW0307598252";
        ksort($data);
        $str = '';
        foreach ($data as $key => $val) {
            $str .= $key . '=' . $val . '&';
        }

        $str .= hash('sha256', Yii::$app->params['signKey']);
        header('Content-type: text/html; charset=utf-8');
        $data['signature'] = hash('sha256', $str);
        var_dump(Yii::$app->params['signKey'] . '--' . $data['signature']);
        $datastr = json_encode($data);
        $header = array(
            "content-type:application/json; charset=UTF-8",
            "Content-Length: " . strlen($datastr)
        );
        $result = W::http_post($url, $datastr, $header);
        var_dump(json_decode($result, true));
    }

    public function actionOil()
    {
        $request = Yii::$app->request;
        $data = [];
        $data['userId'] = $request->get('userId');
        $data['bizId'] = $request->get("bizId");
        $data['ejId'] = $request->get("ejId");
        $data['downstreamSerialno'] = $request->get("downstreamSerialno");
        $data['status'] = $request->get('status');
        $sign = $request->get('sign');
        $memo = $request->get('memo', null);
        if ($memo) $memo = urldecode($memo);

        //验证签名
        $mysign = (new Oilcard())->sign($data);
        if ($sign != $mysign) {
            return 'sign is no valid';
        }
        $map['orderid'] = $data['downstreamSerialno'];
        $obj = new CarOilor();
        $order = $obj->table()->where($map)->one();
        if ($order['status'] != $data['status']) {
            $order['status'] = $data['status'];
            if ($data['status'] == 2 || $data['status'] == 3) {
                $order['s_time'] = time();
            }
            if($data['status'] == 3){
                //恢复优惠券
                $r = (new CarCouponAction())->unuseCoupon($order['coupon_id']);
                if($r === false){
                    $this->log('oil','fail');
                    return 'fail';
                }
            }
            if ($memo) {
                $order['errmsg'] = $memo;
            }
            $r = $obj->myUpdate($order);
            if ($r === false) {
                $this->log('oil','fail');
                return 'fail';
            }
        }
        $this->log('oil','success');
        return 'success';
    }

    /**
     * 盛大洗车核销接口
     * @return array
     */
    public function actionShengda()
    {
        $baseUrl = Yii::$app->request->queryString;
        $postBody = yii::$app->getRequest()->getRawBody();

        $params = strstr($baseUrl, "&");
        $params = substr($params, 1);
        $status = 1;
        $msg = '订单处理成功';
        $trans = Yii::$app->db->beginTransaction();
        try {
            $shengdaObj = new ShengDaCarWash();
            $checkSign = $shengdaObj->checkMd5($params, $postBody);
            if(!$checkSign){
                //throw new \Exception('sign验证错误');
            }
            $postParam = json_decode($postBody,true);
            $washObj = new WashOrder();
            $order = $washObj->table()->select()->where(['consumerCode' => $postParam['order'], 'mobile' => $postParam['userInfo']])->one();

            //太保核销
            if(!$order){
                $tbObj = new Car_wash_order_taibao();
                $tbOrder = $tbObj->table()->where([
                    'consumer_code'=>$postParam['order'],
                    'apply_phone'=>$postParam['userInfo'],
                    'status'=>3
                ])->one();
                $disObj = new CarDisinfectionOrder();
                $disOrder = $disObj->table()->select()->where([
                    'consumer_code'=>$postParam['order'],
                    'mobile'=>$postParam['userInfo'],
                    'status'=>1
                ])->one();
                if(!$tbOrder && !$disOrder){
                    throw new \Exception('订单不存在');
                }
                if($tbOrder){
                    //黑名单
                    if($tbOrder['apply_phone'] == '15095128226' || $tbOrder['car_rental_vehicle_no'] == '鲁GY280R'){
                        throw new \Exception('您已进入黑名单');
                    };
                    $result = $this->tbOrderStatus($tbOrder,'DW01002','1006','0');
                    if(!$result){
                        throw new \Exception('太保接口调用失败');
                    }
                    if($result['ReturnCode'] == 0){
                        throw new \Exception('错误代码:'.$result['ErrorCode'].',错误描述:'.$result['ErrorMessage']);
                    }
                    $tbOrder['status'] = ORDER_SUCCESS;
                    $tbOrder['equity_status'] = ORDER_SUCCESS;
                    $tbOrder['u_time'] = time();
                    $res = (new Car_wash_order_taibao())->myUpdate($tbOrder);
                    if(!$res){
                        throw new \Exception('订单状态写入失败');
                    }
                }else{
                    $disOrder['status'] = ORDER_SUCCESS;
                    $disOrder['s_time'] = time();
                    $disres = $disObj->myUpdate($disOrder);
                    if(!$disres){
                        throw new \Exception('臭氧杀菌订单状态写入失败');
                    }
                }

            } else {
                if($order[$status] == ORDER_SUCCESS){
                    throw new \Exception('订单已经核销过');
                }
                $order['status'] = ORDER_SUCCESS;
                $order['s_time'] = time();
                $res = $washObj->myUpdate($order);
                if($order['companyid'] == Yii::$app->params['national_life']['companyid']){
                    $couponinfo = (new CarCoupon())->table()->select('coupon_sn')->where(['id'=>$order['couponId']])->one();
                    $datags = [
                        'num' => $order['id'],
                        'order_id' => $order['outOrderNo'],
                        'cdkey' => $couponinfo['coupon_sn'],
                        'mobile' => $order['mobile'],
                        'shop_name' => $order['shopName'],
                        'service_name' => $order['serviceName'],
                        'certificate' => $order['consumerCode'],
                        'status' => WashOrder::$status_text['2'],
                        'create_time' => date("Y-m-d H:i:s",$order['c_time']),
                        'update_time' => date("Y-m-d H:i:s",$order['s_time'])
                    ];
                    $objgs = new NationalLife();
                    $objgs->notice($datags);
                }
                if(!$res){
                    throw new \Exception('订单状态写入失败');
                }
            }

            $trans->commit();
        } catch (\Exception $e){
            $trans->rollBack();
            $msg = $e->getMessage();
            $status = 0;
        }

        $shengdaObj->log($baseUrl, $postBody, $msg,'order');
        return $this->json($status, $msg);
    }


    /**
     * 回调给山东太保洗车核销信息
     * @param $washOrder
     * @return bool
     */
    private function tbOrderStatus($washOrder,$code,$status,$type)
    {
        $params = [
            'TicketId' => $washOrder['ticket_id'],
            'ServiceType' => $washOrder['service_type'],
            'SurveyUnitCode' => $status,
            'TicketType' => $type,
            'StatusTime' => date('Y-m-d H:i:s',time()),
            'TheTimeStamp' => (string)sprintf('%.0f', microtime(true)*1000),
        ];

        $repuestData = [
            'RequestCode' => $code,
            'AppId' => "cpic_e_rescue",
            'RequestBodyJson' => $params
        ];

        $url = Yii::$app->params['shandongtaibao']['url'];
        $params = json_encode($repuestData);
        $res = W::http_post($url,$params);
        (new DianDian())->requestlog($url,$params,json_encode($res,JSON_UNESCAPED_UNICODE),'taibaowash',$status,'taibaowash');

        return json_decode($res,true);
    }


    /**
     * 太保订单推送
     * @return array|string
     * @throws \yii\db\Exception
     */
    public function actionTborder()
    {
        if(!Yii::$app->request->isPost){
            return '非法请求';
        }
        $url = Yii::$app->request->absoluteUrl;
        $post = yii::$app->getRequest()->getRawBody();
        $postBody = json_decode($post,true);
        $status = '1';
        $msg = '';
        $now = time();
        $trans = Yii::$app->db->beginTransaction();
        try {
            $orderObj = new Car_wash_order_taibao();
            //订单状态ticketType 0-受理 1-取消
            if($postBody['TicketType'] == 0){
                //暂停服务时间段
                $jinzhitime =   strtotime($postBody['PointTime']);
                if($jinzhitime >= 1611676800 && $jinzhitime < 1614355200){
                    throw new Exception('临近春节服务网点人员返乡，洗车服务将于2021年1月27日-2021年2月26日暂停服务，在此期间系统暂停预约及使用，因此给您带来的不便深表歉意，敬请谅解！恭祝尊敬的客户春节快乐、阖家幸福！');
                }
                //时间范围限制
                $datetime = strtotime(date('Y-m-d'));
                if($jinzhitime < $datetime )throw new Exception('预约时间不能在当天之前，请重新选择预约时间！');
                $year = $datetime+30*86400;
                if($jinzhitime > $year) throw new Exception('预约时间不能在30天之后，请重新选择预约时间！');

                //检查订单号是否重复 20201103 许雄泽
                $check = $orderObj->table()->select()->where([
                    'ticket_id'=>$postBody['TicketId']
                ])->one();
                if($check) throw new Exception('当前订单已存在，无需重新下单');

                //查询用户洗车推送订单 20201103 许雄泽  1111 注释
//                $order = $orderObj->table()->select()->where([
//                    'car_rental_vehicle_no'=>$postBody['CarRentalVehicleNo'],
//                    'apply_phone'=>$postBody['ApplyPhone'],
//                    'status' => [1,3]
//                ])->order('id DESC')->one();
//                //如果有推送订单，根据状态返回信息
//                if($order){
//                    throw new Exception('当前用户还有正在进行中的订单');
//                    //洗车限制判断，每天限制一次
////                    if($order['status'] == 2 && $order['u_time']>(time()-60*60*24)){
////                        throw new Exception('洗车次数限制');
////                    }
//                }

                //通过盛大接口下单
                $sourceApp = Yii::$app->params['shengda_sourceApp'];
                $param = [
                    'source' => $sourceApp,
                    'orgSource' => $sourceApp,
                    'order' => $sourceApp.$postBody['TicketId'],
                    'randStr' => $postBody['TicketId'],
                    'carType' => '03',
                    'userInfo' => $postBody['ApplyPhone'],
                    'endTime' => $postBody['PointTime'],
                    'userOrderType' => 'order',
                    'generationRule' => '02',
                ];
                $washObj = new ShengDaCarWash();
                $result = $washObj->receiveOrder($param);
                if($result['resultCode'] != 'SUCCESS'){
                    throw new \Exception('服务器响应失败');
                }
                $resultJson = strstr($result['encryptJsonStr'],'|',true);
                $resultCode = json_decode( $resultJson,true);
                //添加网点名称和ID  20201104 许雄泽
                $address = explode(' ',$postBody['Address']);
                $address = $address[3];
                $where = " address LIKE '%".$address."%' ";
                $shop = (new WashShopShandongTaibao())->select('*',$where)->orderBy(' id DESC ')->one();
                if($shop){
                    $shop_name = $shop['name'];
                    $shop_id = $shop['id'];
                }else{
                    $shop_name = ' ';
                    $shop_id = 0;
                }

                $insData = [
                    'ticket_id' => $postBody['TicketId'],
                    'branch_code' => $postBody['BranchCode'],
                    'unit_code' => $postBody['UnitCode'],
                    'apply_name' => $postBody['ApplyName'],
                    'apply_phone' => $postBody['ApplyPhone'],
                    'car_rental_vehicle_no' => $postBody['CarRentalVehicleNo'],
                    'point_time' => $postBody['PointTime'],
                    'service_type' => $postBody['ServiceType'],
                    'address' => $postBody['Address'],
                    'encrypt_code' => $resultCode['encryptCode'], //盛大消费券码 客户使用
                    'consumer_code' => $resultCode['order'],  //盛大消费二维码 核销使用
                    'status' => 1, //订单状态 -1-取消 1-进行中 2-完成
                    'c_time' => $now,
                    'u_time' => $now,
                    'shop_name'=>$shop_name,
                    'shop_id'=>$shop_id
                ];

                //写入太保洗车订单
                $res = $orderObj->myInsert($insData);
                if(!$res){
                    throw new \Exception('数据写入失败');
                }

                //$f = W::sendSms($postBody['ApplyPhone'],'【云车驾到】尊敬的客户，您已获得太平洋产险山东分公司（盛大）赠送的“洗车”服务，核销码为：'.$insData['encrypt_code'].'，请在预约时间（'.$param['endTime'].'）前往使用，券码当天有效，如有疑问请拨打客服电话：400-617-1981。'.'门店名称：'.$shop_name.'，门店地址：'.$insData['address'].'。');
                $f = W::sendSms($postBody['ApplyPhone'],'【云车驾到】您已获得太平洋产险山东分公司赠送的洗车券：'.$insData['encrypt_code'].'，请在'.$param['endTime'].'前往'.$shop_name.'（'.$address.'）使用，券码当天有效，如有疑问请拨打：400-617-1981。');
            }elseif($postBody['TicketType'] == 1) {
                $order = $orderObj->table()->select()->where(['ticket_id'=>$postBody['TicketId']])->one();
                if(!$order){
                    throw new \Exception('该订单编号不存在');
                }
                if($order['status'] == 2){
                    throw new \Exception('该订单已经完成，无法取消');
                }
                if($order['status'] == -1){
                    throw new \Exception('该订单已取消，请勿重复取消');
                }
                //太保取消订单同时通知盛大也取消 20201118 许雄泽
                $cancelres = $this->cancelShengDaOrder($order);
                if($cancelres['resultCode'] != 'SUCCESS'){
                    throw new \Exception('订单取消失败');
                }
                $order['status'] = ORDER_CANCEL;
                $order['u_time'] = time();
                $r = $orderObj->myUpdate($order);
                if(!$r){
                    throw new \Exception('取消状态写入失败');
                }

            }
            $trans->commit();
        }catch (\Exception $e){
            $trans->rollBack();
            $status = '0';
            $msg = $e->getMessage();
        }

        $returnData = [
            'ReturnCode' => $status,
            'ErrorCode' => '',
            'ErrorMessage' => $msg
        ];

        (new DianDian())->requestlog($url,$post,json_encode($returnData,JSON_UNESCAPED_UNICODE),'taibaowash',$status,'taibaowash');


        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $returnData;
    }

    /**
     * 太保订单状态通知，定时任务
     * @return bool
     */
    public function actionTbstatus()
    {
        $orderObj = new Car_wash_order_taibao();
        $order = $orderObj->table()->where(['status'=>1])->all();
        if($order){
            foreach ($order as $val){
                //传已接单状态
                $r = $this->tbOrderStatus($val,'DW01002','1002','0');
                if($r['ReturnCode'] == 1){
                    //写入订单状态已接单
                    $res = $orderObj->myUpdate(['status'=>3],['id'=>$val['id']]);
                }
            }

        }

        return true;
    }


    /**
     * 太保洗车网点推送
     * @return bool|string
     */
    public function actionWashshop()
    {
        $request = Yii::$app->request;
        if(!$request->isPost){
            return '非法请求';
        }
        $post = yii::$app->getRequest()->getRawBody();
        $postBody = json_decode($post,true);
        $map = [
            'prov' =>$postBody['province'],
            'city' =>$postBody['city'],
            'district' =>$postBody['district'],
        ];
        $shopStatus = trim($request->post('status',1));

        $shopList = (new WashShop())->table()->select()->where($map)->all();
        $counNum = count($shopList);
        $spCode = Yii::$app->params['shandongtaibao']['spCode'];
        foreach($shopList as $val){
            $areasList[] = [
                'branchName' => '山东分公司',
                'provinceName' => $val['prov'],
                'cityName' => $val['city'],
                'areaName' => $val['district'],
                'addressDetail' => $val['shopAddress'],
                'commissionPoint' => $val['shopName'],
                'contactPhone' => $val['shopTel'],
                'inspectionType' => 16,
                'sjId' => $spCode.$val['id'],
                'spCode' => $spCode
            ];
        }
        $params = [
            'sjAreasNum' => $counNum,
            'status' => $shopStatus,
            'sjAreasList' => $areasList
        ];

        $repuestData = [
            'RequestCode' => 'DW01007',
            'AppId' => 'cpic_e_rescue',
            'RequestBodyJson' => $params
        ];

        $url = Yii::$app->params['shandongtaibao']['url'];
        $params = json_encode($repuestData,JSON_UNESCAPED_UNICODE);
        $res = W::http_post($url,$params);

        return $res;
    }

    public function actionWashshop2()
    {
        $request = Yii::$app->request;
        if(!$request->isPost){
            return '非法请求';
        }
        $post = yii::$app->getRequest()->getRawBody();
        $postBody = json_decode($post,true);
//        $postBody = [
//            'province' =>'山东省',
//            'city' =>'济南市',
//            'district' =>'市中区',
//        ];
        $postData = [
            'source' => Yii::$app->params['shengda_sourceApp'],
            // 'pro' => $post['pro'],
            'isMap' => 1,
            'page' => '1',
            'pageSize' => '100'
        ];
        $postData['city'] = $postBody['city'];
        $postData['area'] = $postBody['district'];
        $address = [
            'province' =>$postBody['province'],
            'city' =>$postBody['city'],
            'district' =>$postBody['district'],
        ];
        //获取省市区
        $location = (new Car_washarea())->getLocation($address);
        $shopStatus = trim($request->post('status',1));
        $shengDa = new ShengDaCarWash();
        $result = $shengDa->merchantDistanceList($postData);


        $result = strstr($result['encryptJsonStr'],'|',true);
        $result = json_decode($result,true);
        $shopList = $result['coEnterprises'];
        $counNum = count($result);
        $spCode = Yii::$app->params['shandongtaibao']['spCode'];
        foreach($shopList as $val){
            $areasList[] = [
                'branchName' => '山东分公司',
                'provinceName' => $location['province']['name'],
                'cityName' => $location['city']['name'],
                'areaName' => $location['area']['name'],
                'addressDetail' => $val['address'],
                'commissionPoint' => $val['name'],
                'contactPhone' => $val['telephone'],
                'inspectionType' => 16,
                'sjId' => $spCode.$val['distance']*10000,
                'spCode' => $spCode
            ];
        }
        $params = [
            'sjAreasNum' => $counNum,
            'status' => $shopStatus,
            'sjAreasList' => $areasList
        ];

        $repuestData = [
            'RequestCode' => 'DW01007',
            'AppId' => 'cpic_e_rescue',
            'RequestBodyJson' => $params
        ];

        $url = Yii::$app->params['shandongtaibao']['url'];
        $params = json_encode($repuestData,JSON_UNESCAPED_UNICODE);
//        return $params;
        $res = W::http_post($url,$params);

        return $res;
    }
    /**
     * 订单状态变更
     */
    public function actionEcarStatus(){
        $baseUrl = Yii::$app->request->queryString;

        $return = ['code' => 0,"message"=> 'suc'];
        $origin_data = $data = Yii::$app->request->post();
        $sign = $data['sig'];
        unset($data['sig']);

        //订单状态更改
        $status = $data['orderStatus'];
        $obj = new CarSubstituteDriving();
        $ecar = new EDaiJia();
        $lcfg = Yii::$app->params['Ecar'];
        $mysign = md5($lcfg['from']);
        $bookingid =$data['bookingId'];
        //检查订单id是否存在
        $info = $obj->table()->where(['booking_id' => $bookingid,'company_id'=>0])->one();
        $couponModel = new CarCoupon();


        try {
            //订单状态更改
//            $status = $data['orderStatus'];
//
//
//            $obj = new CarSubstituteDriving();
//            $ecar = new EDaiJia();
//
//            $lcfg = Yii::$app->params['Ecar'];
//            $mysign = md5($lcfg['from']);
            if($mysign!=$sign){
//                 throw new \Exception('sign不正确');
            }
//
//            $bookingid =$data['bookingId'];
//            //检查订单id是否存在
//            $info = $obj->table()->where(['booking_id' => $bookingid,'company_id'=>0])->one();
            if(!$info){
                throw new \Exception('order not found!');
            }

            $update = [];
            $update['status'] = $status;
            $update['polling_state'] = 1;
            switch ($status){
                case 301:
                    $driverId = $update['driver_id'] = $data['driverId'];
                    $update['order_id'] = $data['orderId'];
                    break;
                case 302:
                    $driverId = $update['driver_id'] = $data['driverId'];
                    $update['order_id'] = $data['orderId'];
                    break;


                case 501:
                    $update['order_id'] = $data['orderId'];
                    $update['cast'] = $data['price'];
                    $update['amount']  = $data['income'];
                    $update['end_time'] =time();
                    break;
                default:
                    break;

            }

            //更新状态

            (new CarSubstituteDriving())->myUpdate($update, ['id' => $info['id']]);
            //如果没有司机获取司机信息
            //根据司机id，查询信息,更新年龄
            if($info['driveryear']==0){
                if(isset($driverId)){
                    $result = $ecar->getDriverInfo($driverId);
                    if($result['code']==0){
                        $update = [];
                        $year = $result['driverInfo']['year'];
                        $update['driveryear'] =$year;
                        $update['drivername'] =$result['driverInfo']['name'];
                        (new CarSubstituteDriving())->myUpdate($update, ['id' => $info['id']]);
                    }
                }
            }

            //优惠券更改
            if(in_array($status,[401,402,403,404,404,506]) ){
//                $couponModel->myUpdate(['status'=>1],['id'=>$info['coupon_id']]);
                (new CarCouponAction())->unuseCoupon($info['coupon_id']);
            }


        } catch (\Exception $e){
            $msg = $e->getMessage();


            $return['code']=1;
            $return['message']=$msg;
        }
        //核销优惠券

        $coupon_info = $couponModel->table()->select('id,status')->where(['id' => $info['coupon_id']])->one();
        if($coupon_info['status']==1 && in_array($status,[301,302,303,501]) ){
//            (new CarCouponAction())->useCoupon($info['coupon_id']);
            $couponModel->myUpdate(['status'=>2],['id'=>$coupon_info['id']]);
        }

        $origin_data = \GuzzleHttp\json_encode($origin_data);
        $return_data = \GuzzleHttp\json_encode($return);
        (new DianDian())->requestlog($baseUrl,$origin_data,$return_data,'edaijia',$status,'EDaiJia');


        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $return;
    }

    /**
     *用于e代驾的优惠券回调
     * 张珍 19年4月2号删除
     */
    public function actionCouponStatusDelete(){
/*        $baseUrl = Yii::$app->request->queryString;

        $return = ['code' => 0,"message"=> 'suc'];
        $origin_data = $data = Yii::$app->request->post();
        $sign = $data['sig'];
        unset($data['sig']);
        try {
            //订单状态更改
            $bonus = $data['bonusSn'];
            $event_time = strtotime($data['eventTime']);


            $obj = new CarCoupon();

            $lcfg = Yii::$app->params['Ecar'];
            $mysign = md5($lcfg['from']);
            if($mysign!=$sign){
               throw new \Exception('sign不正确');
            }


            //检查优惠券是否存在
            $info = $obj->table()->where(['coupon_sn' => $bonus,'coupon_type'=>1])->one();
            if(!$info){
                throw new \Exception('coupon not found!');
            }

            $update = [];
            $update['status'] = 2;  //2 表示已经使用
            $update['use_time'] = $event_time;  //更新使用时间。。

            //更新状态
            (new CarCoupon())->myUpdate($update, ['id' => $info['id']]);


        } catch (\Exception $e){
            $msg = $e->getMessage();
            $return['code']=1;
            $return['message']=$msg;
        }
        $origin_data = \GuzzleHttp\json_encode($origin_data);
        $return_data = \GuzzleHttp\json_encode($return);
        (new DianDian())->requestlog($baseUrl,$origin_data,$return_data,'edaijia',2,'EDaiJia');

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $return;*/
    }


    /**
     *用于帮冲的回调
     */
    public function actionBangChongStatus(){
        $baseUrl = Yii::$app->request->queryString;

        $return = ['code' => 0,"message"=> 'suc'];
        $origin_data = $data = Yii::$app->request->post();
        $sign = $data['sig'];
        unset($data['sig']);
        $trans = Yii::$app->db->beginTransaction();
        $status = 0;

        try {

            //订单状态更改
            $order_no = $data['order_no'];
            $user_order_no = $data['user_order_no'];
            $status = $data['user_order_no'];

            $obj = new CarOilor();

//            测试ip和端口：http://120.27.219.49:80
//            测试uid：test
//测试appkey：1e76d4de45e674ecf6893c773f2356e9
//油卡的business_id是yk  签名验证: MD5(uid+order_no+user_order_no+{APP_KEY})， APP_KEY由帮充对接商务提供

            $lcfg = Yii::$app->params['bangchong'];
            $mysign = md5($lcfg['uid']+$order_no+$user_order_no+$lcfg['oil_key']);
            if($mysign!=$sign){
                throw new \Exception('sign不正确');
            }


            //检查订单是否存在
            $info = $obj->table()->where(['orderid' => $user_order_no,'bizorderid'=>$order_no])->one();
            if(!$info){
                throw new \Exception('order not found!');
            }

            $update = [];
            if($status==200){
                $update['status'] = 2;  //2 表示成功
            }elseif ($status==202){
                $update['status'] = 3;  //3 表示失败
                $update['s_time'] = time();
                (new CarCouponAction())->unuseCoupon($info['coupon_id']);

            }


            //更新状态
            $obj->myUpdate($update, ['id' => $info['id']]);

            $trans->rollBack();
        } catch (\Exception $e){
            $trans->commit();
            $msg = $e->getMessage();
            $return['code']=-1;
            $return['message']=$msg;
        }
        $origin_data = \GuzzleHttp\json_encode($origin_data);
        $return_data = \GuzzleHttp\json_encode($return);
        (new DianDian())->requestlog($baseUrl,$origin_data,$return_data,'BangChong',$status,'BangChong');

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $return;
    }

    //人保兑换码
    public function actionRenbao()
    {
        $postBody = yii::$app->getRequest()->getRawBody();
        //以公司ID查询openssl加密的key和iv,comapny表中secret对应加密算法的key,appkey对应加密算法中的iv
        $company = 18;
       // $company = 20;
        $companys = (new CarCompany())->table()->where(['id' =>$company])->one();
        $openSll = new Openssl($companys['secret'],$companys['appkey'],'AES-128-CBC');
        $status = 1;
        $msg = '兑换码处理成功';
        $resultDesc = [];
        try {
            $data = json_decode($postBody,true);
            $data = $data['encryptJsonStr'];
            $data = $openSll->decrypt($data);
            if(!$data){
                throw new \Exception('参数解密失败');
            }
            $data = json_decode($data,true);
            if(!$data['redeem_code']){
                throw new \Exception('没有兑换码列表');
            }
            if(!$data['batch_nb']){
                throw new \Exception('没有优惠券批号');
            }
            $batch = $data['batch_nb'];
            $data = explode(',',$data['redeem_code']);
            //生成批号
            $batch_no=W::createNonceCapitalStr(8);
            $is_you=(new Car_bank_code())->table()->select('id')->where(['batch_no'=>$batch_no])->one();
            if($is_you){
                throw new \Exception('批号重复');
            }
            //查询券包剩余数量
            $couponnum = (new CarCouponPackage())->table()->select('id')->where(['companyid'=>$company,'status'=>1,'batch_nb'=>$batch])->count();
//            $banknum=(new Car_bank_code())->table()->select('id')->where(['status'=>1,'package_batch_no'=>$batch])->count();
            $banknum = count($data);
            $num = $couponnum-$banknum;
            foreach ($data as $val){
                if($num <= 0){
                    $resultDesc[]=[
                        'redeem_code' => $val,
                        'fialDesc' => '不存在此批券包或此批券包已用完！'
                    ];
                    break;
                }
                //正则验证兑换码
                $pattern = W::is_renbaocode($val);
                if(!$pattern){
                    $resultDesc[]=[
                        'redeem_code' => $val,
                        'fialDesc' => '兑换码格式不正确！'
                    ];
                    continue;
                }
                //如果数据库中有兑换码且状态为1，则返回
                $is_coupon=(new Car_bank_code())->table()->select('id')->where(['bank_code'=>$val,'status'=>1])->one();
                if($is_coupon){
                    $resultDesc[]=[
                        'redeem_code' => $val,
                        'fialDesc' => '该兑换码已经存在！'
                    ];
                    continue;
                }
                //查询券包
                $packageNum=(new CarCouponPackage())->table()->select('id,batch_nb,companyid')->where(['companyid'=>$company,'status'=>1,'batch_nb'=>$batch])->one();
                if(!$packageNum){
                    $resultDesc[]=[
                        'redeem_code' => $val,
                        'fialDesc' => '不存在此批券包或此批券包已用完'
                    ];
                    continue;
                }
                //插入数据库
                $insetData = [
                    'bank_code' => $val,
                    'package_batch_no'=>$packageNum['batch_nb'],
                    'company_id' => $company,
                    'batch_no' => $batch_no,
                    'c_time' =>time(),
                    'status' => 1
                ];
                $bank_code =(new Car_bank_code())->myInsert($insetData);
                if(!$bank_code){
                    $resultDesc[]=[
                        'redeem_code' => $val,
                        'fialDesc' => '数据插入失败'
                    ];
                }

            }

        } catch (\Exception $e){
            $msg = $e->getMessage();
            $status = 0;
        }
        if($resultDesc){
            $status = 0;
            $msg = '部分处理失败';
            $resultDesc = json_encode($resultDesc);
            $resultDescen = $openSll->encrypt($resultDesc);

        }

        $result = [
            'status' => $status,
            'msg' => $msg,
            'result' => $resultDesc,//记录日志使用
            'resultDesc' => $resultDescen,
            'num' => $couponnum
        ];
        $this->log('renbao',json_encode($result));
        //删除未加密的数组元素
        unset($result['result']);
        return json_encode($result);
    }


    /**
     * 取消盛大订单
     * @param $washOrder
     * @return mixed
     * @throws Exception
     */
    private function cancelShengDaOrder($washOrder)
    {
        $sourceApp = Yii::$app->params['shengda_sourceApp'];
        $param = [
            'source' => $sourceApp,
            'order' => $washOrder['encrypt_code'],
            'refundStatus'=>2
        ];
        $washObj = new ShengDaCarWash();
        $result = $washObj->cancelOrder($param);
        $encryp= strstr($result['encryptJsonStr'],'|',true);
        $resultCode = json_decode($encryp,true);
        if(!$resultCode){
            throw new \Exception('接口连接失败');
        }
        return $resultCode;
    }
}