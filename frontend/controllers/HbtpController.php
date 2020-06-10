<?php

namespace frontend\controllers;

use common\models\CarWashpay;
use Yii;
use yii\web\Response;
use yii\helpers\Url;
use common\components\W;

class HbtpController extends CloudcarController
{

    public function beforeAction($action = null)
    {
        parent::beforeAction($action);
        return true;
    }

    public function actionIndex()
    {
        return $this->renderPartial('index');
    }
}