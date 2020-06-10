<?php

namespace frontend\controllers;

use common\components\CarCouponAction;
use common\components\W;
use common\models\Car_rescueor;
use common\models\Car_tuhunotice;
use common\models\CarCoupon;
use common\models\CarCouponPackage;
use common\models\CarInsorder;
use common\models\CarOilor;
use common\models\CarPaternalor;
use common\models\CarSegorder;
use common\models\CarSubstituteDriving;
use common\models\CarWashpay;
use common\models\Wash_shop;
use common\models\WashOrder;
use common\models\WashShop;
use common\models\CarDisinfectionOrder;
use Yii;
use yii\helpers\Url;

class CaruorderController extends CloudcarController
{
    public $menuActive = 'caruorder';

    public function beforeAction($action = null)
    {
        parent::beforeAction($action);
        return true;
    }

    public function actionIndex()
    {
        $this->layout = 'cloudcarv2';

        //当前只有救援订单，所以只做这个的显示，其他的暂时不弄
        $user = $this->isLogin();
        $scene = CarCoupon::$coupon_faulttype;
        $s_t = Car_rescueor::$status;
        $o_st = CarOilor::$status_text;
        $ins_st = CarInsorder::$order_status;
        $w_st = WashOrder::$status_text;

        $model = new CarPaternalor();
        $main = $model->select("{{%car_paternalor}}.id,{{%car_paternalor}}.c_time,{{%car_paternalor}}.coupon_amount,{{%car_paternalor}}.type,{{%car_coupon}}.name as coupon_name", "{{%car_paternalor}}.uid = " . $user['uid'])
            ->join('LEFT JOIN', '{{%car_coupon}}', '{{%car_paternalor}}.coupon_id = {{%car_coupon}}.id')
            ->orderBy('{{%car_paternalor}}.id desc')
            ->all();
        $rescue = new Car_rescueor();
        $oil = new CarOilor();
        $insorder = new CarInsorder();
        $segorder = new CarSegorder();
        $wash = new CarWashpay();
        $ecar = new CarSubstituteDriving();
        $washOrder = new WashOrder();
        $list = [];
        //根据类型，判断列表
        $new_list = [];
        foreach ($main as $p) {
            switch ($p['type']) {
                case 1:
                    //救援
                    $temp = $rescue->table()->where(['uid' => $user['uid'], 'm_id' => $p['id']])->one();
                    if ($temp) {
                        $temp['c_time'] = $p['c_time'];
                        $temp['coupon_amount'] = $p['coupon_amount'];
                        $temp['order_type'] = $p['type'];
                        $temp['coupon_name'] = $p['coupon_name'];
                        $temp['order_use_scene'] = $scene[(string)$temp['rescueway']];
                        $temp['status_text'] = $s_t[(string)$temp['status']];
                        $temp['page_url'] = Url::to(["rescue", 'id' => $p['id']]);
                        $list[] = $temp;
                        $new_status = Car_rescueor::$old_status_to_new[$temp['status']];
                        $new_list[$new_status][] = $temp;

                    }

                    break;
                case 2:
                    //代驾
                    $temp = $ecar->table()->where(['uid' => $user['uid'], 'm_id' => $p['id']])->one();
                    if ($temp) {
                        $temp['c_time'] = $p['c_time'];
                        $temp['order_type'] = $p['type'];
                        $temp['coupon_amount'] = $p['coupon_amount'];
                        $temp['coupon_name'] = $p['coupon_name'];
                        $temp['status_text'] = CarSubstituteDriving::$status_text[(string)$temp['status']];

                        $temp['page_url'] = Url::to(["ecar", 'id' => $p['id']]);

                        $list[] = $temp;
                        $new_status = CarSubstituteDriving::$old_status_to_new[$temp['status']];
                        $new_list[$new_status][] = $temp;

                    }

                    break;
                case 3:
                    $temp = $wash->table()->where(['uid' => $user['uid'], 'm_id' => $p['id'], 'status' => 1])->one();
                    if ($temp) {
                        $temp['c_time'] = $p['c_time'];
                        $temp['coupon_amount'] = $p['coupon_amount'];
                        $temp['order_type'] = $p['type'];
                        $temp['coupon_name'] = $p['coupon_name'];
                        $temp['status_text'] = CarWashpay::$status[$temp['status']];
                        $temp['page_url'] = '';
                        $list[] = $temp;
                        $new_status = CarWashpay::$old_status_to_new[$temp['status']];
                        $new_list[$new_status][] = $temp;
                    }
                    break;
                case 4:
                    //洗车
                    $temp = $washOrder->table()->where(['uid' => $user['uid'], 'mainId' => $p['id']])->one();
                    if ($temp) {
                        $temp['c_time'] = $p['c_time'];
                        $temp['coupon_amount'] = $p['coupon_amount'];
                        $temp['order_type'] = $p['type'];
                        $temp['coupon_name'] = $p['coupon_name'];
                        $temp['status_text'] = $w_st[(string)$temp['status']];
                        $temp['page_url'] = Url::to(["washorder", 'id' => $p['id']]);
                        $new_status = WashOrder::$old_status_to_new[$temp['status']];
                        if (!$new_status == 0) {
                            $list[] = $temp;
                            $new_list[$new_status][] = $temp;
                        }
                    }
                    break;
                case 5:
                    //加油
                    $temp = $oil->table()->where(['uid' => $user['uid'], 'm_id' => $p['id']])->one();
                    if ($temp) {
                        $temp['c_time'] = $p['c_time'];
                        $temp['coupon_amount'] = $p['coupon_amount'];
                        $temp['order_type'] = $p['type'];
                        $temp['coupon_name'] = $p['coupon_name'];
                        $temp['status_text'] = $o_st[(string)$temp['status']];
                        $temp['page_url'] = Url::to(["oilinfo", 'id' => $p['id']]);
                        $list[] = $temp;
                        $new_status = CarOilor::$old_status_to_new[$temp['status']];
                        $new_list[$new_status][] = $temp;
                    }
                    break;
                case INSPECTION:
                    //年检
                    $temp = $insorder->table()->where(['uid' => $user['uid'], 'm_id' => $p['id']])->one();
                    if ($temp) {
                        $temp['c_time'] = $p['c_time'];
                        $temp['coupon_amount'] = $p['coupon_amount'];
                        $temp['order_type'] = $p['type'];
                        $temp['coupon_name'] = $p['coupon_name'];
                        $temp['status_text'] = $ins_st[(string)$temp['status']];
                        if ($temp['company_id'] == COMPANY_CHEXINGYI) {
                            $page_url = Url::to(["cyxinspection/orderdetail", 'mid' => $p['id']]);
                            if ($temp['status'] == ORDER_UNSURE || $temp['status'] == ORDER_CANCELING) {
                                $page_url = Url::to(["cyxinspection/preorderres", 'mid' => $p['id']]);
                            }
                            $temp['page_url'] = $page_url;
                        } else {
                            $temp['page_url'] = Url::to(["inspection/orderdetail", 'mid' => $p['id']]);
                        }
                        $list[] = $temp;
                        $new_status = CarInsorder::$old_status_to_new[$temp['status']];
                        $new_list[$new_status][] = $temp;
                    }
                    break;
                case SEGWAY:
                    //代步
                    $temp = $segorder->table()->where(['uid' => $user['uid'], 'm_id' => $p['id']])->one();
                    if ($temp) {
                        $temp['c_time'] = $p['c_time'];
                        $temp['coupon_amount'] = $p['coupon_amount'];
                        $temp['order_type'] = $p['type'];
                        $temp['coupon_name'] = $p['coupon_name'];
                        $temp['status_text'] = CarSegorder::$orderStatusText[CarSegorder::$order_doc[(string)$temp['status']]['sd']];
                        $temp['page_url'] = Url::to(["segway/orderdetail", 'mid' => $p['id']]);
                        $list[] = $temp;
                        $new_status = CarSegorder::$order_doc[$temp['status']]['new'];
                        $new_list[$new_status][] = $temp;
                    }
                    break;
                case 10:
                    //臭氧杀菌
                    $temp = (new CarDisinfectionOrder())->table()->where(['uid' => $user['uid'], 'main_id' => $p['id']])->one();
                    if ($temp) {
                        $temp['c_time'] = $p['c_time'];
                        $temp['coupon_amount'] = $p['coupon_amount'];
                        $temp['order_type'] = $p['type'];
                        $temp['coupon_name'] = $p['coupon_name'];
                        $temp['status_text'] = $w_st[(string)$temp['status']];
                        $temp['page_url'] = Url::to(["disorder", 'id' => $p['id']]);
                        $new_status = WashOrder::$old_status_to_new[$temp['status']];
                        if (!$new_status == 0) {
                            $list[] = $temp;
                            $new_list[$new_status][] = $temp;
                        }
                    }
                    break;
            }
        }
        //途虎洗车订单
        $tuhu_wash = (new Car_tuhunotice)->table()->select()->where(['uid' => $user['uid']])->all();

        foreach ($tuhu_wash as $v) {
            $coupon_amount = (new CarCoupon())->table()->select()->where(['uid' => $user['uid'], 'id' => $v['wash_coupon_id']])->one();
            $tuhu['c_time'] = $v['c_time'];
            $tuhu['coupon_amount'] = $coupon_amount['amount'];
            $tuhu['order_type'] = '4';
            $tuhu['coupon_name'] = $v['servicename'];
            $tuhu['status_text'] = '已完成';
            $tuhu['page_url'] = Url::to(["tuhuorder", 'id' => $v['id']]);
            $list[] = $tuhu;
            $new_list['3'][] = $tuhu;
        }

        return $this->render('index', ['list' => $list, 'new_list' => $new_list]);
    }

