<?php

namespace frontend\controllers;

use common\components\Eddriving;
use common\models\CarCouponPackage;
use frontend\util\FController;
use Yii;
use common\models\User;
use common\components\W;
use frontend\util\PController;
use common\models\Car_rescueor;
use common\models\Car_oraddlog;
use common\models\Car_orstatuslog;
use common\models\Car_paternalor;


class YaNanController extends CloudcarController
{
    public $menuActive = 'carhome';
    public $site_title = '云车驾到';
    public $layout = 'cloudcarv2';

    public function actionIndex()
    {


        return $this->render('index');
    }


    /**
     * 鼎翰文化，彭亚南测试
     */
    public function actionLogo(){
        $user_auth = [
            'uid' => '4209',
            'nickname' => '鼎翰文化',
            'headimgurl' => 'http://www.yunche168.com/frontend/web/images/logo2.jpg',
            'sex' => 0,
            'pid' => 0
        ];
        Yii::$app->session['wx_user_auth'] = $user_auth;
        return $this->render('index');
    }


    /**
     * 鼎翰文化，彭亚南测试
     */
    public function actionToken(){
        Yii::$app->session['openid'] = null;
        Yii::$app->session['token'] =null;
        Yii::$app->session['wx_user_auth'] = null;
        Yii::$app->session['xxz_mobile'] = null;
//        return $this->render('index');

    }

    public function actionClearEdaijia(){
//        $fans = $this->fans_account();
        $phone = '18684698132';
        $edr = new Eddriving();
        $token = $edr->get_authen_token($phone,true);

        if ($token === false) {
           return "用户授权失败";
        }else{
            return "用户授权更新成功";

        }
        return $this->render('index');
    }


    public function actionPrint(){
        $user = $this->isLogin();
        $fans = $this->fans_account();
        print_r($user);
        var_dump("\n");
        echo "<br>";
        echo "<br>";
        echo "<br>";
        echo "<br>";
        print_r($fans);

    }

}
