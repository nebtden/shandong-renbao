<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018\12\3 0003
 * Time: 13:23
 */

namespace common\components;

use GuzzleHttp\Client;
use Yii;

class CheYiXing
{
    protected $http = '';
    public static $id = 'cheyixing';
    protected $redis = '';
    protected $url = '';
    protected $key = '';
    protected $aeskey = '';//车易行密钥
    public static $orderStatus=array(
        '1'=>ORDER_UNPAY,
        '2'=>ORDER_HANDLING,
        '4'=>ORDER_FAIL,
        '7'=>ORDER_HANDLING,
        '8'=>ORDER_SUCCESS,
        '9'=>ORDER_SUCCESS
    );
    public static $limitFlag = 4;//4以下为寄材料的免上线检，4为上传资料的免上线检，以上为上线检

    public function __construct()
    {
        $this->redis = Yii::$app->redis;
        $this->http = new Client();
        $this->url = \Yii::$app->params['cyx_url'];
        $this->key = \Yii::$app->params['cyx_key'];
        $this->aeskey = \Yii::$app->params['cyx_aeskey'];
    }

    private function log($url, $input, $return)
    {
        $this->requestlog($url, $input, $return, self::$id, '', 'cheyixing');
//        $return_data = \GuzzleHttp\json_decode($return, true);
//        if (isset($return_data['success']) && $return_data['success']) {
//            Yii::error("url is :$url\n input is :$input\n return is :$return", __METHOD__);
//        } else {
//            Yii::error("url is :$url\n input is :$input\n return is :$return", __METHOD__);
//        }
    }

    public function requestlog($url, $input, $return, $company, $status, $type)
    {
        $savePath = Yii::$app->getBasePath() . '/web/log/requestlog/' . date('Y-m') . '/';
        if (!is_dir($savePath)) {
            mkdir($savePath, 0777, true);
            chmod($savePath, 0777);
        }
        $f = fopen($savePath . $type . '_' . date('Ymd') . ".txt", 'a+');
        fwrite($f, 'url:' . $url . "\n");
        fwrite($f, 'input:' . $input . "\n");
        fwrite($f, 'return:' . $return . "\n");
        fwrite($f, 'company:' . $company . "\n");
        fwrite($f, 'status:' . $status . "\n");
        fwrite($f, 'c_time:' . date('Y-m-d H:i:s') . "\n");
        fwrite($f, "===========================================\n");
        fclose($f);
    }

    protected function getParam($postData = array())
    {
        $content = $this->encryptData($postData);
        $param = [
            'key' => $this->key,
            'content' => $content
        ];
        return $param;
    }

    protected function encryptData($postData = array())
    {
        $nonStr = $this->createNonceStr();
        $postData = $postData ? json_encode($postData, JSON_UNESCAPED_UNICODE) : '{}';
        $contentStr = $nonStr . $postData;
        $firstAes = strtoupper(md5($contentStr));
        $cyxaes = new CyxAES($this->aeskey);
        $content = $cyxaes->encrypt($contentStr . $firstAes);
        return $content;
    }

    protected function decryptData($str)
    {
        $res = json_decode($str, JSON_UNESCAPED_UNICODE);
        if ($res['code']) {
            return $res;
        }
        $cyxaes = new CyxAES($this->aeskey);
        $result = '';
        if ($str != '') {
            try {
                $content = $cyxaes->decrypt($str);
                $contentStr = substr($content, 0, strlen($content) - 32);
                $md5Str = substr($content, strlen($content) - 32, strlen($content));
                $checkMd5Str = strtoupper(md5($contentStr));
                if ($checkMd5Str == $md5Str) {
                    $result = substr($contentStr, 16, strlen($contentStr));
                }
            } catch (\Exception $e) {
                $this->requestlog('/decryptData', $content, '', self::$id, '', 'cheyixing');
            }
        }
        return json_decode($result, JSON_UNESCAPED_UNICODE);
    }

    protected function urlpost($url, $postdata)
    {
        $field = json_encode($postdata, JSON_UNESCAPED_UNICODE);
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $field,
            CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache",
                "content-type: application/json;charset=UTF-8",
            ),
        ));

        $response = curl_exec($curl);
//        $err = curl_error($curl);

        curl_close($curl);

//        if ($err) {
//            return "cURL Error #:" . $err;
//        } else {
        return $response;
    }

    protected function createNonceStr($length = 16)
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    public function checkSign($post_data)
    {
        $sign = $post_data['sign'];

        $my_sign = strtoupper(md5($post_data['orderId'].$post_data['orderType'].$post_data['orderStatus'].$this->key));
        if ($sign == $my_sign) {
            return true;
        } else {
            return false;
        }
    }
}