    public function actionOilinfo()
    {
        $this->layout = 'cloudcarv2';
        $request = Yii::$app->request;
        $id = $request->get('id', null);
        $user = $this->isLogin();
        $info = (new CarOilor())->table()->where(['uid' => $user['uid'], 'm_id' => $id])->one();
        if (!$info) {
            return '订单不存在';
        }
        $info['status_text'] = CarOilor::$status_text[(string)$info['status']];

        return $this->render('oilinfo', ['info' => $info]);
    }

    public function actionWashorder()
    {
        $this->layout = 'cloudcarv2';
        $request = Yii::$app->request;
        $id = $request->get('id', null);
        $user = $this->isLogin();

        $info = (new WashOrder())->table()->where(['uid' => $user['uid'], 'mainId' => $id])->one();
        if (!$info) {
            return '订单不存在';
        }
        $info['status_text'] = WashOrder::$status_text[(string)$info['status']];
        if ($info['shopId']) {
            //自营门店数据从wash_shop中查询
            if ($info['company_id'] == 3) {
                $shop = (new Wash_shop())->select('id,shop_address', 'id =' . (int)$info['shopId'])->one();
                $info['shopAddress'] = $shop['shop_address'];
            } else {
                $shop = (new WashShop())->table()->where(['shopId' => $info['shopId']])->one();
                // $info['shopName'] = $shop['shopName'];
                $info['shopAddress'] = $shop['shopAddress'];
            }
        }
        return $this->render('wash', ['info' => $info]);
    }

