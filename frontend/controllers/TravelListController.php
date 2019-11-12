<?php

namespace frontend\controllers;


use frontend\util\PController;


/**
 * 用于用户下载
 * Class TravelListController
 * @package frontend\controllers
 */
class TravelListController extends PController
{

    public $site_title = '云车驾到';

    public $layout = 'travelpublic';
    /*

    /*
     * 首页
     * */
    public function actionIndex(){
        $request = \Yii::$app->request;
        $id = $request->get('id');
        return $this->render('index',[
            'id'=>$id
        ]);
    }


    public function actionClient(){
        $request = \Yii::$app->request;
        $id = $request->get('id');
        return $this->render('index',[
            'id'=>$id
        ]);
    }




}
