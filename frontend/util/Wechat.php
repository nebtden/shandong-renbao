<?php

namespace frontend\util;

use common\models\Wxuser;
use think\Exception;
use Yii;
use common\models\Fans;
use common\models\Wx_card;
use common\components\W;

/**
 * 微信通讯类
 * @author yinkp
 */
class Wechat
{
    private $data = array();
    private $appid = '';
    private $appsecret = '';
    private $accessToken = null;
    private $token = '';

    public function __construct($token)
    {
        $this->auth($token) || die ();
        if ($_GET ['echostr']) {
            echo $_GET ['echostr'];
            die ();
        } else {
            //接收微信请求输入数据.从xml转成array
            $this->token = $token;
            $xml = file_get_contents('php://input');
            $xml = new \SimpleXMLElement  ($xml);
            $xml || die ();
            foreach ($xml as $key => $value) {
                $this->data [$key] = strval($value);
            }
            //@file_put_contents("./abc.log",json_encode($this->data,JSON_UNESCAPED_UNICODE));
            $usr = Wxuser::getUsrByToken($token);
            $this->appid = $usr ['appId'];
            $this->appsecret = $usr ['appSecret'];
        }

    }

    //接收请求
    public function request()
    {
        return $this->data;
    }

    //返回xml字符串流给腾讯
    public function response($content, $type = 'text', $flag = 0)
    {

        $this->data = array(
            'ToUserName' => $this->data ['FromUserName'],
            'FromUserName' => $this->data ['ToUserName'],
            'CreateTime' => time(),
            'MsgType' => $type
        );
        if ($type != 'transfer_customer_service') {
            $this->{$type} ($content);
            $this->data ['FuncFlag'] = $flag;
        }
//        $xml = new \SimpleXMLElement ('<xml></xml>');
//        $this->data2xml($xml, $this->data);
//        die ($xml->asXML());
        $xmldata = $this->xml_encode($this->data);
        echo $xmldata;
    }

    public static function xmlSafeStr($str)
    {
        return '<![CDATA['.preg_replace("/[\\x00-\\x08\\x0b-\\x0c\\x0e-\\x1f]/",'',$str).']]>';
    }

    /**
     * 数据XML编码
     * @param mixed $data 数据
     * @return string
     */
    public static function data_to_xml($data) {
        $xml = '';
        foreach ($data as $key => $val) {
            is_numeric($key) && $key = "item id=\"$key\"";
            $xml    .=  "<$key>";
            $xml    .=  ( is_array($val) || is_object($val)) ? self::data_to_xml($val)  : self::xmlSafeStr($val);
            list($key, ) = explode(' ', $key);
            $xml    .=  "</$key>";
        }
        return $xml;
    }

    /**
     * XML编码
     * @param mixed $data 数据
     * @param string $root 根节点名
     * @param string $item 数字索引的子节点名
     * @param string $attr 根节点属性
     * @param string $id   数字索引子节点key转换的属性名
     * @param string $encoding 数据编码
     * @return string
     */
    public function xml_encode($data, $root='xml', $item='item', $attr='', $id='id', $encoding='utf-8') {
        if(is_array($attr)){
            $_attr = array();
            foreach ($attr as $key => $value) {
                $_attr[] = "{$key}=\"{$value}\"";
            }
            $attr = implode(' ', $_attr);
        }
        $attr   = trim($attr);
        $attr   = empty($attr) ? '' : " {$attr}";
        $xml   = "<{$root}{$attr}>";
        $xml   .= self::data_to_xml($data, $item, $id);
        $xml   .= "</{$root}>";
        return $xml;
    }

