<?php

namespace common\components;

use common\models\Lottery_order;
use common\models\Payment;
use common\models\Wxuser;
use Yii;
use yii\base\Component;
use yii\db\Exception;

/**
 * 基本功能
 */
class W extends Component
{
    // 加解密


    public static function authcode($data, $operation = 'DECODE')
    {
        if ($operation == 'DECODE') {
            $data = substr($data, 0, 2) . substr($data, 8, -3);
            return self::base64Decode($data);
        }
        $salt = substr(self::base64Encode('fasd!313144'), 0, 6);
        $data = self::base64Encode($data);
        $data = substr($data, 0, 2) . $salt . substr($data, 2) . '==w';
        return $data;
    }

    // base64URL加密
    public static function base64Encode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    // base64URL解密
    public static function base64Decode($data)
    {
        return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
    }

    // 返回页面请求
    public static function Q()
    {
        return \Yii::$app->getRequest();
    }

    // 返回数据库实例
    public static function D()
    {
        return \Yii::$app->getDb();
    }

    // 返回用户实例
    public static function U()
    {
        return \Yii::$app->getUser();
    }

    // 网站根目录地址
    public static function G()
    {
        return \Yii::$app->baseUrl;
    }

    // 获取id
    public static function I()
    {
        return \Yii::$app->db->getLastInsertID();
    }

    // 创建数据对象,
    public static function C($sql = '', $query = 'all')
    {
        if ($query == 'up')
            return \Yii::$app->db->createCommand($sql)->execute();
        else {
            $query = 'query' . ucfirst($query);
            return \Yii::$app->db->createCommand($sql)->$query();
        }
    }

