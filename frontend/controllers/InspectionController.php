<?php

namespace frontend\controllers;

use common\components\CarCouponAction;
use common\components\DianDianInspection;
use common\components\EJin;
use common\components\Juhe;
use common\components\W;
use common\models\Car_paternalor;
use common\models\CarCoupon;
use common\models\CarInsorder;
use common\models\CarUseraddr;
use common\models\CarUserCarno;
use common\models\FansAccount;
use Yii;
use yii\helpers\ArrayHelper;


class InspectionController extends CloudcarController
{
    public $menuActive = 'carhome';
    public $layout = 'cloudcarv2';
    public $site_title = '代办年检';
    protected $uid;
    protected $user;
    protected $insType = ['1' => '免上线检测', '2' => '上线检测'];

    public function beforeAction($action = null)
    {
        parent::beforeAction($action);
        $user = $this->isLogin();
        $this->user = $user;
        $this->uid = (int)$user['uid'];
        return true;
    }

    /**
     * 代办年检首页
     * @return string
     */
    public function actionIndex()
    {
        $cache = Yii::$app->cache;
        $couponId = Yii::$app->request->get('couponid', 0);
        Yii::$app->session->set('couponid', $couponId);
        $insCar = (new DianDianInspection($this->uid))->vehicleInspectionGet();
        $isAll = 1;
        if (!$insCar['success']) {
            $carInfo = ['status' => ERROR_STATUS, 'msg' => $insCar['message']];
        } else {
            $insCarArr = ArrayHelper::index($insCar['data']['list'], 'carId');//获取典典数据

            $resCar = (new CarUserCarno())->get_user_bind_car($this->uid);
            $resCarArr = ArrayHelper::index($resCar, 'carId');//获取本地保存数据

            $carInfo = array_intersect_key($insCarArr, $resCarArr);//取两数组共有的在典典中的数据
            $isAll = (int)(count($carInfo) > 0 && count($carInfo) == count($resCarArr));//交集数据不为空，且长度与本地保存车辆一致，说明本地车辆信息全部是典典中保存完整的
            $cache->set('diandian_car_' . $this->uid, $carInfo);
            $cache->set('diandian_preorder_' . $this->uid, null);
            $carInfo['status'] = SUCCESS_STATUS;
        }
        return $this->render('index', ['carinfo' => $carInfo, 'isAll' => $isAll, 'carjson' => json_encode($carInfo)]);
    }

    /**
     * 判断年检类型
     */
    public function actionChecktype()
    {
        $session = Yii::$app->session;
        $cache = Yii::$app->cache;
        $carArr = $cache->get('diandian_car_' . $this->uid);
        $carId = Yii::$app->request->get('carId', 0);
        $couponId = (Yii::$app->request->get('couponid', 0)) ?: $session->get('couponid');
        $session->set('carId', $carId);//保存供选券用

        $couponres = (new CarCoupon())->table()->orderBy('use_limit_time asc')->where(['uid' => $this->uid, 'status' => COUPON_ACTIVE, 'coupon_type' => INSPECTION])->all();
        $couponinfo = array();
        if ($couponres) {
            $couponId = $couponId ?: $couponres[0]['id'];//优惠券有值就取，没有的，默认取所持券的第一个的id
            $session->set('couponid', $couponId);//选择的券id
            $couponindex = ArrayHelper::index($couponres, 'id');
            $couponinfo = $couponindex[$couponId];
        }
        $carInfo = $carArr[$carId];
        $carInfo['insType'] = array_key_exists($carInfo['inspectionType'], $this->insType) ? $this->insType[$carInfo['inspectionType']] : '不支持年检';
        return $this->render('checktype', ['carinfo' => $carInfo, 'coupon' => $couponres, 'couponinfo' => $couponinfo]);
    }

    /**
     * 预约办理提交订单
     */
    public function actionPreorder()
    {
        $request = Yii::$app->request;
        $cache = Yii::$app->cache;
        $carArr = $cache->get('diandian_car_' . $this->uid);
        $carId = $request->get('carId', 0);
        $getdata = array(
            'carId' => $carId,
            'inspectType' => $carArr[$carId]['inspectionType'],
        );
        $resorder = (new DianDianInspection($this->uid))->inspectionOrderPre($getdata);
        if (!$resorder['success']) {
            $carInfo = ['status' => ERROR_STATUS, 'msg' => $resorder['message']];
        } else {
            $carInfo = $resorder['data'];
            $carInfo['couponid'] = (int)Yii::$app->session->get('couponid');
            $cache->set('diandian_preorder_' . $this->uid, $carInfo);
            $carInfo['status'] = SUCCESS_STATUS;
        }

        $useraddr = (new CarUseraddr())->getUserDefaultAddr($this->uid);
        return $this->render('preorder', ['carinfo' => $carInfo, 'useraddr' => $useraddr]);
    }

