<?php

namespace frontend\controllers;

use Yii;
use frontend\util\WController;
use yii\web\Response;
use common\components\AlxgBase;

class WebcloudcarController extends WController
{
    public $layout = "webcloudcar";
    public $site_title = '云车驾到';

    public function json($status = 1, $msg = 'ok', $data = [], $url = '', $waiting = 0)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return ['status' => $status, 'msg' => $msg, 'data' => $data, 'url' => $url, 'waiting' => $waiting];
    }

    public function beforeAction($action = null)
    {
        parent::beforeAction($action);
        if (!$this->isLogin()) $this->autoLogin();
        return true;
    }

    protected function isLogin()
    {
        $user_auth = Yii::$app->session['wx_user_auth_web'];

        if (!$user_auth) return false;
        return $user_auth;
    }

    protected function autoLogin()
    {
        $token = Yii::$app->session['token'];
        $mobile=Yii::$app->session['xxz_mobile'];
        $user = (new AlxgBase('fans', 'id'))->table()->select("id,nickname,headimgurl,sex,pid")->where(['mobile'=>$mobile,'source' => 'web端用户', 'token' => $token])->one();
        if ($user) {
            $user_auth = [
                'uid' => $user['id'],
                'nickname' => $mobile,
                'headimgurl' => $user['headimgurl'],
                'mobile' => $mobile,
                'sex' => $user['sex'],
                'pid' => $user['pid']
            ];
            Yii::$app->session['wx_user_auth_web'] = $user_auth;
        }
    }

    protected function fans_account($update = false)
    {
        $user = $this->isLogin();
        $cache = Yii::$app->cache;
        $key = Yii::$app->session['token'] . "fans_account" . $user['uid'];
        if ($update) {
            //将缓存更新掉
            $cache->delete($key);
            return true;
        }
        $ac = $cache->get($key);
        if ($ac === false) {
            $ac = (new AlxgBase('fans_account', 'id'))->table()->select('id,uid,pid,realname,mobile,company,duties,email,is_vip,score,status')->where(['uid' => $user['uid']])->one();
            if ($ac) {
                $cache->set($key, $ac, 60);
            } else {
                return [];
            }
        }
        return $ac;
    }

    //判断手机号码是否被绑定
    protected function mobile_is_exist($mobile = '', $self = 0)
    {
        $q = (new AlxgBase('fans_account', 'id'))->table()->where(['mobile' => $mobile,'is_web'=>1]);
        if ($self) $q->andWhere(['<>', 'uid', $self]);
        $c = $q->count();
        if ($c) return true;
        return false;
    }

}