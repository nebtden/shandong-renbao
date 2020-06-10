<?php

namespace frontend\controllers;


use common\components\W;
use common\models\CarNews;
use common\components\AlxgBase;


class CarNewsController extends CloudcarController
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
        $newsModel = new CarNews();
        $newslist = $newsModel->table()->select("*")->where()->all();

        return $this->render('index', [ 'list' => $newslist]);
    }

    public function actionDetail(){

        $alxg_sign = W::getJSAPIShare(\Yii::$app->session['token']);
        $id = \Yii::$app->request->get('id', 0);
        $newsModel = new CarNews();
        $newsinfo = $newsModel->table()->select("*")->where(['id'=>intval($id)])->one();

        return $this->render('detail', [
            'detail' => $newsinfo,
            'alxg_sign' => $alxg_sign,
        ]);
    }



}