    /**
     * 确认提交订单
     * @return array
     */
    public function actionSureorder()
    {
        $cache = Yii::$app->cache;
        $carArr = $cache->get('diandian_car_' . $this->uid);
        $resorder = $cache->get('diandian_preorder_' . $this->uid);
        if (!$resorder) {
            return $this->json(ERROR_STATUS, '非法访问');
        }
        $coupon_id = $resorder['couponid'];
        $request = Yii::$app->request;
        $data = $request->post();

        try {
            $coupon = (new CarCoupon())->table()->where(['id' => $coupon_id, 'uid' => $this->uid, 'status' => COUPON_ACTIVE])->one();
            if (!$coupon) {
                throw new \Exception('无效年检券');
            }
            $diandian_ins = new DianDianInspection($this->uid);
            $uaddrid = $data['uaddrid'];
            $useraddr = (new CarUseraddr())->getUserAddr($this->uid, 'id=' . $uaddrid, 'one');//得到用户的地址
            $carInfo = $carArr[$resorder['carId']];

            $postdata['shopId'] = $resorder['inspectionShopId'];
            $postdata['carId'] = $resorder['carId'];
            $postdata['inspectType'] = $carInfo['inspectionType'];
            $postdata['contactPhone'] = $data['carresp'];
            $postdata['inspectCityId'] = $resorder['inspectionCityId'];
            $postdata['carOwnerPhone'] = $data['carphone'];
            $postdata['orderPrice'] = $resorder['totalPrice'];
            $postdata['name'] = $useraddr['name'];
            $postdata['phone'] = $useraddr['mobile'];
            $postdata['province'] = $useraddr['province'];
            $postdata['city'] = $useraddr['city'];
            $postdata['county'] = $useraddr['region'];
            $postdata['address'] = $useraddr['street'];

            $res = $diandian_ins->inspectionOrderCreate($postdata);//创建订单得到典典传来的orderid
            if (isset($res['success']) && $res['success']) {
                $order_id = $res['data']['orderId'];
            } else {
                throw new \Exception($res['message']);
            }

            //预下单这步移到“填写单号”完成时再进行（在没填写单号时，可由我们控制取消订单）
//            $data['payMethod'] = DianDianInspection::$paytype['alipay'];
//            $data['orderId'] = $order_id;
//            $data['payPrice'] = $resorder['totalPrice'];
//            $res = $diandian_ins->orderPre($data);
//            if (isset($res['success']) && $res['success']) {
//
//            } else {
//                throw new \Exception($res['message']);
//            }

            $trans = Yii::$app->db->beginTransaction();
            $couponObj = new CarCouponAction($this->user);
            $coupon = $couponObj->useCoupon($coupon_id);
            if ($coupon === false) {
                throw new \Exception($couponObj->msg);
            }
//            $mainOrder = $this->main_order($this->uid, $coupon_id, $coupon['amount']);//年检没有券，所以只能保存典典的费用
            $mainOrder = $this->main_order($this->uid, $coupon_id, $resorder['totalPrice']);
            if ($mainOrder === false) {
                throw new \Exception('订单写入失败1');
            }
            $insdata = [
                'uid' => $this->uid,
                'couponid' => $coupon_id,
                'date_day' => date('Ymd'),
                'date_month' => date('Ym'),
                'm_id' => $mainOrder['id'],
                'orderid' => $mainOrder['order_no'],
                'carid' => $resorder['carId'],
                'carnum' => $resorder['carNum'],
                'carcity' => $resorder['inspectionCityName'],
                'carphone' => $data['carphone'],
                'carresp' => $data['carresp'],
                'uaddrid' => $uaddrid,
                'c_time' => time(),
                'amount' => $resorder['totalPrice'],
                'status' => ORDER_UNSURE,//待处理
                'errmsg' => json_encode($res),
                'bizorderid' => $order_id,
                'company_id' => $coupon['company'],
                'companyid' => $coupon['companyid'],
            ];
            $car_insorder = new CarInsorder();
            $res = $car_insorder->myInsert($insdata);
            if (!$res) {
                throw new \Exception('订单详细信息写入失败');
            }
            $trans->commit();
            $cache->set('diandian_preorder_' . $this->uid, null);
            //生成订单后，填写单号前，给用户发送提醒短信一次start
            $fans_account = FansAccount::find()->where(['uid' => $this->uid])->one();
            $content = \Yii::$app->params['ins_create_order_new'];
            $mobile = $fans_account['mobile'];
//            $getdata = [
//                'orderId' => $order_id,
//                'inspectType' => $carArr[$resorder['carId']]['inspectionType'],
//            ];
//            $odetail = (new DianDianInspection($this->uid))->inspectionOrderDetail($getdata);
//            $smsaddr = '地址:'.$odetail['data']['shopAddress'].' 联系人:'.$odetail['data']['contactName'].' 电话:'.$odetail['data']['contactPhone'];
//            $content = str_replace("smsaddr", $smsaddr, $content);
            if ($mobile) {
                W::sendSms($mobile, $content);
            }
            //end
            return $this->json(SUCCESS_STATUS, 'ok', ['id' => $mainOrder['id']]);

        } catch (\Exception $e) {
            if (isset($trans)) {
                $trans->rollBack();
            }
            return $this->json(ERROR_STATUS, $e->getMessage());
        }
    }

