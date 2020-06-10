<?php

namespace frontend\controllers;

use common\components\BaiduMap;
use common\models\Car_coupon_explain;
use common\models\CarCommonAddress;
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

class CarecarController extends CloudcarController
{
    public $menuActive = 'carhome';

    public function beforeAction($action = null)
    {
        parent::beforeAction($action);
        return true;
    }

    public function actionIndex()
    {
        $request = Yii::$app->request;
        $user = $this->isLogin();
        $temp = (new CarCoupon())->get_user_bind_coupon_list($user['uid'], 1);
        $list = [];
        $now = time();
        foreach ($temp as $cp) {
            if ($cp['status'] == 1 && $now < $cp['use_limit_time']) {
                $list[] = $cp;
            }
        }
        $use_text = (new Car_coupon_explain())->get_use_text();
        return $this->render('index', ['list' => $list, 'use_text' => $use_text]);
    }

    public function actionGetnearbydrivers()
    {
        $request = Yii::$app->request;
        if (!$request->isPost) return '非法访问';
        $data = $request->post();
        $edr = new Eddriving();
        $edr->set_env(true);
        $res = $edr->get_nearby_drivers($data['lng'], $data['lat']);
        $list = $res['driverList'];
        $points = [];
        foreach ($list as $val) {
            $points[] = ['lng' => (float)$val['longitude'], 'lat' => (float)$val['latitude']];
        }
        return $this->json(1, 'ok', $points);
    }

    /**
     * 目的地页面
     */
    public function actionDestination()
    {
        $request = Yii::$app->request;
        //检索范围控制在当前所在的市
        $region = $request->get('region', null);
        //获得用户已经设置的两个常用地址
        $user = $this->isLogin();
        $list = (new CarCommonAddress())->get_user_address($user['uid']);
        return $this->render('destination', ['region' => $region, 'list' => $list]);
    }

    /**
     * 地址检索
     */
    public function actionSearch()
    {
        $request = Yii::$app->request;
        if (!$request->isPost) return '非法访问';
        $q = $request->post('q');
        $r = $request->post('r');
        $list = BaiduMap::search($q, $r);
        if (!$list) return $this->json(0);
        $html = $this->renderPartial('search', ['list' => $list]);
        return $this->json(1, 'ok', $html);
    }

    /**
     * 添加常用地址
     */
    public function actionEditloc()
    {
        $request = Yii::$app->request;
        if (!$request->isPost) return '非法访问';
        $data = $request->post();
        $user = $this->isLogin();
        $data['uid'] = $user['uid'];
        $id = (new CarCommonAddress())->update($data);
        if (!$id) return $this->json(0, '操作失败');
        return $this->json(1, 'ok');
    }

    public function actionEcar(){
        return $this->render('ecar');
    }
}