    /**
     * 粉丝入库
     * @param string $token
     * @return boolean,
     */
    public function addFans()
    {
        $pid = $this->data ['EventKey'];
        $prr = array();
        //error_log(json_encode($this->data),3,APP_PATH.'/test.txt');
        if (strpos($pid, 'qrscene_') === false) {
            $pid = 0;
        } else {
            $pid = str_replace('qrscene_', '', $pid);
        }
        //error_log(json_encode($prr),3,APP_PATH.'/prr.txt');
        $source = '扫描二维码';
        $fans = Fans::checkFans($this->token, $this->data['FromUserName']);
        $wxUsr = W::getUserInfo($this->token, $this->data['FromUserName']);
        if (!$fans) {
            $card = Wx_card::getCard($this->token);
            //error_log(json_encode($card),3,APP_PATH.'/prr.txt');
            $newFans = array(
                'token' => $this->token,
                'openid' => $this->data['FromUserName'],
                'nickname' => $wxUsr['nickname'],
                'sex' => $wxUsr['sex'],
                'city' => $wxUsr['city'],
                'province' => $wxUsr['province'],
                'headimgurl' => $wxUsr['headimgurl'],
                'subscribe_time' => $wxUsr['subscribe_time'],
                'remark' => $wxUsr['remark'],
                'groupid' => $wxUsr['groupid'],
                'status' => Fans::$statusArr['SUCCESS'],
                'card' => $card
            );
            if ($pid) {
                $prow = Fans::checkcard($this->token, $pid);
                $this->sendTplInvitedInfo($wxUsr, $prow);
            } else {
                $pid = 0;
                $source = '直接关注';
            }
            $newFans['pid'] = $pid;
            $newFans['source'] = $source;
            $flag = Fans::addFans($newFans);
            $wxUsr['id'] = $flag;
            $this->sendTplInfo($wxUsr);

        } elseif ($fans['status'] != Fans::$statusArr['SUCCESS']) {

            if ($fans['id'] == $pid) $pid = 0;//如果pid是自己的id则置为0，不允许出现自己推荐自己
            $field2val = array('status' => Fans::$statusArr['SUCCESS']);
            $field2val['nickname'] = $wxUsr['nickname'];
            $field2val['sex'] = $wxUsr['sex'];
            $field2val['city'] = $wxUsr['city'];
            $field2val['province'] = $wxUsr['province'];
            $field2val['headimgurl'] = $wxUsr['headimgurl'];
            $field2val['remark'] = $wxUsr['remark'];
            $field2val['groupid'] = $wxUsr['groupid'];

            if ($fans['status'] == Fans::$statusArr['CANNEL']) {//取消关注者重新关注
                if (empty($fans['pid']) && $pid) {
                    $field2val['pid'] = $pid;
                } else {
                    $field2val['pid'] = 0;
                }
            } elseif ($fans['status'] == Fans::$statusArr['FAIL']) {//未关注者（点过链接）重新关注
                if (empty($fans['pid']) && $pid) {
                    $field2val['pid'] = $pid;
                } else {
                    $field2val['pid'] = 0;
                }
            }
            //真实上线
            $this->sendTplInfo($fans);
            $field2val['status'] = 1;
            $flag = Fans::updateFans($field2val, "id = '{$fans['id']}'");
        } elseif ($fans['status'] == Fans::$statusArr['SUCCESS']) {
            $flag = true;
        }
        //$res=$flag?$flag:false;
        return $prr ? $prr : $flag;
    }

    //发关注成功模板消息
    private function sendTplInfo($wxUsr)
    {
        //发模板消息
        $linkurl = Yii::$app->params['url'] . '/mobile/member/member/token/' . Yii::$app->session['token'];
        $params = array(
            'first' => '欢迎[' . $wxUsr['nickname'] . ']加入益百年健康科技大家庭！请进入会员中心验证手机号享受更多服务！',
            /*'keyword1' => $card,*/
            'keyword1' => $wxUsr['nickname'],
            'keyword2' => $wxUsr['id'],
            'keyword3' => date('2017-m-d H:i:s'),
            'remark' => '欢迎加入！详情请咨询4006043888'
        );
        $flag = W::sendTpl(Yii::$app->session['token'], Yii::$app->session['openid'], 'memberRegSuccess', $params);
    }

