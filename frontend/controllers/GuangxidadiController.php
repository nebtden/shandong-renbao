<?php

namespace frontend\controllers;

use common\components\BaiduMap;
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

class GuangxidadiController extends CloudcarController
{
    public $menuActive = 'carhome';
    public $layout = false;
    public $site_title = '云车驾到';

    public function beforeAction($action = null)
    {
        parent::beforeAction($action);
        return true;
    }


    public function actionIndex()
    {
        $user = $this->fans_account();
        return $this->render('index', ['user' => $user]);
    }

    public function actionActive()
    {
        $user = $this->isLogin();
        return $this->render('active',['user' => $user]);
    }

}