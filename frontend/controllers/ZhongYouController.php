<?php

namespace frontend\controllers;

use common\components\CarCouponAction;
use common\components\DiDi;
use common\components\W;
use common\components\BaseController;
use common\models\CallbackLog;
use common\models\Car_coupon_explain;
use common\models\Car_paternalor;
use common\models\CarCoupon;
use common\models\CarSubstituteDriving;
use common\models\Wxuser;
use Yii;


class ZhongYouController extends BaseController
{
//    public function beforeAction($action = null)
//    {
//        Yii::$app->session['token'] = $token = 'dhcarcard';
//        if(!Yii::$app->session['openid']){
//            W::getOpenid($token,'snsapi_base');
//        }
//    }

    public function actionIndex()
    {
        $token = 'dhcarcard';
        $base = 'snsapi_base';
//         W::getOpenid('dhcarcard');

        if(!Yii::$app->session['openId']){
            $curUrl = 'http://' . $_SERVER ['HTTP_HOST'] . $_SERVER ['REQUEST_URI'];
            //将当前进入的页面地址存起来，授权完成后再跳回
            Yii::$app->session['enterUrl'] = $curUrl;
            $usr = Wxuser::getUsrByToken($token);
            $appId = $usr ['appId'];

            //授权后回跳地址
            $reUrl = urlencode("http://" . $_SERVER['HTTP_HOST'] . '/frontend/web/zhong-you/auth.html');
            $url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=' . $appId . '&redirect_uri=' . $reUrl . '&response_type=code&scope=' . $base . '&state=state#wechat_redirect';

            Yii::$app->response->redirect($url)->send();
        }else{
           $openId =  Yii::$app->session['openId'];
            $url = 'http://it.yunche168.com/zhongyou/qa.html?open_id='.$openId;
            header('Location:'.$url);
            die;
        }



    }



    public function actionAuth(){

        $token = 'dhcarcard';
        $code = $_GET ['code'];
        $state = $_GET ['state'];
        $usr = Wxuser::getUsrByToken($token);
        $appId = $usr ['appId'];
        $secret = $usr ['appSecret'];
        //通过code 获取openid与accesstoken
        $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=' . $appId . '&secret=' . $secret . '&code=' . $code . '&grant_type=authorization_code';
        $info = json_decode(file_get_contents($url), true);

        $openId = $info['openid'];
        Yii::$app->session['openId'] = $openId;


        Yii::$app->response->redirect(Yii::$app->session['enterUrl'])->send();
        return $info;
    }

    public function actionAuth2(){

        $token = 'dhcarcard';
        $code = $_GET ['code'];
        $state = $_GET ['state'];
        echo $code;
        die();
        $usr = Wxuser::getUsrByToken($token);
        $appId = $usr ['appId'];
        $secret = $usr ['appSecret'];
        //通过code 获取openid与accesstoken
        $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=' . $appId . '&secret=' . $secret . '&code=' . $code . '&grant_type=authorization_code';
        $info = json_decode(file_get_contents($url), true);

        $openId = $info['openid'];
        Yii::$app->session['openId'] = $openId;


        Yii::$app->response->redirect(Yii::$app->session['enterUrl'])->send();
        return $info;
    }

    public function actionTest()
    {
        $token = 'dhcarcard';
        $base = 'snsapi_base';
//         W::getOpenid('dhcarcard');


            $curUrl = 'http://' . $_SERVER ['HTTP_HOST'] . $_SERVER ['REQUEST_URI'];
            //将当前进入的页面地址存起来，授权完成后再跳回
            Yii::$app->session['enterUrl'] = $curUrl;
            $usr = Wxuser::getUsrByToken($token);
            $appId = $usr ['appId'];

            //授权后回跳地址
            $reUrl = urlencode( 'http://www.yunche168.com/backend/web/login.html');
            $url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=' . $appId . '&redirect_uri=' . $reUrl . '&response_type=code&scope=' . $base . '&state=state#wechat_redirect';
            echo $url;
            die();

            Yii::$app->response->redirect($url)->send();



    }

}