    //事物处理
    public static function constraint($sqlArr)
    {
        $connection = \Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            foreach ($sqlArr as $sql) {
                $connection->createCommand($sql)->execute();
            }
        } catch (Exception $e) {        // 如果有一条查询失败，则会抛出异常
            $transaction->rollBack();
            return false;
        }
        $transaction->commit();
        return true;
    }

    /**
     * 生成编码
     *
     * @param unknown $type
     *            eg:'token' ,'cardNum','orderCode'
     * @param unknown $length
     */
    public static function createNumber($type, $length)
    {
        $code = uniqid() . rand(1, 1000);
        switch ($type) {
            case 'token' :
                { //公众号标识
                    $code = 'to' . $code;
                }
                break;
            case 'cardNum' :
                { //会员卡号
                    $code = 'ca' . $code;
                }
                break;
            case 'proCode' :
                { //产品编号
                    $code = 'po' . $code;
                }
                break;
            case 'orderCode' :
                { //订单编号
                    $code = 'or' . $code;
                }
                break;
            case 'orderThird' :
                { //订单编号
                    $code = 'ot' . $code;
                }
                break;
            case 'orderThirds' :
                { //订单编号
                    $code = 'st' . $code;
                }
                break;
        }
        if ($length > 0) {
            $code = substr($code, 0, $length);
        }
        return $code;
    }

    //图片路径处理
    public static function dealPic($pic, $dir = '')
    {
        if ($pic == '') {
            return '';
        }
        if (strpos($pic, '|') === false) {
            $pic = $dir . $pic;
            if (strpos($pic, 'static') === false)
                $pic = '/static/upfile/' . $pic;
            return $pic;
        } else {
            $picArr = explode('|', $pic);
            $picStr = '';
            foreach ($picArr as $pic) {
                $pic = $dir . $pic;
                if (strpos($pic, 'static') === false)
                    $pic = '/static/upfile/' . $pic;
                if ($picStr) {
                    $picStr .= '|' . $pic;
                } else {
                    $picStr .= $pic;
                }
            }
            return $picStr;
        }
    }

    //图片路径处理
    public static function dealnotePic($pic, $dir = '')
    {
        if (strpos($pic, ',') === false) {
            $pic = $dir . $pic;
            if (strpos($pic, 'static') === false)
                $pic = '/static/upload/' . $pic;
            return $pic;
        } else {
            $picArr = explode(',', $pic);
            $picStr = '';
            foreach ($picArr as $pic) {
                $pic = $dir . $pic;
                if (strpos($pic, 'static') === false)
                    $pic = '/static/upload/' . $pic;
                if ($picStr) {
                    $picStr .= ',' . $pic;
                } else {
                    $picStr .= $pic;
                }
            }
            return $picStr;
        }
    }

    // UTF-8 截取字符串
    public static function Substr($str, $len)
    {
        for ($i = 0; $i < $len; $i++) {
            $temp_str = substr($str, 0, 1);
            if (ord($temp_str) > 127) {
                $i++;
                if ($i < $len) {
                    $new_str [] = substr($str, 0, 3);
                    $str = substr($str, 3);
                }
            } else {
                $new_str [] = substr($str, 0, 1);
                $str = substr($str, 1);
            }
        }
        $res = join('', $new_str);
        if (strlen($str) < $len) {
            return $res;
        } else {
            return $res . '...';
        }
    }

    /**
     * 获取公众号access_token
     *
     * @param string $token
     */
    public static function getAccessToken($token, $appid = null, $appsecret = null)
    {
        $token = empty ($token) ? $_SESSION ['token'] : $token;
        $key = md5($token . "_access");
        \Yii::$app->cache->delete($key);
        $ret = \Yii::$app->cache->get($key);
        if (!$ret) {
            static $app = array();
            if (is_null($appid) || is_null($appsecret)) {
                if (!isset($app ['id']) || !isset($app ['secret'])) {
                    $usr = Wxuser::getUsrByToken($token);
                    $app ['id'] = $usr ['appId'];
                    $app ['secret'] = $usr ['appSecret'];
                }
            } else {
                $app ['id'] = $appid;
                $app ['secret'] = $appsecret;
            }
            if (!$app ['id'] || !$app ['secret']) {
                return false;
            }
            $tokenUrl = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$app['id']}&secret={$app['secret']}";
            $ret = json_decode(file_get_contents($tokenUrl), true);

            \Yii::$app->cache->set($key, $ret, 3600);
        }
        if ($ret && isset($ret['access_token'])) {
            return $ret['access_token'];
        } else {
            return false;
        }
    }

    /**
     * 生成带参数的二维码
     *
     * @param int $scenc_id
     *            参数值
     * @param string $token
     * @param string $appid
     * @param string $appsecret
     * @return string number
     */
    public static function createQrcode($scenc_id, $token)
    {
        $data = '{"action_name": "QR_LIMIT_SCENE", "action_info": {"scene": {"scene_id": ' . $scenc_id . '}}}';
        $access_token = self::getAccessToken($token);
        $url = 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=' . $access_token;
        $result = self::http_post($url, $data);
        $result = json_decode($result);
        if ($result->ticket) {
            return 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=' . $result->ticket;
        } else {
            return 0;
        }
    }

    /**
     * 生成临时的字符串参数的二维码
     * 固定有效期为30分钟
     * @param $scenc_id
     * @param $token
     * @return int|string
     */
    public static function createStrQrcode($scenc_id, $token)
    {
        $data = '{"expire_seconds":1800,"action_name": "QR_STR_SCENE", "action_info": {"scene": {"scene_str": "' . $scenc_id . '"}}}';
        $access_token = self::getAccessToken($token);

        $url = 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=' . $access_token;
        $result = self::http_post($url, $data);
        $result = json_decode($result);

        if ($result->ticket) {
            return 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=' . $result->ticket;
        } else {
            return 0;
        }
    }


    //网页认证获取openid
    public static function getOpenid($token, $base = 'snsapi_base')
    {
        $curUrl = 'http://' . $_SERVER ['HTTP_HOST'] . $_SERVER ['REQUEST_URI'];
        //将当前进入的页面地址存起来，授权完成后再跳回
        Yii::$app->session['enterUrl'] = $curUrl;
        $usr = Wxuser::getUsrByToken($token);
        $appId = $usr ['appId'];

        //授权后回跳地址
        $reUrl = urlencode("http://" . $_SERVER['HTTP_HOST'] . '/frontend/web/oauth2/authorize.html');
        $url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=' . $appId . '&redirect_uri=' . $reUrl . '&response_type=code&scope=' . $base . '&state=state#wechat_redirect';
        Yii::$app->response->redirect($url)->send();
    }

    //获取微信JSAPI共享地址签名
    public static function getJSAPI($token)
    {
        $curUrl = 'http://' . $_SERVER ['HTTP_HOST'] . $_SERVER ['REQUEST_URI'];
        $info = self::getOpenid($token);
        $app = Wxuser::getUsrByToken($token);
        $sign_info = array(
            'accesstoken' => $info ['access_token'],
            'url' => $curUrl,
            'timeStamp' => time(),
            'nonceStr' => self::createNoncestr(6),
            'appid' => $app ['appId']
        );
        $address_sign = self::getSign_address($sign_info);
        $infoarray = array(
            'appId' => $app ['appId'],
            'scope' => 'jsapi_address',
            'signType' => "sha1",
            "addrSign" => $address_sign,
            'timeStamp' => $sign_info ['timeStamp'] . '',
            'nonceStr' => $sign_info ['nonceStr']
        );
        return $addressSign_info = json_encode($infoarray);
    }

    //获取JSAPI分享签名
    public static function getJSAPIShare($token)
    {
        $ret = \Yii::$app->cache->get($token . 'jsapi_ticket');
        if (!$ret or !isset($ret['ticket'])) {
            $access_token = self::getAccessToken($token);
            $url = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=' . $access_token . '&type=jsapi';
            $ret = json_decode(file_get_contents($url), true);
            if ($ret ['errcode'] == 40001) {
                \Yii::$app->cache->delete(md5($token . "_access"));
                \Yii::$app->cache->delete($token . "jsapi_ticket");
                self::getJSAPIShare($token);
            }
            \Yii::$app->cache->set($token . 'jsapi_ticket', $ret, 5000);
        }

        $curUrl = 'http://' . $_SERVER ['HTTP_HOST'] . $_SERVER ['REQUEST_URI'];
        $temp = array(
            'jsapi_ticket' => $ret ['ticket'],
            'noncestr' => self::createNoncestr(10),
            'timestamp' => time(),
            'url' => $curUrl
        );
        $str = "jsapi_ticket=" . $temp ['jsapi_ticket'] . "&noncestr=" . $temp ['noncestr'] . "&timestamp=" . $temp ['timestamp'] . "&url=" . $temp ['url'];
        $signature = sha1($str);
        $app = Wxuser::getUsrByToken($token);
        return array(
            'appId' => $app ['appId'],
            'timestamp' => $temp ['timestamp'] . '',
            'noncestr' => $temp ['noncestr'],
            'signature' => $signature
        );
    }

    /**
     * 获取微信用户详细信息
     *
     * @param
     *            $openid
     * @return
     *
     */
    public static function getUserInfo($token, $openid)
    {
        $accessToken = self::getAccessToken($token);
        $url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token=' . $accessToken . '&openid=' . $openid . '&lang=zh_CN';
        $res = @file_get_contents($url);
        $res = @json_decode($res, true);
        if ($res ['errcode'] == 40001) {
            \Yii::$app->cache->delete(md5($token . "_access"));
            self::getUserInfo($token, $openid);
        }
        if (isset ($res ['errcode']) || $res ['errcode'] != 0) {
            return array();
        }
        return $res;
    }

    /**
     * 发送模板消息
     *
     * @param string $token
     * @param string $openid
     * @param string $tplid
     * @param string $linkUrl
     * @param string $info
     */
    public static function sendTplInfo($token, $openid, $tplid, $linkUrl, $info)
    {
        $accessToken = self::getAccessToken($token);
        $url = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=' . $accessToken;
        $data = '{
           "touser":"' . $openid . '",
           "template_id":"' . $tplid . '",
           "url":"' . $linkUrl . '",
           "topcolor":"#FF0000",
           "data":' . $info . '
        }';
        return self::http_post($url, $data);
    }

    public static function sendTpl($token, $openid, $template, $params = array())
    {
        if (!$template || !$openid || !$token) {
            return json_encode(array(
                "errcode" => -1,
                "errmsg" => false,
                'ret' => '参数不全'
            ));
        }
        $tplinfo = \Yii::$app->params ['tplInfo'] [$template];
        if (!$tplinfo) {
            return json_encode(array(
                "errcode" => -1,
                "errmsg" => false,
                'ret' => '模板不存在'
            ));
        }
        $tplid = $tplinfo ['tmpid'];
        $linkUrl = urldecode($params ['linkurl']);
        $data = $tplinfo ['data'];
        $title = $tplinfo ['title'];
        foreach ($data as $k => $v) {
            if ($params [$k]) {
                $data [$k] ['value'] = urlencode($params [$k]);
            } else {
                return json_encode(array(
                    "errcode" => -1,
                    "errmsg" => false,
                    'ret' => "{$title}消息模板,参数:{$k}为空"
                ));
            }
        }
        $info = json_encode($data);
        $info = urldecode($info);
        return $result = self::sendTplInfo($token, $openid, $tplid, $linkUrl, $info);
    }

    /**
     * 拉取微信粉丝列表
     *
     * @param string $token
     */
    public static function getFansList($token, $url = NULL, $rtn = 1)
    {
        static $openid = array();
        $accessToken = self::getAccessToken($token);
        if (!$url)
            $url = 'https://api.weixin.qq.com/cgi-bin/user/get?access_token=' . $accessToken;
        $res = @file_get_contents($url);
        $res = @json_decode($res, true);
        if ($res ['errcode'] == 40001) {
            \Yii::$app->cache->delete(md5($token . "_access"));
            self::getFansList($token, $url, $rtn);
        }
        if ($res ['data'] ['openid']) {
            if ($openid)
                $openid = array_merge($openid, $res ['data'] ['openid']);
            else
                $openid = $res ['data'] ['openid'];
        }
        if ($res ['total'] > 10000 * $rtn) {
            $url = 'https://api.weixin.qq.com/cgi-bin/user/get?access_token=' . $accessToken . '&next_openid=' . $res ['next_openid'];
            self::getFansList($token, $url, ++$rtn);
        }
        return $openid;
    }

    // 上传文件
    public static function uploadFile($inputName, $fileGroup = '', $fileName = '', $fileType = array())
    {
        if (!isset ($_FILES [$inputName])) {
            return false;
        }
        $file = $_FILES [$inputName];
        if (!empty ($fileType)) {
            if (!in_array($file ['type'], $fileType)) {
                return false;
            }
        }
        if (!is_string($fileGroup) || empty ($fileGroup)) {
            return false;
        }
        if (!$fileName || !is_string($fileName) || empty ($fileName)) {
            $fileName = date('YmdHis') . rand(1, 1000);
        }
        $fileName .= strstr($file ['name'], '.');

        if (file_exists(APP_PATH . '/' . $fileGroup . '/' . $fileName)) {
            unlink(APP_PATH . '/' . $fileGroup . '/' . $fileName);
        }

        if (move_uploaded_file($file ['tmp_name'], APP_PATH . '/wbfiles/' . $fileGroup . '/' . $fileName)) {
            return $fileName;
        }

        return false;
    }

    // 上传图片
    public static function uploadPic($inputName, $fileGroup = '', $fileName = '')
    {
        return self::uploadFile($inputName, $fileGroup, $fileName, array(
            'image/gif',
            'image/jpeg',
            'image/pjpeg',
            'image/png'
        ));
    }

    // 获取键值对
    public static function createKeyVal($list, $key, $value = '')
    {
        if (!$value) {
            $value = $key;
        }
        $new_List = array();
        foreach ($list as $val) {
            $new_List [$val [$key]] = $val [$value];
        }
        return $new_List;
    }

    // 生成 $key字符串
    public static function createKeyStr($list, $key)
    {
        $new_Str = '';
        foreach ($list as $val) {
            if (!empty ($val [$key])) {
                $new_Str .= ",'" . $val [$key] . "'";
            }
        }
        return substr($new_Str, 1);
    }

    // 生成 $key字符串
    public static function createIntKeyStr($list, $key)
    {
        $new_Str = '';
        foreach ($list as $val) {
            $new_Str .= ",'" . $val [$key] . "'";
        }
        return substr($new_Str, 1);
    }

    //产生随机字符串，不长于32位

    public static function createNonceCapitalStr($length = 10)
    {
        $chars = "A5BHCD7E2F6G3H89J4KL5MYN6K2P7Q3R8S4T9U5VW2X3Y4Z";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    //产生随机字符串，不长于32位abcdefghijklmnpqrstuvwxy

    public static function createNoflow($length = 10)
    {
        $chars = "345678abcdefhijkmnpqrstuvwxy";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    public static function createNoncestr($length = 10)
    {
        $chars = "0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }
    public static function createNonceViewcode($length = 10)
    {
        $chars = "123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }
    /**
     * 作用：格式化参数，签名过程需要使用
     */
    public static function formatBizQueryParaMap($paraMap, $urlencode)
    {
        $buff = "";
        ksort($paraMap);
        foreach ($paraMap as $k => $v) {
            if ($urlencode) {
                $v = urlencode($v);
            }
            $buff .= $k . "=" . $v . "&";
        }
        $reqPar = '';
        if (strlen($buff) > 0) {
            $reqPar = substr($buff, 0, strlen($buff) - 1);
        }
        return $reqPar;
    }

    /**
     * 作用：生成签名 address
     */
    public static function getSign_address($Obj)
    {
        foreach ($Obj as $k => $v) {
            $Parameters [strtolower($k)] = $v;
        }
        //签名步骤一：按字典序排序参数
        ksort($Parameters);
        $String = self::formatBizQueryParaMap($Parameters, false);
        $result_ = sha1($String);
        return $result_;
    }

    //红包签名
    public static function sign($params, $key)
    {
        ksort($params);
        $beSign = array_filter($params, 'strlen');
        $pairs = array();
        foreach ($beSign as $k => $v) {
            $pairs [] = "$k=$v";
        }
        $sign_data = implode('&', $pairs);
        $sign_data .= '&key=' . $key;
        return strtoupper(md5($sign_data));
    }

    //数组转xml
    public static function array2xml($params)
    {
        $xml = '<xml>';
        $fmt = '<%s>%s</%s>';
        foreach ($params as $key => $val) {
            $xml .= sprintf($fmt, $key, $val, $key);
        }
        $xml .= '</xml>';
        return $xml;
    }

    //微信带证书请求
    public static function curl_post_ssl($url, $vars, $token, $second = 30, $aHeader = array())
    {
        $ch = curl_init();
        // 超时时间
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // 这里设置代理，如果有的话
        // curl_setopt($ch,CURLOPT_PROXY, '10.206.30.98');
        // curl_setopt($ch,CURLOPT_PROXYPORT, 8080);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        // 以下两种方式需选择一种


        // 第一种方法，cert 与 key 分别属于两个.pem文件
        // 默认格式为PEM，可以注释
        curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'PEM');
        curl_setopt($ch, CURLOPT_SSLCERT, getcwd() . '/pay/cert/' . $token . '/apiclient_cert.pem');
        // 默认格式为PEM，可以注释
        curl_setopt($ch, CURLOPT_SSLKEYTYPE, 'PEM');
        curl_setopt($ch, CURLOPT_SSLKEY, getcwd() . '/pay/cert/' . $token . '/apiclient_key.pem');

        // 第二种方式，两个文件合成一个.pem文件
        // curl_setopt ( $ch, CURLOPT_SSLCERT, getcwd () . '/all.pem' );


        if (count($aHeader) >= 1) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $aHeader);
        }

        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $vars);
        $data = curl_exec($ch);
        if ($data) {
            curl_close($ch);
            return $data;
        } else {
            $error = curl_errno($ch);
            echo "call faild, errorCode:$error\n";
            curl_close($ch);
            return false;
        }
    }

    //微信红包
    public static function packet($token, $params)
    {
        $url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/sendredpack';
        $wxuser = new Wxuser ();
        $wuser = $wxuser->getData('wxname,appId,headpic', 'one', '`token`="' . $token . '"');
        $payment = new Payment();
        $pdata = $payment->getData('*', 'one', 'token="' . $token . '" and pay_type=1');
        $params = array(
            'mch_billno' => $pdata ['account'] . date('Ymd') . time(),
            'mch_id' => $pdata ['account'],
            'wxappid' => $wuser ['appId'],
            'nick_name' => $wuser ['wxname'],
            'send_name' => $wuser ['wxname'],
            're_openid' => $params ['openid'], //oBidps0drFkU6x8dbgowS-ciDrbg
            'total_amount' => $params ['money'],
            'min_value' => $params ['money'],
            'max_value' => $params ['money'],
            'total_num' => 1,
            'wishing' => '感谢您参加！',
            'client_ip' => '127.0.0.1',
            'act_name' => '提现红包',
            'remark' => '返利提现',
            'logo_imgurl' => \Yii::$app->params ['url'] . $wuser ['headpic'],
            'share_url' => \Yii::$app->params ['url'],
            'share_imgurl' => 'http://dev.y100n.com/images/share.png',
            'share_content' => '健康发财',
            'remark' => '返利即时提现',
            'nonce_str' => md5(uniqid('', true))
        );
        $params ['sign'] = self::sign($params, $pdata ['payment_key']);
        return self::curl_post_ssl($url, self::array2xml($params), $token);
    }

    // 多线程操作
    public static function runThread($url, $hostname = '', $port = 80)
    {
        if (!$hostname) {
            $hostname = $_SERVER ['HTTP_HOST'];
        }
        $fp = fsockopen($hostname, $port, $errno, $errstr, 600);
        stream_set_blocking($fp, 0); //开启非阻塞模式
        fputs($fp, "GET " . $url . "\r\n");
        fclose($fp);
    }

    // 模拟（post/get）提交数据
    public static function http_post($url, $data = NULL, $header = NULL)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        if (!empty ($data)) {
            curl_setopt($curl, CURLOPT_POST, 1);
            if ($header) curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);

