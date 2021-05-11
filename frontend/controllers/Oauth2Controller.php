<?php

namespace frontend\controllers;

use common\components\AlxgBase;
use common\models\Wxuser;
use Yii;
use yii\web\Controller;

class Oauth2Controller extends Controller
{
    public $enableCsrfValidation = false;

    public function actionAuthorize()
    {
        $token = Yii::$app->session['token'];
        $code = $_GET ['code'];
        $state = $_GET ['state'];
        $usr = Wxuser::getUsrByToken($token);
        $appId = $usr ['appId'];
        $secret = $usr ['appSecret'];
        //通过code 获取openid与accesstoken
        $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=' . $appId . '&secret=' . $secret . '&code=' . $code . '&grant_type=authorization_code';
        $info = json_decode(file_get_contents($url), true);

        $wxuser = ['openid' => $info['openid']];

        if ($info['scope'] == 'snsapi_userinfo') {
            $url = "https://api.weixin.qq.com/sns/userinfo?access_token=" . $info['access_token'] . "&openid=" . $info['openid'] . "&lang=zh_CN";
            $data = json_decode(file_get_contents($url), true);
            if(!isset($data['openid'])){
                return '用户信息获取失败';
            }
            $wxuser = $data;
        }

        $this->check_user($wxuser);

        Yii::$app->session['openid'] = $info['openid'];
        Yii::$app->response->redirect(Yii::$app->session['enterUrl'])->send();
    }

    protected function check_user($wx){
        $token = Yii::$app->session['token'];
        $openid = $wx['openid'];
        //查询是否有用户存在
        $fansModel = new AlxgBase('fans','id');
        $user = $fansModel->table()->where(['openid' => $openid,'token' => $token])->one();
        if(!$user){
            $user['token'] = $token;
            $user['openid'] = $openid;
            if(count($wx) > 1){
                $user['nickname'] = $wx['nickname'];
                $user['sex'] = $wx['sex'];
                $user['city'] = $wx['city'];
                $user['province'] = $wx['province'];
                $user['headimgurl'] = $wx['headimgurl'];
                $user['nickname'] = $wx['nickname'];
                $user['source'] = '网页授权';
                $user['subscribe_time'] = time();
            }
            $id = $fansModel->myInsert($user);
            if(!$id) return false;
        }
        return true;
    }
}