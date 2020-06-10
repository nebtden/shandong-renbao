<?php
namespace frontend\controllers;

use common\models\Car_banner;
use common\models\CarNews;
use Yii;
use yii\web\Controller;
use common\components\AlxgBase;
use common\models\Car_indexad;
use common\models\CarCoupon;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public $menuActive = 'carhome';
//    public $layout = "cloudcar";
    public $layout = "cloudcarv2";
    public $site_title = '云车驾到';
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }


    public function actionIndex()
    {
        $session = Yii::$app->session;
        $session->set('carId',0);
        $session->set('couponid',0);
        $session->set('segid',0);
        //$user = $this->isLogin();
        //获得轮播
        $bannerList = Car_banner::find()->where('status=1')->orderBy('sort DESC')->limit(10)->all();
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
        $uid = Yii::$app->session['wx_user_auth']['uid'];

        $washCoupon = (new CarCoupon())->get_user_bind_coupon_list($uid, 4);

        return $this->render('/carhome/index', ['menulist' => $menu,'bannerList'=>$bannerList, 'likelist' => $likelist,'new_list'=>$newslist,'washCoupon'=>$washCoupon]);
    }




}