    /**
     * 生成主订单
     * @param $uid
     * @param $coupon_id
     * @param $coupon_amount
     * @return array|bool
     */
    protected function main_order($uid, $coupon_id, $coupon_amount)
    {
        $obj = new Car_paternalor();
        $data = [
            'order_no' => $obj->create_order_no($uid, 'INS'),
            'uid' => $uid,
            'type' => INSPECTION,
            'coupon_id' => $coupon_id,
            'coupon_amount' => $coupon_amount,
            'c_time' => time()
        ];
        $id = $obj->myInsert($data);
        if ($id && is_numeric($id)) {
            $data['id'] = $id;
            return $data;
        }
        return false;
    }

    /**
     * 订单详情
     */
    public function actionOrderdetail()
    {
        $this->menuActive = 'caruorder';
        $request = Yii::$app->request;
        $cache = Yii::$app->cache;
        $carArr = $cache->get('diandian_car_' . $this->uid);
        $mid = $request->get('mid', 0);
        $info = (new CarInsorder())->table()->where(['uid' => $this->uid, 'm_id' => $mid])->one();
        if (!$info) {
            return '订单不存在';
        }
        $inspectType = $carArr[$info['carid']]['inspectionType'];
        $orderId = $info['bizorderid'];
        $getdata = [
            'orderId' => $orderId,
            'inspectType' => $inspectType,
        ];
        $odetail = (new DianDianInspection($this->uid))->inspectionOrderDetail($getdata);
        if (!$odetail['success'] || !isset($odetail['data']) || !$odetail['data']) {
            return '订单数据错误';
        }

        $mainorder = (new Car_paternalor())->table()->where(['id' => $mid, 'type' => INSPECTION])->one();

        $info['couponprice'] = $mainorder['coupon_amount'];
        $info['inspectionType'] = $this->insType[$carArr[$info['carid']]['inspectionType']];
        $info['c_time'] = date('Y-m-d H:i:s', $info['c_time']);
        $info = ArrayHelper::merge($info, $odetail['data']);
        //物流信息查询
        $expressinfo = array();
        if ($info['expresscom'] && $info['expressno']) {
            $params['com'] = $info['expresscom'];
            $params['no'] = $info['expressno'];
            $resinfo = Juhe::deliver($params);
            $expressinfo['reason'] = $resinfo['reason'];
            $expressinfo['result'] = $resinfo['result'];
            $expressinfo['result']['list'] = array_reverse($resinfo['result']['list']);
        }
        return $this->render('orderdetail', ['info' => $info, 'expressinfo' => $expressinfo]);
    }

    /**
     * 填写单号
     */
    public function actionExpress()
    {
        $request = Yii::$app->request;
        if ($request->isPost) {
            $data = $request->post();
            $trans = Yii::$app->db->beginTransaction();
            try {
//                $insorder = (new CarInsorder())->table()->where(['id' => $data['id']])->one();
//                $postdata['payMethod'] = DianDianInspection::$paytype['alipay'];
//                $postdata['orderId'] = $insorder['bizorderid'];
//                $postdata['payPrice'] = $insorder['amount'];
//                $res = (new DianDianInspection($this->uid))->orderPre($postdata);
//                if (isset($res['success']) && $res['success']) {
//                    $data['errmsg'] = json_encode($res);
//                    $data['status'] = ORDER_HANDLING;
//                    (new CarInsorder())->myUpdate($data);
//                } else {
//                    throw new \Exception($res['message']);
//                }
                $res = (new CarInsorder())->myUpdate($data);
                if ($res === false) {
                    throw new \Exception('操作失败');
                }
                $trans->commit();
                return $this->json(SUCCESS_STATUS, '操作成功');

            } catch (\Exception $e) {
                $trans->rollBack();
                return $this->json(ERROR_STATUS, $e->getMessage());
            }
        } else {
            $id = $request->get('id', 0);
            $express = Juhe::deliver();
            $expressinfo = array();
            if ($express['resultcode'] == 200) {
                $expressinfo = $express['result'];
            }
            return $this->render('express', ['id' => $id, 'expressinfo' => $expressinfo]);
        }
    }

