<?php

namespace frontend\controllers;

use common\components\Helpper;
use common\components\PhoneArea;
use common\components\W;
use common\components\WxPay;
use common\models\Car_paternalor;
use common\models\CarCouponPackage;
use common\models\CarWashpay;
use Yii;
use yii\web\Response;
use yii\helpers\Url;

class WpayController extends CloudcarController
{

    public function beforeAction($action = null)
    {
        parent::beforeAction($action);
        return true;
    }

    /**
     * 购买洗车卡
     */
    public function actionWash()
    {
        $alxg_sign = W::getJSAPIShare(Yii::$app->session['token']);
        return $this->renderPartial('wash', ['alxg_sign' => $alxg_sign]);
    }

    protected function mobile_area($mobile,$area){
        $rs = PhoneArea::S360($mobile);
        if($rs === false){
            return false;
        }
        if(stripos($area,$rs['province']) === false){
            return false;
        }
        return true;
    }

    public function actionSendcode()
    {
        $request = Yii::$app->request;
        if (!$request->isPost) return '非法访问';
        $mobile = $request->post('mobile', null);
        if (!$mobile) return $this->json(0, '请输入手机号码');
        //手机归属地
        if(!$this->mobile_area($mobile,'河北')){
            return $this->json(0,'仅限河北手机用户参与');
        }
        $code = rand(100000, 999999);
        $content = "【云车驾到】您的验证码是：{$code}，有效时间3分钟。";
        $f = W::sendSms($mobile, $content, 'hbtp_', $code);
        if (!$f) {
            return $this->json(0, '验证码发送失败，请重试');
        }
        return $this->json(1, '验证码已送');
    }

    /**
     * @return array|string
     * @throws \WxPayException
     * @throws \yii\db\Exception
     */
    public function actionWashpay()
    {
        $request = Yii::$app->request;
        if (!$request->isPost) return '非法访问';
        $data = $request->post();
        //数据验证
        if (!$data['mobile']) return $this->json(0, '请输入手机号码');
        if (!$data['code']) return $this->json(0, '请输入验证码');
        if (!$data['promotion']) return $this->json(0, '请输入优惠码');
        if (!($data['num'] && is_numeric($data['num']))) return $this->json(0, '请输入正确的购买数量');
        if(!$this->mobile_area($data['mobile'],'河北')){
            return $this->json(0,'仅限河北手机用户参与');
        }
        //验证码验证
        $msg = 'ok';
        $vali = Helpper::vali_mobile_code($data['mobile'], $data['code'], $msg, 'hbtp_');
        if ($vali === false) {
            return $this->json(0, $msg);
        }
        //判断剩余数量
        $c = (new CarCouponPackage())->table()->where(['is_pay' => 1, 'status' => 1, 'uid' => 0])->count();
        if($c < $data['num']){
            return $this->json(0,'优惠券库存不足');
        }
        //开启事务，存入数据库
        $user = $this->isLogin();
        $trans = Yii::$app->db->beginTransaction();
        try {
            $main_data = $this->insert_wash_main_order($user['uid']);
            if ($main_data === false) {
                throw new \Exception('主订单写入失败');
            }
            $sub_data = $this->insert_wash_sub_order(array_merge($data, $main_data));
            if ($sub_data === false) {
                throw new \Exception('副订单写入失败');
            }
            $trans->commit();
        } catch (\Exception $e) {
            $trans->rollBack();
            return $this->json(0, $e->getMessage());
        }
        //获得微信支付参数
        $pay_data = [
            'openid' => Yii::$app->session['openid'],
            'body' => '洗车优惠券',
            'attach' => 'wash',
            'orderno' => $sub_data['orderid'],
            'total_fee' => $sub_data['amount'],
            'notify_url' => 'http://' . $_SERVER['HTTP_HOST'] . '/frontend/web/wpayback/wash.html'
        ];
        $jsapi = WxPay::WxJsApi($pay_data);
        return $this->json(1, '订单提交成功', $jsapi, Url::to(['caruorder/washinfo', 'id' => $sub_data['m_id']]));
    }

    protected function insert_wash_main_order($uid)
    {
        $obj = new Car_paternalor();
        $data = [
            'order_no' => $obj->create_order_no($uid, 'W'),
            'uid' => $uid,
            'type' => 3,
            'c_time' => time()
        ];
        $id = $obj->myInsert($data);
        if (!$id) return false;
        $data['id'] = $id;
        return $data;
    }

    protected function insert_wash_sub_order($params)
    {
        $price = CarWashpay::PRICE;
        $data = [
            'uid' => $params['uid'],
            'm_id' => $params['id'],
            'orderid' => $params['order_no'],
            'date_day' => date("Ymd"),
            'date_month' => date("Ym"),
            'mobile' => $params['mobile'],
            'promotion_code' => $params['promotion'],
            'num' => $params['num'],
            'price' => $price,
            'amount' => $params['num'] * $price,
            'c_time' => time()
        ];
        $id = (new CarWashpay())->myInsert($data);
        if (!$id) return false;
        $data['id'] = $id;
        return $data;
    }

}