<?php

namespace frontend\controllers;

use common\components\CarCouponAction;
use common\components\DianDianOilCard;
use common\components\Oilcard;
use common\models\Car_coupon_explain;
use common\models\Car_paternalor;
use common\models\CarCoupon;
use common\models\CarOilor;
use common\models\CarUseroilcard;
use common\models\FansAccount;
use Yii;
use yii\helpers\Url;

class CaroilController extends CloudcarController
{
    public $menuActive = 'caruser';

    public function beforeAction($action = null)
    {
        parent::beforeAction($action);
        return true;
    }

    public function actionIndex()
    {
        $this->layout = 'cloudcarv2';
        $user = $this->isLogin();
        //查询是否绑定了油卡
        $info = (new CarUseroilcard())->table()->where(['uid' => (int)$user['uid']])->one();
        if (!$info) {
            return $this->redirect(['bind']);
        }
        if ($info['oil_card_type'] == 1) {
            $info['type_txt'] = '中石油';
            $info['imgName'] = 'zhongshiyou';
        } else {
            $info['type_txt'] = '中石化';
            $info['imgName'] = 'zhongshihua';
        }
        //获得油卡
        $temp = (new CarCoupon())->get_user_bind_coupon_list($user['uid'], 5);
        $list = [];
        $now = time();
        foreach ($temp as $cp) {
            if ($cp['status'] == 1 && $now < $cp['use_limit_time']) {
                $list[] = $cp;
            }
        }
        $use_text = (new Car_coupon_explain())->get_use_text();
//        VarDumper::dump($list);
//        VarDumper::dump($use_text);exit;
        return $this->render('index', ['info' => $info,'list' => $list, 'use_text' => $use_text]);
    }

    /**
     * 绑定油卡
     */
    public function actionBind()
    {
        $this->layout = 'cloudcarv2';
        $request = Yii::$app->request;

        //用于连接，从哪里来，回到哪里去。。
        if ($request->isGet) {
            $from = $request->get('from',null);
            Yii::$app->session->set('caroil_url_from', $from);
        }

        $user = $this->isLogin();
        if ($request->isPost) {
            $data = $request->post();
            $data['uid'] = $user['uid'];
            $r = (new CarUseroilcard())->bind($data);

            //如果是从用户系统进入的，则依然进入用户系统
            $from = Yii::$app->session->get('caroil_url_from');
            if( $from == "user"){
                $url =  Url::to(['caruser/index']);
            }else{
                $url =  Url::to(['index']);
            }
            return $this->json($r['status'], $r['msg'], [], $url);

        }
        $id = $request->get('id', null);
        $info = null;
        if ($id && is_numeric($id)) {
            $info = (new CarUseroilcard())->table()->where(['id' => (int)$id, 'uid' => (int)$user['uid']])->one();
        }
        return $this->render('bind', ['info' => $info]);
    }

    public function actionCoupon()
    {
        $request = Yii::$app->request;
        $user = $this->isLogin();
        //$card = (new CarUseroilcard())->table()->where(['uid' => $user['uid']])->one();
        //获得油卡
        $temp = (new CarCoupon())->get_user_bind_coupon_list($user['uid'], 5);
        $list = [];
        $now = time();
        foreach ($temp as $cp) {
            if ($cp['status'] == 1 && $now < $cp['use_limit_time']) {
                $list[] = $cp;
            }
        }
        $use_text = (new Car_coupon_explain())->get_use_text();
        return $this->render('coupon', ['list' => $list, 'use_text' => $use_text]);
    }

    public function actionConfirm()
    {
        $request = Yii::$app->request;
        $user = $this->isLogin();
        $coupon_id = $request->get('cid', null);
        if (!($coupon_id && is_numeric($coupon_id))) {
            return $this->redirect(['coupon']);
        }
        $card = (new CarUseroilcard())->table()->where(['uid' => $user['uid']])->one();
        $coupon = (new CarCoupon())->table()->where(['id' => $coupon_id, 'uid' => $user['uid']])->one();

        return $this->render('confirm', ['info' => $card, 'coupon' => $coupon]);
    }