    /**
     * 提交订单
     * @return array
     * @throws \yii\db\Exception
     */
    public function actionSubmitorder(){
        $data = Yii::$app->request->post();
        $trans = Yii::$app->db->beginTransaction();
        try {
            $insorder = (new CarInsorder())->table()->where(['id' => $data['id']])->one();
            $postdata['payMethod'] = DianDianInspection::$paytype['alipay'];
            $postdata['orderId'] = $insorder['bizorderid'];
            $postdata['payPrice'] = $insorder['amount'];
            $res = (new DianDianInspection($this->uid))->orderPre($postdata);
            if (isset($res['success']) && $res['success']) {
                $data['errmsg'] = json_encode($res);
                $data['status'] = ORDER_HANDLING;
                (new CarInsorder())->myUpdate($data);
            } else {
                throw new \Exception($res['message']);
            }
            $trans->commit();

            //使用优惠券回调通知。业务：不管用户办理成功与否，都不会返还优惠券，所以就在提交订单时就回调通知
            (new EJin())->useCouponNotice($insorder);

            return $this->json(SUCCESS_STATUS, '操作成功');

        } catch (\Exception $e) {
            $trans->rollBack();
            return $this->json(ERROR_STATUS, $e->getMessage());
        }
    }

    /**
     * 取消订单
     */
    public function actionCancelorder()
    {
        $request = Yii::$app->request;
        $id = $request->post('id');
        $orderinfo = (new CarInsorder())->table()->where(['id' => $id])->one();
        $trans = Yii::$app->db->beginTransaction();
        try {
            $updata['id'] = $id;
            $updata['status'] = ORDER_CANCEL;
            $res = (new CarInsorder())->myUpdate($updata);
            if ($res === false) {
                throw new \Exception('订单取消失败');
            }
            //恢复券
            $r = (new CarCouponAction())->unuseCoupon($orderinfo['couponid']);
            if ($r === false) {
                throw new \Exception('优惠券出错');
            }
            $trans->commit();
            return $this->json(SUCCESS_STATUS, '订单取消成功');
        } catch (\Exception $e) {
            $trans->rollBack();
            return $this->json(ERROR_STATUS, $e->getMessage());
        }
    }

    /**
     * 获得用户的回寄地址列表
     * @return string
     */
    public function actionUseraddr()
    {
        $useraddrs = (new CarUseraddr())->getUserAddr($this->uid);
        return $this->render('useraddr', ['useraddrs' => $useraddrs]);
    }

    /**
     * 编辑或新增回寄地址
     */
    public function actionHandleuseraddr()
    {
        $request = Yii::$app->request;
        $addrobj = new CarUseraddr();
        if ($request->isPost) {
            $data = $request->post();
            if ($data['type'] == 'del') {
                $res = $addrobj->delUserAddr($data['id']);
                if ($res) {
                    return $this->json(SUCCESS_STATUS, '操作成功');
                }
            } elseif ($data['type'] == 'setdefault') {
                $res = $addrobj->setUserDefault($data['id'], $this->uid);
                if ($res) {
                    return $this->json(SUCCESS_STATUS, '操作成功');
                }
            } else {
                $data['uid'] = $this->uid;
                $useraddr = $addrobj->getUserDefaultAddr($this->uid);
                if (!$useraddr) {
                    $data['isdefault'] = 1;
                }
                unset($data['type']);
                $res = $addrobj->addUserAddr($data);
                if ($res !== false) {
                    return $this->json(SUCCESS_STATUS, '操作成功');
                }
            }
            return $this->json(ERROR_STATUS, '操作失败');

        } else {
            $addrid = $request->get('id', 0);
            $addrinfo = (new CarUseraddr())->getUserAddr($this->uid, ' id = ' . $addrid, 'one');
            return $this->render('handleuseraddr', ['addrinfo' => $addrinfo]);
        }
    }
}
