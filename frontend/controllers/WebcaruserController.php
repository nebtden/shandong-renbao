<?php

namespace frontend\controllers;

use common\components\CarCateType;
use common\models\Car_coupon_explain;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use yii\helpers\Url;
use common\components\Helpper;
use common\components\W;
use common\components\AlxgBase;
use common\models\CarUserCarno;
use common\components\CarCouponAction;
use common\components\Eddriving;
use common\models\CarCoupon;

class WebcaruserController extends WebcloudcarController
{

    public $menuActive = 'caruser';
    public $layout = 'webcloudcarv2';

    public function beforeAction($action = null)
    {
        parent::beforeAction($action);
        return true;
    }

    public function actionIndex()
    {
        //$this->layout = 'webcloudcar';
        $user = $this->isLogin();
        $vip = $this->fans_account();
        return $this->render('index', ['user' => $user, 'vip' => $vip]);
    }

    /**
     * 手机号码绑定
     * @return string
     */
    public function actionBindmobile()
    {
        $request = Yii::$app->request;
        if ($request->isPost) {
            $user = $this->isLogin();
            $mobile = $request->post('mobile', null);
            $code = $request->post('code', null);
            if (!$mobile) return $this->json(0, '请输入手机号码！');
            $msg = 'ok';
            if (!Helpper::vali_mobile_code($mobile, $code, $msg)) return $this->json(0, $msg);
            $now = time();
            $data = [
                'uid' => $user['uid'],
                'realname' => $user['nickname'],
                'mobile' => $mobile,
                'u_time' => $now,
            ];
            $this->fans_account(true);
            $ac = $this->fans_account();
            $model = new AlxgBase('fans_account', 'id');
            $fansMobel = new AlxgBase('fans', 'id');
            if ($ac) {
                //更新
                $db = Yii::$app->db;
                $trans = $db->beginTransaction();
                try{
                    $fansMobel->myUpdate(['mobile'=>$mobile],['id'=>$user['uid']]);
                    $model->myUpdate($data, ['id' => $ac['id']]);
                }catch (\Exception $e){
                    $trans->rollBack();
                    return $this->json(0, '绑定失败，请重试！');
                }
                $trans->commit();
            } else {
                //新增
                $data['c_time'] = $now;
                $id = $model->myInsert($data);
                if (!$id) return $this->json(0, '绑定失败，请重试！');
            }
            //更新session;
            $user['nickname'] = $mobile;
            $user['mobile'] = $mobile;
            Yii::$app->session['wx_user_auth_web'] = $user;
            
            $this->fans_account(true);
            return $this->json(1, 'ok', [], Url::to(['index']));
        }
        return $this->render('bindmobile');
    }

    /**
     * 发送验证码
     */
    public function actionSendsms()
    {
        $request = Yii::$app->request;
        if (!$request->isPost) return '非法访问';
        $mobile = $request->post('mobile', null);
        if (!$mobile) return $this->json(0, '请输入手机号码');
        $user = $this->isLogin();
        //判断手机号码是否已绑定
        if ($this->mobile_is_exist($mobile)) {
            return $this->json(0, '手机号码被占用，请更换手机号码！');
        }
        $f = W::sendSms($mobile);
        if (!$f) return $this->json(0, '验证码发送失败，请重试！');
        return $this->json(1, 'ok');
    }

    //绑定的车辆列表
    public function actionCarlist()
    {
        //$this->layout = 'webcloudcar';
        $user = $this->isLogin();
        $list = (new CarUserCarno())->get_user_bind_car($user['uid']);
        return $this->render('carlist', ['list' => $list]);
    }

    //绑定车牌编辑页
    public function actionBindcar()
    {
        $this->layout = 'webcloudcar';
        $request = Yii::$app->request;
        if ($request->isPost) {
            $data = $request->post();
            if (!$data['card_brand']) return $this->json(0, '请选择车型');
            if (!$data['card_no']) return $this->json(0, '请输入车牌号码');
            if (!$data['car_model_small_fullname']) return $this->json(0, '请选择车型');
            $user = $this->isLogin();
            $data['uid'] = $user['uid'];
            $rs = (new CarUserCarno())->update_card($data);
            if ($rs === false) return $this->json(0, '操作失败');
            return $this->json(1, '操作成功', [], Url::to(['carlist']));
        }
        $id = $request->get('id', 0);
        $data = null;
        if ($id && is_numeric($id) && $id > 0) {
            $data = (new CarUserCarno())->table()->where(['id' => $id])->one();
        }
        //车型数据
        $brands = CarCateType::Brand();
        $anchor = array_keys($brands);
        $car_types = CarUserCarno::$car_type;//车牌类型
        unset($car_brands);
        return $this->render('bindcar', ['data' => $data, 'brands' => $brands, 'anchors' => $anchor, 'car_types' => $car_types]);
    }

    public function actionGetcarmodel()
    {
        $request = Yii::$app->request;
        if (!$request->isPost) return '非法访问';
        $id = $request->post('id', 0);
        if (!$id) return $this->json(0, 'id=0');
        $list = CarCateType::Type($id);
        $html = $this->renderPartial('carmodel', ['list' => $list]);
        return $this->json(1, 'ok', $html);
    }

    /**
     * 我的卡券
     */
    public function actionCoupon()
    {
        $request = Yii::$app->request;
        $coupon_type = $request->get("coupon_type",null);
        $footer = $request->get("footer",null);
        $user = $this->isLogin();
        $list = (new CarCoupon())->get_user_bind_coupon_list($user['uid'],$coupon_type,1);
        $use_text = (new Car_coupon_explain())->get_use_text();
        if($footer == 'hidden') $this->site_title = '中国人寿综合服务平台';
        return $this->render('coupon', [
            'active'=>$coupon_type,
            'list' => $list,
            'use_text' => $use_text,
            'footer' => $footer
        ]);
    }

    /**
     * 卡券激活
     * @return string
     */
    public function actionAccoupon()
    {
        $request = Yii::$app->request;
        if ($request->isPost) {
            $pwd = $request->post('pwd', null);
            $pwd = trim($pwd);
            if (!$pwd) return $this->json(0, '请输入兑换码');
            $user = $this->isLogin();
            $fans = $this->fans_account();
            $user['mobile'] = $fans['mobile'];
            $couponObj = new CarCouponAction($user);
            $coupons = $couponObj->activate($pwd);
            if ($coupons === false) {
                return $this->json(0, $couponObj->msg);
            }
            if (empty($coupons)) {
                return $this->json(0, '严重错误，请联系客服！');
            }
            $couponModel = new CarCoupon();
            foreach ($coupons as $k => $val) $coupons[$k] = $couponModel->renderCouponList($val);
            return $this->json(1, 'ok', $coupons);
        }
        $this->menuActive = 'accoupon';
        //如果用户没有激活手机号码，需要先激活后才可以激活卡券
        $fans = $this->fans_account();
        $isbindmobile = ($fans && $fans['mobile']) ? 1 : 0;
        $footer = $request->get("footer",null);
        if($footer == 'hidden') $this->site_title = '中国人寿综合服务平台';
        return $this->render('accoupon', [
            'isbindmobile' => $isbindmobile,
            'footer' => $footer]);
    }
}