    /**
     * 下单
     * @return array
     */
    public function actionPlayorder()
    {
        $request = Yii::$app->request;
        if ($request->isPost) {
            $coupon_id = $request->post('cid');

            $user = $this->isLogin();
            try {
                $coupon = (new CarCoupon())->table()->where(['id' => $coupon_id])->one();
                if (!$coupon) {
                    throw new \Exception('coupou 不存在');
                }


                //获得要充值的油卡
                $oilcard = (new CarUseroilcard())->table()->where(['uid' => $user['uid']])->one();
                if (!$oilcard) {
                    throw new \Exception('请先绑定油卡');
                }

                //监测是否五分钟内充值过
                if ($oilcard['oil_card_type']==1) {
                    $oil = (new CarOilor())->table()->where(['uid' => $user['uid']])->orderBy('id desc')->one();
                    if($oil){
                        if($oil['c_time']+10*60>time()){
                            throw new \Exception('因为中石油服务商服务商限制，请过5分钟之后，再次充值');
                        }
                    }
                }



                //对其逻辑进行判断，如果company_id 为1，则继续执行旧的流程，否则，执行新的流程。
                if ($coupon['company'] == 1) {

                    $couponObj = new CarCouponAction($user);
                    $coupon = $couponObj->useCoupon($coupon_id);
                    $trans = Yii::$app->db->beginTransaction();
                    if ($coupon === false) {
                        throw new \Exception($couponObj->msg);
                    }


                    //获得itemid
                    $itemid = Oilcard::get_item_id($oilcard['oil_card_no'], $coupon['amount'], $oilcard['oil_card_type']);
                    if (!is_numeric($itemid)) {
                        throw new \Exception($itemid);
                    }
                    $mainOrder = $this->main_order($user['uid'], $coupon_id, $coupon['amount']);
                    if ($mainOrder === false) {
                        throw new \Exception('订单写入失败1');
                    }

                    $data = [
                        'uid' => $user['uid'],
                        'coupon_id' => $coupon_id,
                        'm_id' => $mainOrder['id'],
                        'orderid' => $mainOrder['order_no'],
                        'card_no' => $oilcard['oil_card_no'],
                        'card_type' => $oilcard['oil_card_type'],
                        'amount' => $coupon['amount'],
                        'date_day' => date('Ymd'),
                        'date_month' => date('Ym'),
                        'c_time' => time(),
                        'errmsg' => '',
                        'bizorderid' => 0,
                        'itemid' => $itemid,

                        'company_id' => $coupon['company'],
                        'companyid' => $coupon['companyid'],
                    ];
                    $car_oilor = new  CarOilor();
                    $result = $car_oilor->myInsert($data);

                    if (!$result) {
                        throw new \Exception('订单详细信息写入失败');
                    }

                    $trans->commit();
                    return $this->json(1, 'ok', ['id' => $mainOrder['id']]);


                } elseif($coupon['company'] == 2) {
                    //这部分为典典用车新增部分，张珍写入

                    //调用典典用车充值接口
                    $diandian_oil = new DianDianOilCard();

                    $data = [];
                    $data['money'] = intval($coupon['amount']);
                    $result = $diandian_oil->oil_card_instore($data);
                    if (isset($result['success']) && $result['success'] == 1) {

                    } else {
                        throw new \Exception($result['message']);
                    }

                    $trans = Yii::$app->db->beginTransaction();
                    $couponObj = new CarCouponAction($user);
                    $coupon = $couponObj->useCoupon($coupon_id);
                    if ($coupon === false) {
                        throw new \Exception($couponObj->msg);
                    }

                    $mainOrder = $this->main_order($user['uid'], $coupon_id, $coupon['amount']);
                    if ($mainOrder === false) {
                        throw new \Exception('订单写入失败1');
                    }

                    $data = [
                        'uid' => $user['uid'],
                        'coupon_id' => $coupon_id,
                        'm_id' => $mainOrder['id'],
                        'orderid' => $mainOrder['order_no'],
                        'card_no' => $oilcard['oil_card_no'],
                        'card_type' => $oilcard['oil_card_type'],
                        'amount' => $coupon['amount'],
                        'date_day' => date('Ymd'),
                        'date_month' => date('Ym'),
                        'c_time' => time(),
                        'errmsg' => json_encode($result),
                        'bizorderid' => 0,
//                        'carriertype' => $order_id,
                        'errmsg' => json_encode($result),
                        'company_id' => $coupon['company'],
                        'companyid' => $coupon['companyid'],
                    ];
                    $car_oilor = new  CarOilor();
                    $result = $car_oilor->myInsert($data);

                    if (!$result) {
                        throw new \Exception('订单详细信息写入失败');
                    }
                }elseif ($coupon['company']==3 or $coupon['company']==4){

                    $couponObj = new CarCouponAction($user);
                    $coupon = $couponObj->useCoupon($coupon_id);
                    
                    //这部分为聚合/帮冲新增部分，张珍写入
                    $mainOrder = $this->main_order($user['uid'], $coupon_id, $coupon['amount']);
                    if ($mainOrder === false) {
                        throw new \Exception('订单写入失败..');
                    }

                    $data = [
                        'uid' => $user['uid'],
                        'coupon_id' => $coupon_id,
                        'm_id' => $mainOrder['id'],
                        'orderid' => $mainOrder['order_no'],
                        'card_no' => $oilcard['oil_card_no'],
                        'card_type' => $oilcard['oil_card_type'],
                        'amount' => $coupon['amount'],
                        'date_day' => date('Ymd'),
                        'date_month' => date('Ym'),
                        'c_time' => time(),
                        'bizorderid' => 0,
                        'errmsg' => '',
                        'company_id' => $coupon['company'],
                        'companyid' => $coupon['companyid'],
                    ];
                    $car_oilor = new  CarOilor();
                    $result = $car_oilor->myInsert($data);

                    if (!$result) {
                        throw new \Exception('订单详细信息写入失败');
                    }
                }


                $trans->commit();
                if ($oilcard['oil_card_type']==1) {
                    $msg = '因为中石油限制，再次充值请于10分钟后操作！';
                }else{
                    $msg = 'ok';
                }
                return $this->json(1, $msg, ['id' => $mainOrder['id']]);

            } catch (\Exception $e) {
                if (isset($trans)) {
                    $trans->rollBack();
                }
                return $this->json(0, $e->getMessage());
            }
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
            'order_no' => $obj->create_order_no($uid, 'OIL'),
            'uid' => $uid,
            'type' => 5,
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
     * 副订单
     * @param $uid
     * @param $coupon_id
     * @param $m_id
     * @param $orderid
     * @param $card_no
     * @param $card_type
     * @param $amount
     * @param $itemid
     * @param $errcode
     * @param $errmsg
     * @param string $areacode
     * @param int $bizorderid
     * @param int $carriertype
     * @param int $itemfaceprice
     * @param string $itemname
     * @param int $price
     * @return bool
     */
    public function sub_order($uid, $coupon_id, $m_id, $orderid, $card_no, $card_type, $amount, $itemid, $errcode, $errmsg, $areacode = '', $bizorderid = 0, $carriertype = 0, $itemfaceprice = 0, $itemname = '', $price = 0)
    {
        $data = [
            'uid' => $uid,
            'coupon_id' => $coupon_id,
            'm_id' => $m_id,
            'date_day' => date('Ymd'),
            'date_month' => date('Ym'),
            'orderid' => $orderid,
            'card_no' => $card_no,
            'card_type' => $card_type,
            'amount' => $amount,
            'itemid' => $itemid,
            'c_time' => time(),
            'errcode' => $errcode,
            'errmsg' => $errmsg,
            'areacode' => $areacode,
            'bizorderid' => $bizorderid,
            'carriertype' => $carriertype,
            'itemfaceprice' => $itemfaceprice,
            'itemname' => $itemname,
            'price' => $price
        ];
        $id = (new CarOilor())->myInsert($data);
        if ($id && is_numeric($id)) return true;
        return false;
    }
}