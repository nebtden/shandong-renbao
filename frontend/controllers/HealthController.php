<?php

namespace frontend\controllers;


use common\components\W;
use common\models\CarNews;
use common\components\AlxgBase;


class HealthController extends CloudcarController
{
    //    public $layout = "cloudcar";
    public $layout = false;
    public $site_title = '云车驾到';

    public function beforeAction($action = null)
    {
        parent::beforeAction($action);
        return true;
    }

    public function actionIndex()
    {
        $alxg_sign = W::getJSAPIShare(\Yii::$app->session['token']);
        return $this->render('index', [

            'alxg_sign' => $alxg_sign,
        ]);
    }

    public function actionDetail(){
        $alxg_sign = W::getJSAPIShare(\Yii::$app->session['token']);
        return $this->render('detail', [

            'alxg_sign' => $alxg_sign,
        ]);

    }



}