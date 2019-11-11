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



    /*
     * 首页
     * */
    public function actionIndex(){
        return $this->render('index',[

        ]);
    }




}