    public function actionDisorder()
    {
        $this->layout = 'cloudcarv2';
        $request = Yii::$app->request;
        $id = $request->get('id', null);
        $user = $this->isLogin();

        $info = (new CarDisinfectionOrder())->table()->select()->where(['uid' => $user['uid'], 'main_id' => $id])->one();

        if (!$info) {
            return '订单不存在';
        }
        $info['status_text'] = WashOrder::$status_text[(string)$info['status']];
        if ($info['shop_id']) {
            $shop = (new WashShop())->table()->where(['shopId' => $info['shop_id']])->one();
            $info['shopAddress'] = $shop['shopAddress'];
        }
        return $this->render('disorder', ['info' => $info]);
    }


    public function actionTuhuorder()
    {
        $this->layout = 'cloudcarv2';
        $request = Yii::$app->request;
        $id = $request->get('id', null);
        $user = $this->isLogin();

        $info = (new Car_tuhunotice())->table()->where(['uid' => $user['uid'], 'id' => $id])->one();
        if (!$info) {
            return '订单不存在';
        }
        $info['status_text'] = '已完成';
        $info['shopAddress'] = $info['region'];
        $info['shopName'] = $info['shopname'];
        $info['amount'] = $info['price'] / 100;
        $info['consumerCode'] = $info['servicecode'];
        $info['mainOrderSn'] = $info['verifytime'];
        $info['s_time'] = $info['verifytime'];

        return $this->render('wash', ['info' => $info]);
    }

