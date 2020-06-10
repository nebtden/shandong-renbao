<?php

namespace frontend\controllers;

use common\components\BaseController;
use Yii;
use frontend\util\FController;
use common\models\Shop_product;
use common\models\Keyword;
use common\models\Reply;
use common\models\Active;
use frontend\util\Wechat;

/**
 * 微信接口类
 * @author yinkp
 */
class WeixinController extends BaseController
{
    private $token;
    private $fun;
    private $data = array();
    private $my = '洗车卡';
    private $weixin = null;

    public function actionIndex()
    {
        $this->token = trim($_GET ['token']);
        if ($this->token) Yii::$app->session['token'] = $this->token;
        $this->weixin = new Wechat($this->token);
        $this->data = $this->weixin->request();//接收请求
        if ($this->data ['FromUserName']) Yii::$app->session['openid'] = $this->data ['FromUserName'];
        list ($content, $type) = $this->reply($this->data);
        $this->weixin->response($content, $type);//请求回复
    }

    /**
     * 触发回复
     * @param unknown $data
     * @return multitype:string
     */
    private function reply($data)
    {
        $oldData = $data;
        if ('CLICK' == $data ['Event']) {
            $data ['Content'] = $data ['EventKey'];
        }
        if ('subscribe' == $data ['Event']) {//关注
            $res = $this->weixin->addfans();
            //error_log(json_encode($res),3,APP_PATH.'/test2.txt');
            return $this->_specialReply($res);
        } elseif ('SCAN' == $data ['Event']) { // 已关注者扫描带参数二维码
            $res = $this->weixin->addfans();
            return $this->_specialReply($res);
        } elseif ('unsubscribe' == $data ['Event']) {//取消关注
            $this->weixin->cannelfans();
        }
        return $this->_keyword(trim($data ['Content']));//关键字回复处理
    }

    //特殊回复
    private function _specialReply($res)
    {
        if (is_array($res) && $res) {
            switch ($res[0]) {
                case 1:
                    return $this->_replyProNews($res[1]);//shop
                    break;
                case 2:
                    return $this->_replySchoolNews($res[2], $res[1]);//school
                    break;
            }
        } else {
            return $this->_replyKeyword('关注成功');
        }
    }

    /**
     * 处理关键字
     * @param string $key
     * @return array
     */
    private function _keyword($key = 'help')
    {
        switch ($key) {
            case 'help':
                return $this->_help();
                break;
            case 'KF':
            case 'kf':
                {
                    return array('', 'transfer_customer_service');
                    break;
                }
            default:
                {
                    return $this->_replyKeyword($key);
                }
        }
    }

    //产品图文推送
    private function _replyProNews($id)
    {
        $mode = new Shop_product();
        $where = "id='" . $id . "'";
        $proInfo = $mode->getData('id,proname,pic,sell_count,postage_status,standard,share_desc', 'one', $where);
        $arr = $brr = array();
        $arr[0] = $proInfo['proname'];
        $arr[1] = $proInfo['share_desc'];
        $pic = explode('|', $proInfo['pic']);
        $arr[2] = Yii::$app->params['url'] . $pic[0];
        $url = Yii::$app->params['url'] . Yii::$app->createUrl('/mobile/shop/ProDetail', array('pid' => $id, 'token' => $this->token));
        $arr[3] = $this->handleurl($url);
        $brr[] = $arr;
        return array($brr, 'news');
        exit;

    }

    //胎儿大学图文推送
    private function _replySchoolNews($uid, $sex)
    {
        $keyword = new Keyword();
        $data = $keyword->getData('*', 'one', 'token="' . Yii::$app->session['token'] . '" and  locate("胎儿大学",keyword)>0', 'createtime desc');
        if ($data) {
            $fid = $data['m_id'];
            $reply = new Reply();
            $arr = $brr = array();
            $data = $reply->getData('*', 'one', 'rid=' . $fid);
            $arr[0] = $data['title'];
            $arr[1] = $data['details'];
            $arr[2] = Yii::$app->params['url'] . $data['imageurl'];
            $url = Yii::$app->params['url'] . Yii::$app->createUrl('/mobile/shool/bind', array('uid' => $uid, 'sex' => $sex, 'token' => $this->token));
            $arr[3] = $this->handleurl($url);
            $brr[] = $arr;
            return array($brr, 'news');
        }
        exit;
    }

    //根据数据表关键字回复
    private function _replyKeyword($key)
    {
        $keyword = new Keyword();
        $data = $keyword->getData('*', 'one', 'token="' . Yii::$app->session['token'] . '" and  locate("' . $key . '",keyword)>0', 'createtime desc');
        if ($data) {
            $fid = $data['m_id'];
            $class = ucfirst($data['module']);
            if ($class == 'Reply') {
                $cmodle = new Reply();
                $list = $cmodle->getData('*', 'one', 'id=' . $fid);
                if ($list['type'] == 1) {
                    return array($list['details'], 'text');
                } else {
                    $list2 = $cmodle->getData('*', 'all', 'pid=' . $fid . ' or id=' . $fid . ' and token="' . Yii::$app->session['token'] . '"', '`order` asc');
                    $arr = array();
                    foreach ($list2 as $v) {
                        $arr[0] = $v['title'];
                        $arr[1] = $v['details'];
                        $arr[2] = Yii::$app->params['url'] . $v['imageurl'];
                        $arr[3] = $this->handleurl($v['url']);
                        $arr2[] = $arr;
                    }
                    return array($arr2, 'news');
                }
            } elseif ($class == 'Active') {
                $cmodle = new Active();
                $info = $cmodle->getData('title,covertext,dazhuanpanimg1,info', 'one', 'id=' . $data['m_id']);
                $arr[0] = $info['title'];
                $arr[1] = $info['covertext'];
                $arr[2] = Yii::$app->params['url'] . $info['dazhuanpanimg1'];
                $arr[3] = Yii::$app->params['url'] . '/mobile/lottery/index/token/' . Yii::$app->session['token'];
                $arr2[] = $arr;
                return array($arr2, 'news');
                exit;
            }
        } else {
            return $this->_help();
        }
    }

    /**
     * 回复帮助信息
     * @return array
     */
    private function _help()
    {
        return array(
            '欢迎您关注云车驾到！',
            'text'
        );
    }

    //链接地址处理
    private function handleurl($url)
    {
        $link = $url;
        if (strpos($url, 'http://') === false) {
            if (strpos($url, 'https://') === false) {
                $link = "http://" . $url;
            }
        }
        $link = str_replace('{token}', Yii::$app->session['token'], $link);
        $link = str_replace('{openid}', Yii::$app->session['openid'], $link);
        return $link;
    }
}