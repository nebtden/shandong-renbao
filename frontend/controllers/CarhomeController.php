<?php

namespace frontend\controllers;

use common\components\BaiduMap;
use common\models\Car_banner;
use common\models\Car_indexad;
use common\models\Car_menu;
use common\models\CarNews;
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

class CarhomeController extends CloudcarController
{
    public $menuActive = 'carhome';
    //    public $layout = "cloudcar";
    public $layout = "cloudcarv2";
    public $site_title = '云车驾到';

    public function beforeAction($action = null)
    {
        parent::beforeAction($action);
        return true;
    }


    public function actionIndex()
    {
        $uid = Yii::$app->session['wx_user_auth']['uid'] ? Yii::$app->session['wx_user_auth']['uid'] : '-1' ;

        //在首页列出所有的代驾优惠券
        $companys = (new CarCoupon())->getDrivingCompanyList($uid);
        //获得轮播
        $bannerList = Car_banner::find()->where('status=1')->orderBy('sort DESC')->limit(10)->all();

        $session = Yii::$app->session;
        $session->set('carId',0);
        $session->set('couponid',0);
        $session->set('segid',0);
        //$user = $this->isLogin();
        //获得首页菜单
        $menu = (new AlxgBase('car_menu', 'id'))->table()
            ->select("menu_name,menu_url,menu_img")
            ->where(['status' => 1])
            ->orderBy('sort asc')
            ->all();
        //猜你喜欢
        $likelist = (new Car_indexad())->get_list();
        $likelist=array();
        $newsModel = new CarNews();
        $where = ['status'=>1];
        $res=$newsModel->page_list('*',$where, 'sort asc');
        $status=CarNews::$status;
        $newslist=$res['rows'];
        foreach ($newslist as $key=>$val){
            $newslist[$key]['c_time'] = !empty($val['c_time'])?date("Y-m-d H:i:s",$val['c_time']):'--';
            $newslist[$key]['status']=$status[$val['status']];

        }
        $res['rows']=$newslist;
        $washCoupon = (new CarCoupon())->get_user_bind_coupon_list($uid, 4);
        $washCoupon = array_filter($washCoupon, function($v, $k) {
            return $v['show_coupon_all'] >0;
        }, ARRAY_FILTER_USE_BOTH);

        $temp = (new CarCoupon())->get_user_bind_coupon_list($uid, 1);
        $drivingCoupon = [];
        $now = time();
        foreach ($temp as $cp) {
            if ($cp['status'] == 1 && $now < $cp['use_limit_time']) {
                $drivingCoupon[] = $cp;
            }
        }
        $discoupon = (new CarCoupon())->get_user_bind_coupon_list($uid, 10);
        $disurl = $discoupon ? Url::to(['caruser/coupon']):Url::to(['caruser/accoupon']) ;

        return $this->render('index', [
            'menulist' => $menu,
            'bannerList'=>$bannerList,
            'likelist' => $likelist,
            'new_list'=>$newslist,
            'washCoupon'=>$washCoupon,
            'drivingCoupon'=>$drivingCoupon,
            'disurl'=>$disurl
        ]);
    }

    public function actionIndexv2()
    {



    }

}