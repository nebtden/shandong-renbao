<?php
namespace frontend\controllers;

use Yii;
use yii\web\Response;
use frontend\util\FController;
use common\models\Fans;
//基类控制器
class FcarcardController extends FController{
    public $layout = "main";
    public $sitetitle = '云车驾到';
    //入口
    //判断用户是否关注了公众号，如果没有则去关注公众号
    //将用户信息写入session;
    protected function checkUser(){

        $session = Yii::$app->session;
        $user_auth = $session['wx_user_auth'];//用户登录信息
        $openid = $session['openid'];
        $token = $session['token'];
        //判断用户是否已登录
        $fans = (new Fans())->select('id,token,openid,nickname,headimgurl,sex,pid,status',['openid'=>$openid])->one();


        if(!$fans){
            //没有相应用户或状态不为关注，跳转到关注页面
            return $this->redirect(['site/index']);

        }
        if(!$user_auth){
            //没有登录，通过openid取用户，可同时判断用户是否关注公众号
            $user_auth = [
                'uid'=>$fans['id'],
                'nickname'=>$fans['nickname'],
                'headimgurl'=>$fans['headimgurl'],
                'sex'=>$fans['sex'],
                'pid'=>$fans['pid'],
            ];
            //已关注，将用户信息写入session
            $session->set('wx_user_auth',$user_auth);
        }
        return true;
    }

    public function beforeAction($action = null){
        parent::beforeAction($action);
        $this->checkUser();
        return true;
    }
}