<?php

namespace frontend\controllers;


use common\models\TravelList;
use frontend\util\PController;


/**
 * 用于用户下载
 * Class TravelListController
 * @package frontend\controllers
 */
class TravelListController extends PController
{

    public $site_title = '太平洋保险';

    public $layout = 'travelpublic';
    /*

    /*
     * 首页
     * */
    public function actionIndex(){
        $request = \Yii::$app->request;
        $id = $request->get('id');

        $info = TravelList::findOne($id);
        $this->site_title = $this->site_title.'--'.$info->title.'--'.$info->title2;

        return $this->render('index',[
            'id'=>$id,

        ]);
    }


    /**
     * @return string
     * 详情页
     */
    public function actionClient(){
        $request = \Yii::$app->request;
        $id = $request->get('id');
        return $this->render('client',[
            'id'=>$id
        ]);
    }




}