//        $log = new RequestLog();
//        $log->url = $url;
//        $log->input = \GuzzleHttp\json_encode($data);
//        $log->return = $output;
//
//        $log->company = '';
//        $log->c_time = time();
//        $log->save();

        (new DianDian())->requestlog($url,\GuzzleHttp\json_encode($data,JSON_UNESCAPED_UNICODE),$output,'','','W');

        return $output;
    }

    public static function http_get($url)
    {
        $oCurl = curl_init();
        if (stripos($url, "https://") !== FALSE) {
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
        }
        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
        $sContent = curl_exec($oCurl);
        $aStatus = curl_getinfo($oCurl);
        curl_close($oCurl);

//        $log = new RequestLog();
//        $log->url = $url;
//        $log->input = '';
//        $log->return = $sContent;
//        $log->status = $aStatus["http_code"];
//
//        $log->company = '';
//        $log->c_time = time();
//        $log->save();
        (new DianDian())->requestlog($url,'',$sContent,'',$aStatus["http_code"],'W');

        if (intval($aStatus["http_code"]) == 200) {
            return $sContent;
        } else {
            return false;
        }
    }

    // 接收模拟（post/get）提交数据
    public static function curlGet()
    {
        $obj = file_get_contents('php://input');
        $data = json_decode($obj, true);
        return $data;
    }

    //
    public static function getResultShowFormat($content)
    {
        $model_1 = '/<p.*?>.*<\/p>/iUs';
        $model_2 = '/<table.*?>.*<\/table>/iUs';
        $model_3 = '/<div.*?>.*<\/div>/iUs';

        if (preg_match($model_1, $content) || preg_match($model_2, $content) || preg_match($model_3, $content)) {
            '';
        } else {
            if (mb_substr_count($content, "\n") && !(preg_match('/<br\s?\/?>/iUs', $content))) {

                $content = str_replace("\n", '<br/>', $content);
            }
        }
        return $content;
    }

    /**
     * *******************************************************************
     * 函数作用:加密解密字符串
     * 使用方法:
     * 加密 :encrypt('str','E','nowamagic');
     * 解密 :encrypt('被加密过的字符串','D','nowamagic');
     * 参数说明:
     * $string :需要加密解密的字符串
     * $operation:判断是加密还是解密:E:加密 D:解密
     * $key :加密的钥匙(密匙);
     * *******************************************************************
     */
    public static function encrypt($string, $operation, $key = '')
    {
        $key = md5($key);
        $key_length = strlen($key);
        $string = $operation == 'D' ? base64_decode($string) : substr(md5($string . $key), 0, 8) . $string;
        $string_length = strlen($string);
        $rndkey = $box = array();
        $result = '';
        for ($i = 0; $i <= 255; $i++) {
            $rndkey [$i] = ord($key [$i % $key_length]);
            $box [$i] = $i;
        }
        for ($j = $i = 0; $i < 256; $i++) {
            $j = ($j + $box [$i] + $rndkey [$i]) % 256;
            $tmp = $box [$i];
            $box [$i] = $box [$j];
            $box [$j] = $tmp;
        }
        for ($a = $j = $i = 0; $i < $string_length; $i++) {
            $a = ($a + 1) % 256;
            $j = ($j + $box [$a]) % 256;
            $tmp = $box [$a];
            $box [$a] = $box [$j];
            $box [$j] = $tmp;
            $result .= chr(ord($string [$i]) ^ ($box [($box [$a] + $box [$j]) % 256]));
        }
        if ($operation == 'D') {
            if (substr($result, 0, 8) == substr(md5(substr($result, 8) . $key), 0, 8)) {
                return substr($result, 8);
            } else {
                return '';
            }
        } else {
            return str_replace('=', '', base64_encode($result));
        }
    }

    /**
     * 作用：将xml转为array
     */
    public static function xmlToArray($xml)
    {
        //将XML转为array
        $array_data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $array_data;
    }

    /*
     * 税后实际申请额
    */
    public static function AferTax($money)
    {
        $arr = array();
        switch ($money) {
            case $money > 800 && $money <= 4000 :
                $arr ['tax'] = round(($money - 800) * 0.2, 2);
                break;
            case $money > 4000 && $money <= 25000 :
                $arr ['tax'] = round($money * 0.8 * 0.2, 2);
                break;
            case $money > 25000 && $money <= 62500 :
                $arr ['tax'] = intval($money * 0.3 * 0.8) - 2000;
                break;
            case $money > 62500 :
                $arr ['tax'] = intval($money * 0.8 * 0.4) - 7000;
                break;
            case $money <= 800 :
                $arr ['tax'] = 0;
                break;
        }

        $arr ['amount'] = round($money, 2) - $arr ['tax'];
        // print_r($arr);
        return $arr;
    }

    //
    public static function AuthInfo($token)
    {
        $curUrl = 'http://' . $_SERVER ['HTTP_HOST'] . $_SERVER ['REQUEST_URI'];
        $usr = Wxuser::getUsrByToken($token);
        $appId = $usr ['appId'];
        $secret = $usr ['appSecret'];
        $reUrl = urlencode($curUrl);
        $code = $_GET ['code'];
        if (!$code) {
            $url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=' . $appId . '&redirect_uri=' . $reUrl . '&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect';
            \Yii::$app->getRequest()->redirect($url);
        } else {
            $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=' . $appId . '&secret=' . $secret . '&code=' . $code . '&grant_type=authorization_code';
            $data = file_get_contents($url);
            $arr = json_decode($data, true);
            $tinfo = self::Regttoken($appId, $arr ['refresh_token']);
            $access_token = $tinfo ['access_token'];

            if ($access_token) {
                $url = 'https://api.weixin.qq.com/sns/userinfo?access_token=' . $access_token . '&openid=' . \Yii::$app->session ['openid'] . '&lang=zh_CN';
                $fdata = file_get_contents($url);
                $userinfo = json_decode($fdata, true);
                return $userinfo;
            } else {
                return 'NUll';
            }
        }
    }

    //根据refresh_token 重新获取access_token
    private static function Regttoken($appId, $refresh_token)
    {
        $url = 'https://api.weixin.qq.com/sns/oauth2/refresh_token?appid=' . $appId . '&grant_type=refresh_token&refresh_token=' . $refresh_token;
        $data = file_get_contents($url);
        $arr = json_decode($data, true);
        return $arr;
    }

    public static function getImg($token, $serverId)
    {
        $access_token = self::getAccessToken($token);
        $url = 'http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=' . $access_token . '&media_id=' . $serverId;
        $dir = "/static/upload/mobile/" . time() . rand(1000, 9999) . ".jpg";
        if (!file_exists('.' . $dir)) {
            $ch = curl_init($url);
            $fp = fopen('.' . $dir, "wb");
            curl_setopt($ch, CURLOPT_FILE, $fp);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_exec($ch);
            curl_close($ch);
            fclose($fp);
        }
        return $dir;
    }

    public static function getAudio($token, $serverId)
    {
        $access_token = self::getAccessToken($token);
        $url = 'http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=' . $access_token . '&media_id=' . $serverId;
        $dir = "/static/upload/mobile/" . time() . rand(1000, 9999) . ".amr";
        if (!file_exists('.' . $dir)) {
            $ch = curl_init($url);
            $fp = fopen('.' . $dir, "wb");
            curl_setopt($ch, CURLOPT_FILE, $fp);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_exec($ch);
            curl_close($ch);
            fclose($fp);
        }
        return $dir;
    }

    //上传多媒体文件
    public static function uploadImg($token, $file, $type = 'image')
    {
        $access_token = self::getAccessToken($token);
        $url = 'http://file.api.weixin.qq.com/cgi-bin/media/upload?access_token=' . $access_token . '&type=' . $type;
        $file = UPFILE_PATH . $file;
        $fileData = array(
            'media' => '@' . $file
        );
        $res = self::http_post($url, $fileData);
        return json_decode($res, true);
    }

    //获取合并图片
    public static function getMergeImage($dst, $src, $openid)
    {
        //得到原始图片信息
        $path = '/static/img/code/';
        $pin = md5(\Yii::$app->session ['token'] . $openid) . ".jpg";
        if (is_file('.' . $path . $pin)) {
            return $path . $pin;
        }
        $dst_info = getimagesize($dst);
        switch ($dst_info [2]) {
            case 1 :
                $dst_im = imagecreatefromgif($dst);
                break;
            case 2 :
                $dst_im = imagecreatefromjpeg($dst);
                break;
            case 3 :
                $dst_im = imagecreatefrompng($dst);
                break;
        }
        $src_info = getimagesize($src);
        switch ($src_info [2]) {
            case 1 :
                $src_im = imagecreatefromgif($src);
                break;
            case 2 :
                $src_im = imagecreatefromjpeg($src);
                break;
            case 3 :
                $src_im = imagecreatefrompng($src);
                break;
        }

        //水印透明度
        $alpha = 100;
        //合并水印图片
        imagecopymerge($dst_im, $src_im, ($dst_info [0] - $src_info [0]) / 2, ($dst_info [1] - $src_info [1]) / 2 - 60, 0, 0, $src_info [0], $src_info [1], $alpha);
        if (Imagejpeg($dst_im, '.' . $path . $pin)) {
            return $path . $pin;
        }
    }

    //图片压缩
    public static function imgCompress($imgurl, $percent = 0.7)
    {
        list ($width, $height, $type) = getimagesize($imgurl); //获取原图尺寸
        switch ($type) {
            case 1 :
                $ext = 'gif';
                break;
            case 2 :
                $ext = 'jpeg';
                break;
            case 3 :
                $ext = 'png';
                break;
        }
        $imgcreatMethod = 'imagecreatefrom' . $ext;
        $imgMethod = 'image' . $ext;
        $src_im = $imgcreatMethod ($imgurl);

        $newwidth = $width * $percent;
        $newheight = $height * $percent;
        $dst_im = imagecreatetruecolor($newwidth, $newheight);
        imagecopyresampled($dst_im, $src_im, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
        $imgMethod ($dst_im, $imgurl); //输出压缩后的图片


        imagedestroy($dst_im);
        imagedestroy($src_im);
    }

    public static function dowith_sql($str)
    {
        $refuse_str = "and|or|select|update|from|where|order|by|*|delete|insert|into|values|create|table|database";
        $arr = explode("|", $refuse_str);
        for ($i = 0; $i < count($arr); $i++) {
            $replace = "[" . $arr [$i] . "]";
            $str = str_replace($arr [$i], $replace, $str);
        }
        return $str;
    }

    public static function filterParams()
    {
        function addslashes_deep($value)
        {
            if (empty ($value)) {
                return $value;
            } else {
                return is_array($value) ? array_map('addslashes_deep', $value) : addslashes($value);
            }
        }

        if (!get_magic_quotes_gpc()) {
            if (!empty ($_GET)) {
                $_GET = addslashes_deep($_GET);
            }
            if (!empty ($_POST)) {
                $_POST = addslashes_deep($_POST);
            }
            $_COOKIE = addslashes_deep($_COOKIE);
            $_REQUEST = addslashes_deep($_REQUEST);
        }
    }

    public static function Rztime($ftime)
    {
        $time = floor((time() - $ftime) / 3600);
        $res = '';
        switch ($time) {
            case 0 :
                $res = '刚刚';
                break;
            case $time > 0 && $time < 24 :
                $res = $time . '小时之前';
                break;
            case floor($time / 24) > 0 && floor($time / 24) < 30 :
                $res = floor($time / 24) . '天之前';
                break;
            case floor($time / 24) > 30 && floor($time / 720) < 12 :
                $res = floor($time / 720) . '月之前';
                break;
            default :
                $res = date('Y-m-d', $ftime);
        }
        return $res;
    }

    public static function Qgnum($a) //二维码整型多参拆分
    {
        $arr = array();
        $arr[0] = intval(substr($a, 0, 1)); //type
        $arr[1] = intval(substr($a, 1, (strlen($a) - 7)));
        $arr[2] = intval(substr($a, -6)); // pid
        return $arr;  //type-- pid--uid
    }

    public static function str_insert($str, $i, $substr)  //在字符串某位置插入另一个字符串
    {
        for ($j = 0; $j < $i; $j++) {
            $startstr .= $str[$j];
        }
        for ($j = $i; $j < strlen($str); $j++) {
            $laststr .= $str[$j];
        }
        $str = ($startstr . $substr . $laststr);
        return $str;
    }

//    public static function sendSms($tel = NULL)
//    {
//        $tel || exit('电话不能为空');
//        header("Content-type: text/html; charset=utf-8");
//        $code = rand(100000, 999999);
//        $url = 'http://42.121.98.132:8888/sms.aspx';
//        $content = '您的绑定验证码是：' . $code . ',有效时间3分钟，请验证后立即删除，不要泄露。';
//        //echo $data = 'action=send&userid=470&account=yibainian&password=123456&mobile='.$tel.'&content='.$content.'&sendTime=&extno=';
//        $data = array(
//            'action' => 'send',
//            'userid' => '551',
//            'account' => 'hndhwh',
//            'password' => '123456',
//            'mobile' => $tel,
//            'content' => $content,
//            'sendTime' => '',
//            'extno' => '',
//        );
//        $result = self::http_post($url, $data);
//        $resArr = self::xmlToArray($result);
//        if ($resArr['returnstatus'] == 'Success') {
//            Yii::$app->cache->set($tel . Yii::$app->session['token'], $code, 180);
//        } else {
//            Yii::$app->cache->delete($tel . Yii::$app->session['token']);
//            return false;
//        }
//        return true;
//    }

    public static function sendSms($tel = NULL, $content = '', $prefix = '',$code = '')
    {
        $tel || exit('电话不能为空');
        $config = \Yii::$app->params ['sms'];
//        $config = [
//            'api_send_url' => 'http://smssh1.253.com/msg/send/json',
//            'api_account' => 'N9778747',
//            'api_password' => '7vSQ7KHeaT5itjAf'
//        ];
        $header = array(
            'Content-Type: application/json; charset=utf-8'
        );
        if(!$code){
            $code = rand(100000, 999999);
        }

        if ($content == '') $content = '【云车驾到】' . $code . '您的绑定验证码，有效时间3分钟，请验证后立即删除，不要泄露。';
        $postArr = array(
            'account' => $config['api_account'],
            'password' => $config['api_password'],
            'msg' => urlencode($content),
            'phone' => $tel,
            'report' => true
        );
        $postArr = json_encode($postArr);
        $resArr = W::http_post($config['api_send_url'], $postArr, $header);
        $resArr = json_decode($resArr, true);

        //error_log(json_encode($resArr),3,APP_PATH.'/sms.txt');

        if ((int)$resArr['code'] == 0) {
            Yii::$app->cache->set($prefix . $tel . Yii::$app->session['token'], $code, 180);
        } else {
            Yii::$app->cache->delete($prefix . $tel . Yii::$app->session['token']);
            return false;
        }
        return true;
    }

    //data转xml
    public static function obj2xml($data)
    {
        $xml = new SimpleXMLElement ('<xml></xml>');
        self::data2xml($xml, $data);
        return $xml->asXML();
    }

    private static function data2xml($xml, $data, $item = 'item')
    {
        foreach ($data as $key => $value) {
            is_numeric($key) && ($key = $item);
            if (is_array($value) || is_object($value)) {
                $child = $xml->addChild($key);
                self::data2xml($child, $value, $item);
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

    private static function sendwuliu($thirdorder)
    {
        $lotorderModel = new Lottery_order();
        $where = "orderId='" . $thirdorder . "'";
        $order = $lotorderModel->getData('*', 'one', $where);
        $data = array('orderId' => $order['orderId'], 'orderNum' => $order['code'], 'trackingNum' => $order['trackingNum'], 'trackcompany' => $order['trackcompany']);
        $thirdApi = '';
        W::http_post($thirdApi, json_encode($data));

    }

    public static function formatNumber($number)
    {
        $arr = str_split($number, 3);
        $str = implode('-', $arr);
        return $str;
    }

    /**
     * 中联道路救援下单接口
     *
     * @param array $data
     *            参数值
     * @return mixed
     */
    public static function rescueapi($data)
    {

        $url = 'http://api.dev.aachina.cn/out/third/createRescureOrder';
        $data['serverId'] = Yii::$app->params['serverId'];
        $time = time();
        ksort($data);
        header('Content-type: text/html; charset=utf-8');
        $ossstr = implode("", $data);
        $data['sign'] = md5($ossstr . Yii::$app->params['serverKey']);
        //x-www-form-urlencoded //multipart/form-data
        $header = array(
            "content-type:multipart/form-data; charset=UTF-8"
        );
        $dateday = date("Ymd", $time);
        $result = W::http_post($url, $data, $header);
        error_log($result, 3, APP_PATH . '/carlog/' . $dateday . 'osslog.txt');
        return json_decode($result, true);

    }

    /**
     * 九天道路救援下单接口
     *
     * @param array $data
     *            参数值
     * @return mixed
     */
    public static function rescuejiutianapi($data)
    {

        $url = 'http://39.106.216.40/jiutian/orderInsert';
        $data['serverId'] = Yii::$app->params['jserverId'];
        $time = time();
        ksort($data);
        header('Content-type: text/html; charset=utf-8');
        $ossstr = implode("", $data);
        $data['sign'] = md5($ossstr . Yii::$app->params['jserverKey']);
        $datastr = json_encode($data);
        $header = array(
            "content-type:application/json; charset=UTF-8",
            "Content-Length: " . strlen($datastr)

        );
        $dateday = date("Ymd", $time);
        $result = W::http_post($url, $datastr, $header);
        error_log($result, 3, APP_PATH . '/carlog/' . $dateday . 'jtosslog.txt');
        return $result;

    }

    /**
     * 中联道路救援取消订单接口
     *
     * @param array $data
     *            参数值
     * @return mixed
     */
    public static function cancelororder($data)
    {
        $url = 'http://api.dev.aachina.cn/out/third/cacleRescureOrder';
        $data['serverId'] = Yii::$app->params['serverId'];
        ksort($data);
        header('Content-type: text/html; charset=utf-8');
        $ossstr = implode("", $data);
        $data['sign'] = md5($ossstr . Yii::$app->params['serverKey']);
        //x-www-form-urlencoded //multipart/form-data
        $header = array(
            "content-type:multipart/form-data; charset=UTF-8"
        );
        $dateday = date("Ymd", time());
        $result = W::http_post($url, $data, $header);
        error_log($result, 3, APP_PATH . '/carlog/' . $dateday . 'cancellog.txt');
        return json_decode($result, true);
    }

    /**
     * 九天道路救援取消订单接口
     *
     * @param array $data
     *            参数值
     * @return mixed
     */
    public static function cancelorder($data)
    {
        $url = 'http://39.106.216.40/jiutian/orderCancel';
        $data['serverId'] = Yii::$app->params['jserverId'];
        ksort($data);
        header('Content-type: text/html; charset=utf-8');
        $ossstr = implode("", $data);
        $data['sign'] = md5($ossstr . Yii::$app->params['jserverKey']);
        $datastr = json_encode($data);
        $header = array(
            "content-type:application/json; charset=UTF-8",
            "Content-Length: " . strlen($datastr)

        );
        $dateday = date("Ymd", time());
        $result = W::http_post($url, $datastr, $header);
        error_log($result, 3, APP_PATH . '/' . $dateday . '/jtcancellog.txt');
        return json_decode($result, true);
    }

    /**
     * 判断手机号码
     */
    public static function is_mobile($text)
    {
        $search = '/^0?1[3|4|5|6|7|8|9][0-9]\d{8}$/';
        if (preg_match($search, $text)) {
            return (true);
        } else {
            return (false);
        }
    }
    /**
     * 判断人保兑换码
     */
    public static function is_renbaocode($text)
    {
        $search = '/^[x{4e00}-\x{9fa5}]{2,5}\d{4}\d{1,6}$/u';
        if (preg_match($search, $text)) {
            return (true);
        } else {
            return (false);
        }
    }
    /**
     * 获取微信端的上传图片
     */
    public static function getWechatImg($token, $serverId)
    {
        $access_token = self::getAccessToken($token);
        $url = 'https://api.weixin.qq.com/cgi-bin/media/get?access_token=' . $access_token . '&media_id=' . $serverId;
        $dir = "/static/upload/mobile/" . md5($serverId) . ".jpg";
        $rs = self::http_get($url);
        if ($rs !== false) {
            file_put_contents('../../.' . $dir, $rs);
        }
        return $dir;
    }
}