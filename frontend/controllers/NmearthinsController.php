<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019\2\26 0026
 * Time: 10:32
 */

namespace frontend\controllers;

use common\components\W;
use common\models\CarCoupon;
use Yii;

class NmearthinsController extends CloudcarController
{
    public $layout = 'nmearthins';
    public $site_title = '大地保险内蒙古车后服务';
    const STATIC_PATH = '/frontend/web/nmearthins/';
    private $user;

    public function beforeAction($action = null)
    {
        parent::beforeAction($action);
        $this->user = $this->fans_account();;
        return true;
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionNmins()
    {
        $this->site_title = '代办年检';
        return $this->render('nmins');
    }

    public function actionNmsafe()
    {
        $this->site_title = '安全检测';
        return $this->render('nmsafe');
    }

    public function actionNmwash()
    {
        $this->site_title = '清洗检测';
        $uid = Yii::$app->session['wx_user_auth']['uid'];
        $washCoupon = (new CarCoupon())->get_user_bind_coupon_list($uid, 4);
        $washCoupon = array_filter($washCoupon, function ($v, $k) {
            return $v['show_coupon_all'] > 0 && $v['company'] = COMPANY_SHENGDA_WASH;
        }, ARRAY_FILTER_USE_BOTH);
        return $this->render('nmwash', [
            'user' => $this->user,
            'washCoupon' => $washCoupon,
        ]);
    }
}