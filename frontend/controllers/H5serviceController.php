<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2019\4\30 0030
 * Time: 13:11
 */

namespace frontend\controllers;

use common\components\BaseController;
use common\components\CarCouponAction;
use common\models\Car_wash_carno;
use common\models\CarCoupon;
use common\models\CarPcr;
use Yii;
use yii\helpers\Url;

class H5serviceController extends WebcloudcarController
{
    public $menuActive = 'carhome';
    public $layout = 'bank_h5';
    public $title = '一键洗车';
    protected $user;

    public function beforeAction($action = null)
    {
       // parent::beforeAction($action);
        $session = Yii::$app->session;
        $this->user = $session['wx_user_auth_web'];
        //获取客户端cookies,如果没有，则重新登录
        $cookies = Yii::$app->request->cookies;
        $p_xxz_mobile = $cookies->getValue('p_xxz_mobile');
        if(!$p_xxz_mobile){
            $this->user = '';
        }else{
            //获取session,如果没有session,则自动登录
            $this->user = $session['wx_user_auth_web'];
            if(!$this->user){
                $this->autoLogin();
                $this->user = $this->isLogin();
            }
        }

        return true;
    }

    public function actionGuoshouwash()
    {
        $this->title = '一键洗车 - 首页';
        $washCoupon = [];
        if(!empty($this->user['uid'])){
            $washCoupon = (new CarCoupon())->get_user_bind_coupon_list($this->user['uid'], 4);
            $washCoupon = array_filter($washCoupon, function($v, $k) {
                return $v['show_coupon_all'] >0;
            }, ARRAY_FILTER_USE_BOTH);
        }
        return $this->render('guoshouwash',[
            'user' => $this->user,
            'washCoupon' => $washCoupon
        ]);
    }

    public function actionAccoupon()
    {
        $request = Yii::$app->request;
        if($request->isPost){
            $card_province = $request->post('card_province',null);
            $card_char = $request->post('card_char',null);
            $card_no = $request->post('card_no',null);
            $card_no = strtoupper(trim($card_no));
            if(!$card_no){
                return $this->json(0,'请输入车牌号');
            }
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
            $now = time();
            $cardData = [
                'uid' => $user['uid'],
                'mobile' => $user['mobile'],
                'card_province' => $card_province,
                'card_char' => $card_char,
                'card_no' => $card_no,
                'c_time' => $now,
                'u_time' => $now,
            ];
            $res = (new Car_wash_carno())->myInsert($cardData);
            return $this->json(1, 'ok');
        }
        $cardNo = Car_wash_carno::find()->where(['uid'=>$this->user['uid']])->asArray()->one();
        $province = CarPcr::find()->where(['pid'=>0])->asArray()->all();
        $letter =CarPcr::find()->groupBy('desc')->where(['pid' => 1])->asArray()->all();
        $user = $this->user;
        return $this->render('accoupon',compact('user','province','letter','cardNo'));
    }



}