<?php

namespace frontend\controllers;

use common\components\CarCouponAction;
use common\components\CExcel;
use common\models\Car_paternalor;
use common\models\CarCoupon;
use common\models\CarCouponPackage;
use common\models\CarMobile;
use common\models\CarSubstituteDriving;
use common\models\ErrorLog;
use common\models\FansAccount;
use Yii;
use JMessage\JMessage;
use common\components\EDaiJia;
use frontend\util\PController;
use GuzzleHttp\Client;

class TravelUsersController extends PController
{

    public $site_title = '云车驾到';
    public $layout = 'travelpublic';

    /*
     * 首页
     * */
    public function actionIndex(){
        return $this->render('index',[

        ]);
    }




}
