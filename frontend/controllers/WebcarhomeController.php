<?php

namespace frontend\controllers;

use common\components\BaiduMap;
use common\models\Car_indexad;
use common\models\Car_menu;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use yii\helpers\Url;
use common\components\Helpper;
use common\components\W;
use common\components\AlxgBase;
use frontend\util\WController;
use common\models\CarUserCarno;
use common\components\CarCouponAction;
use common\components\Eddriving;
use common\models\CarCoupon;
use common\models\CarNews;

class WebcarhomeController extends WebbaseController
{
    public $menuActive = 'carhome';
    public $layout = 'webcloudcarv2';

    public function beforeAction($action = null)
    {
        parent::beforeAction($action);
        return true;
    }

    public function actionIndex()
    {
        //获得首页菜单
        $menu = (new AlxgBase('car_menu', 'id'))->table()
            ->select("menu_name,web_menu_url,menu_img")
            ->where(['status' => 1])
            ->orderBy('sort asc')
            ->all();
        //猜你喜欢
        $likelist = (new Car_indexad())->get_list();
        $cookie = \Yii::$app->request->cookies;
        $mobile=$cookie->getValue('p_xxz_mobile');
        $s_mobile=Yii::$app->session['xxz_mobile'];

        $show=Yii::$app->session['isshow'];
        unset(Yii::$app->session['isshow']);
        $washCoupon = '';
        if( ! empty($s_mobile)){
            $show='no';
            $uid = Yii::$app->session['wx_user_auth_web']['uid'];
            $washCoupon = (new CarCoupon())->get_user_bind_coupon_list($uid, 4);
            $washCoupon = array_filter($washCoupon, function($v, $k) {
                return $v['show_coupon_all'] >0;
            }, ARRAY_FILTER_USE_BOTH);
        }
        $newsModel = new CarNews();
        $where = ['status'=>1];
        $res=$newsModel->page_list('*',$where, 'sort asc');
        $status=CarNews::$status;
        $newslist=$res['rows'];

        foreach ($newslist as $key=>$val){
            $newslist[$key]['c_time'] = !empty($val['c_time'])?date("Y-m-d H:i:s",$val['c_time']):'--';
            $newslist[$key]['status']=$status[$val['status']];

        }

        //代驾
        $temp = [];
        if(!empty($uid)) $temp = (new CarCoupon())->get_user_bind_coupon_list($uid, 1);
        $drivingCoupon = [];
        $now = time();
        foreach ($temp as $cp) {
            if ($cp['status'] == 1 && $now < $cp['use_limit_time']) {
                $drivingCoupon[] = $cp;
            }
        }
        $discoupon = (new CarCoupon())->get_user_bind_coupon_list($uid, 10);
        $disurl = $discoupon ? Url::to(['webcaruser/coupon']):Url::to(['webcaruser/accoupon']) ;
        return $this->render('index', [
            'menulist' => $menu,
            'show' => $show,
            'likelist' => $likelist,
            'new_list' => $newslist,
            'washCoupon'=>$washCoupon,
            'drivingCoupon'=>$drivingCoupon,
            'disurl'=>$disurl
        ]);
    }

//    public function actionTest()
//    {
//        $eddr = new Eddriving();
//        //$res = $eddr->coupon_bind('263232310922', '15574946648');
//        $res = $eddr->coupon_allinfo('263232310922','15574946648');
//        print_r($res);
//        echo "<hr>";
//        echo $eddr->errCode . ':' . $eddr->errMsg;
//    }

}