    //邀请下线关注成功模板消息
    private function sendTplInvitedInfo($wxUsr, $pusr)
    {
//发模板消息
        $linkurl = Yii::$app->params['url'] . '/mobile/member/member/token/' . Yii::$app->session['token'];
        $params = array(
            'first' => '您好，会员[' . $wxUsr['nickname'] . ']已接受您的邀请关注益百年健康科技，成为您的正式益友！',
            /*'keyword1' => $card,*/
            'keyword1' => $wxUsr['nickname'],
            'keyword2' => date('Y-m-d H:i:s'),
            'keyword3' => $pusr['nickname'],
            'remark' => '详询4006043888'
        );
        $flag = W::sendTpl(Yii::$app->session['token'], $pusr['openid'], 'AskMemberSuccess', $params);
    }

    private function fansActiveInfo($wnickname, $pnickname, $popenid)  //1
    {
        $linkurl = Yii::$app->params['url'] . '/mobile/member/member/token/' . Yii::$app->session['token'];
        $params = array(
            'first' => '您好，' . $pnickname . '!',
            'keyword1' => '会员激活通知',
            'keyword2' => date('Y-m-d H:i:s'),
            'remark' => '您的益友[' . $wnickname . ']已再次关注益百年健康科技!您可进入益友名单中查询。'//您有一下线会员['.$wnickname.']刚进行了激活!'
        );
        $flag = W::sendTpl(Yii::$app->session['token'], $popenid, 'MemberRemind', $params);
    }

    private function fansActiveInfo_2($wxUsr, $pusr, $fusr)  //2
    {
        $linkurl = Yii::$app->params['url'] . '/mobile/member/member/token/' . Yii::$app->session['token'];
        $params = array(
            'first' => '您好，' . $fusr['nickname'] . '!',
            'keyword1' => '会员激活感谢通知',
            'keyword2' => date('Y-m-d H:i:s'),
            'remark' => '感谢您重新激活[' . $wxUsr['nickname'] . ']的会员身份！该会员是[' . $pusr['nickname'] . ']所邀请的益友。'
            //通过扫描您的二维码成功激活了会员['.$wxUsr['nickname'].'],该会员是['.$pusr['nickname'].']的未激活下线会员！'
        );
        $flag = W::sendTpl(Yii::$app->session['token'], $fusr['openid'], 'MemberRemind', $params);
    }

    /**
     * 粉丝取消关注
     * @param string $token
     * @return boolean,
     */
    public function cannelfans()
    {
        $fans = Fans::checkFans($this->token, $this->data['FromUserName']);
        if ($fans) {
            return Fans::updateFans(array('status' => Fans::$statusArr['CANNEL'], 'pid' => 0,), "id = '{$fans['id']}'");
        }
        return false;
    }

    //文本
    private function text($content)
    {
        $this->data ['Content'] = $content;
    }

    //音乐
    private function music($music)
    {
        list ($music ['Title'], $music ['Description'], $music ['MusicUrl'], $music ['HQMusicUrl']) = $music;
        $this->data ['Music'] = $music;
    }

    //新闻
    private function news($news)
    {
        $articles = array();
        foreach ($news as $key => $value) {
            list ($articles [$key] ['Title'], $articles [$key] ['Description'], $articles [$key] ['PicUrl'], $articles [$key] ['Url']) = $value;
            if ($key >= 9) {
                break;
            }
        }
        $this->data ['ArticleCount'] = count($articles);
        $this->data ['Articles'] = $articles;
    }

    //数组转xml
    private function data2xml($xml, $data, $item = 'item')
    {
        foreach ($data as $key => $value) {
            is_numeric($key) && ($key = $item);
            if (is_array($value) || is_object($value)) {
                $child = $xml->addChild($key);
                $this->data2xml($child, $value, $item);
            } else {
                if (is_numeric($value)) {
                    $child = $xml->addChild($key, $value);
                } else {
                    $child = $xml->addChild($key);
                    $node = dom_import_simplexml($child);
                    $node->appendChild($node->ownerDocument->createCDATASection($value));
                }
            }
        }
    }

    /**
     * token验证
     * @param unknown $token
     * @return boolean
     */
    private function auth($token)
    {
        $data = array(
            $_GET ['timestamp'],
            $_GET ['nonce'],
            $token
        );
        $sign = $_GET ['signature'];
        sort($data, SORT_STRING);
        $signature = sha1(implode($data));
        return $signature === $sign;
    }
}