    public function actionEcar()
    {
        $alxg_sign = W::getJSAPIShare(Yii::$app->session['token']);
        $this->layout = 'cloudcarv2';
        $request = Yii::$app->request;
        $id = $request->get('id', null);
        $user = $this->isLogin();
        $info = (new CarSubstituteDriving())->table()->where(['uid' => $user['uid'], 'm_id' => $id])->one();
        if (!$info) {
            return '订单不存在';
        }
        if ($info['coupon_id']) {
            $coupon = (new CarCoupon())->table()->where(['id' => $info['coupon_id']])->one();
            $info['coupon_name'] = $coupon['name'];
            $info['coupon_amount'] = $coupon['amount'];
        }
        $info['status_text'] = CarSubstituteDriving::$status_text[(string)$info['status']];


        if ($info['status'] <= 303) {
            //判断是哪个公司的，如果是0，e代驾，使用之前的链接，如果是滴滴，则使用新的。
            if ($info['company_id'] == 0) {
                return $this->render('ecar_cancel',
                    [
                        'info' => $info,
                        'alxg_sign' => $alxg_sign,
                    ]);
            } elseif ($info['company_id'] == 1) {
                $url = $info['url'];
                header("location:$url");
                die();
            }

        } else {
            return $this->render('ecar', ['info' => $info]);
        }

        return $this->render('ecar', ['info' => $info]);
    }

    public function actionRescue()
    {
        $this->layout = 'cloudcarv2';
        $request = Yii::$app->request;
        $id = $request->get('id', null);
        $user = $this->isLogin();
        $info = (new Car_rescueor())->table()->where(['uid' => $user['uid'], 'm_id' => $id])->one();
        if (!$info) {
            return '订单不存在';
        }
        if ($info['coupon_id']) {
            $coupon = (new CarCoupon())->table()->where(['id' => $info['coupon_id']])->one();
            $info['coupon_name'] = $coupon['name'];
            $info['coupon_amount'] = $coupon['amount'];
        }
        $info['status_text'] = Car_rescueor::$status_text[(string)$info['status']];
        $info['type_text'] = CarCoupon::$coupon_faulttype[(string)$info['rescueway']];

        return $this->render('rescue', ['info' => $info]);
    }

    public function actionWashinfo()
    {
        $request = Yii::$app->request;
        $id = $request->get('id', null);
        $user = $this->isLogin();
        $info = (new CarWashpay())->table()->where(['uid' => $user['uid'], 'm_id' => $id])->one();
        if (!$info) {
            return '订单不存在';
        }

        $ids = explode(',', $info['package_id']);
        $package = (new CarCouponPackage())->table()->where(['id' => $ids])->all();
        return $this->renderPartial('washinfo', ['info' => $info, 'package' => $package]);
    }


    public function actionWash()
    {
        $this->layout = 'cloudcarv2';
        $request = Yii::$app->request;
        $id = $request->get('id', null);
        $user = $this->isLogin();
        $info = (new CarWashpay())->table()->where(['uid' => $user['uid'], 'm_id' => $id])->one();
        if (!$info) {
            return '订单不存在';
        }
        $info['status_text'] = CarWashpay::$status[(string)$info['status']];
        return $this->render('wash', ['info' => $info]);
    }

    public function actionAcwash()
    {
        $request = Yii::$app->request;
        if (!$request->isPost) return '非法访问';
        $id = $request->post('id', null);
        $oid = $request->post("oid", null);
        $order = (new CarWashpay())->table()->select('mobile')->where(['id' => (int)$oid])->one();
        if (!$order) {
            return $this->json(0, '订单不存在');
        }
        $package = (new CarCouponPackage())->table()->where(['id' => (int)$id])->one();
        if (!$package) {
            return $this->json(0, '卡包不存在');
        }
        $user = $this->isLogin();
        $user['mobile'] = $order['mobile'];
        $obj = new CarCouponAction($user);
        $coupons = $obj->activate($package['package_pwd']);
        if ($coupons === false || !$coupons) {
            return $this->json(0, '激活失败');
        }
        return $this->json(1, '激活成功');
    }
}