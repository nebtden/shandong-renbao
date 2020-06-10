<?php

namespace frontend\controllers;

use common\models\FansAccount;
use Yii;
use frontend\util\FController;
use yii\web\Response;
use common\components\AlxgBase;

class CloudcarController extends FController
{
    public $layout = "cloudcar";
    public $site_title = '云车驾到';
    public $webUrl = null;


    public function json($status = 1, $msg = 'ok', $data = [], $url = '', $waiting = 0)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return ['status' => $status, 'msg' => $msg, 'data' => $data, 'url' => $url, 'waiting' => $waiting];
    }

    public function beforeAction($action = null)
    {
        parent::beforeAction($action);
        if (isset($_GET['hfive'])) {
            if ($_GET['hfive']) {
                //ecar,savecar
                Yii::$app->session['car_h5'] = $_GET['hfive'];
            }
        }
        if (!$this->isLogin()) $this->autoLogin();

        //更新最后的时间
        $user = $this->isLogin();
        $obj = FansAccount::find()->where(['uid' => $user['uid']])->one();
        $obj->u_time = time();
//        $obj->save();

        $this->getLayout();
        return true;
    }

    /**
     * 根据cookies判断微信端和web端
     * @return string
     */
    public function getLayout()
    {
        if($this->is_web){
            return $this->webUrl = 'web';
        }
        return true;
    }

    protected function isLogin()
    {
        //如果是web端传入，判断session['wx_user_auth']是否为web用户，否则重新设置
        if($this->is_web && Yii::$app->session['wx_user_auth']['nickname'] != $this->is_web){
            $this->autoLogin();
        }
        $user_auth = Yii::$app->session['wx_user_auth'];
        if (!$user_auth) return false;
        return $user_auth;
    }

    protected function autoLogin()
    {
        $token = Yii::$app->session['token'];
        //如果是WEB端客户就用fans_account表查询 否则用fans表
        if($this->is_web){
            $mobile=Yii::$app->session['xxz_mobile'];
            $user = (new AlxgBase('fans_account', 'id'))->table()->select("id,uid,pid")->where(['mobile'=>$mobile,'is_web' => 1])->one();
        } else {
            $openid = Yii::$app->session['openid'];
            $user = (new AlxgBase('fans', 'id'))->table()->select("id,nickname,headimgurl,sex,pid,sid")->where(['openid' => $openid, 'token' => $token])->one();
            $user_ans_account = (new AlxgBase('fans_account', 'id'))->table()->select("mobile")->where(['uid' => $user['id']])->one();
        }
        if ($user) {
            $user_auth = [
                'uid' => $this->is_web?$user['uid']:$user['id'],      //web端用fans_account表中uid，微信端用fans表中id,
                'nickname' => $user['nickname']?:$mobile,
                'headimgurl' => $user['headimgurl'],
                'sex' => $user['sex'],
                'pid' => $user['pid'],
                'sid' => $user['sid']?:0,
                'mobile' => $user_ans_account['mobile'],
            ];
            Yii::$app->session['wx_user_auth'] = $user_auth;
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
        $q = (new AlxgBase('fans_account', 'id'))->table()->where(['mobile' => $mobile, 'is_web' => 0]);
        if ($self) $q->andWhere(['<>', 'uid', $self]);
        $c = $q->count();
        if ($c) return true;
        return false;
    }

    //判断店铺是否通过审核
    //判断是否已经成为店主
    protected function is_shoper($uid = 0)
    {
        $map['uid'] = $uid;
        $info = (new AlxgBase('car_shop', 'id'))->table()->select("id,shop_status")->where($map)->one();
        if ($info) return $info;
        return false;
    }
}