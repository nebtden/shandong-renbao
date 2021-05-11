<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/2/27
 * Time: 11:14
 */

namespace common\components;

use GuzzleHttp\Client;
use Yii;
use common\components\Encrypt3Des;

class ShengDaCarNewApi
{
    protected $key;
    protected $key3Des;

    public function __construct()
    {
        $this->url = Yii::$app->params['shengda_url_new'];
        $this->key = Yii::$app->params['shengda_key_new'];
        $this->key3Des = Yii::$app->params['shengda_key3Des_new'];
        $this->sourceApp = Yii::$app->params['shengda_sourceApp_new'];
    }

    /**
     * 记录盛大接口调用日志
     * @param $url
     * @param $input
     * @param $return
     */
    public function log($url,$postData,$return,$type='')
    {
        $content= "--------------". date('Y-m-d H:i:s') . "--------------".PHP_EOL;
        $content.= 'url:' . $url . PHP_EOL;
        $content.= 'input:' . $postData . PHP_EOL;
        $content.= 'return:' . $return . PHP_EOL;
        $content.= PHP_EOL;
        $path = Yii::$app->getBasePath() . '/web/log/shengda/' . date('Y-m') . '/';
        if(!is_dir($path)){
            mkdir($path,0777, true);
        }
        $fopenLog = fopen($path .$type. date('Ymd') . '.log', 'a+' );
        fwrite($fopenLog, $content);
        fclose($fopenLog);
    }
    /**
     * 接口请求
     * @param $url
     * @param $input
     * @param $return
     */
    private function httpRequest($method,$url,$data=[],$header='Content-Type: application/json')
    {
        $headers = array(
            'Accept: application/json',
            'access:5GqjCZCZLNPlljJLmybgr'    //请求头默认值
        );

        if ($header) {
            array_push($headers, $header);
        }

        $headers = array_values(array_unique($headers));
        // 启动一个CURL会话
        $handle = curl_init();
        curl_setopt($handle, CURLOPT_URL, $url); // 要访问的地址
        //curl_setopt($handle,CURLOPT_HEADER,1); // 是否显示返回的Header区域内容
        curl_setopt($handle, CURLOPT_HTTPHEADER, $headers); //设置请求头
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true); // 获取的信息以文件流的形式返回
        curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false); // 从证书中检查SSL加密算法是否存在
        curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false); // 对认证证书来源的检查
        switch($method) {
            case 'GET':
                break;
            case 'POST':
                curl_setopt($handle, CURLOPT_POST, true);
                curl_setopt($handle, CURLOPT_POSTFIELDS, $data); //设置请求体，提交数据包
                break;
            case 'PUT':
                curl_setopt($handle, CURLOPT_CUSTOMREQUEST, 'PUT');
                curl_setopt($handle, CURLOPT_POSTFIELDS, $data); //设置请求体，提交数据包
                break;
            case 'DELETE':
                curl_setopt($handle, CURLOPT_CUSTOMREQUEST, 'DELETE');
                break;
        }

        $response = curl_exec($handle); // 执行操作
        $code = curl_getinfo($handle, CURLINFO_HTTP_CODE); // 获取返回的状态码
        curl_close ($handle); // 关闭CURL会话
        $row=array(
            'code'=>$code,
            'data'=>$response
        );
        return $row;      //返回结果值//返回状态码
    }
    /**
     * 3des加密
     * @param $params
     * @return mixed|string
     */
    public function encrypt($params)
    {
        $crypt3des = new Encrypt3Des($this->key3Des, '23456789');
        $newData = $params . $this->key;
        $md5Data = strtoupper(md5($newData));
        $postData = $params . '|' . $md5Data;
        $crypt = $crypt3des->encrypt($postData);

        return $crypt;
    }

    /**
     * 3des解密
     * @param $crypt
     * @return bool|string
     */
    public function decrypt($crypt)
    {

        $crypt3des = new Encrypt3Des($this->key3Des, '23456789');
        return $crypt3des->decrypt($crypt);
    }

    /**
     * 盛大公共请求方法
     * @param $data请求数据 $url请求地址
     * @return $res  返回结果json
     */
    private function requestApi($data,$url)
    {
        $this->url .= $url;
        $postJson = json_encode($data,256);
        $md5Json = strtoupper(md5($postJson.$this->key));
        $str = base64_encode(openssl_encrypt($postJson.'|'.$md5Json, 'DES-EDE3', $this->key3Des, OPENSSL_RAW_DATA, ''));

        $jia= [];
        $jia['encryptJsonStr'] = str_replace(array('+','/','='), array('-','_','.'), $str);

        $res = $this->httpRequest('POST',$this->url,json_encode($jia));
        $this->log($url,$postJson,\GuzzleHttp\json_encode($res));

        return $res;
    }


    /**
     * 创建订单
     * @param $postData
     * @return $res  返回结果json
     */
    public function receiveOrder($postData)
    {
        $url ='/ShengDaPlaceOrder/webOrderController/createOrder' ;
        $res = $this->requestApi($postData,$url);
        return $res;
    }

    /**
     * 门店查询
     * @param $postData
     * @return $res  返回结果json
     */
    public function merchantDistanceList($postData)
    {
        $url ='/ShengDaPlatform/enterpriseRange/merchantDistanceList' ;
        $res = $this->requestApi($postData,$url);
        return $res;
    }

    /**
     * 取消订单
     * @param $postData
     * @return $res  返回结果json
     */
    public function cancelOrder($postData)
    {
        $url ='/ShengDaPlaceOrder/webOrderController/cancleOrder' ;
        $res = $this->requestApi($postData,$url);
        return $res;
    }

    /**
     * 订单查询
     * @param $postData
     * @return $res  返回结果json
     */
    public function queryOrder($postData)
    {
        $url ='/ShengDaPlaceOrder/webQueryOrderController/queryOrder' ;
        $res = $this->requestApi($postData,$url);
        return $res;
    }

    /**
     * 省市区查询
     * @param $postData
     * @return $res  返回结果json
     */
    public function queryAreaList($postData)
    {
        $url ='/ShengDaPlatform/webArea/queryAreaList' ;
        $res = $this->requestApi($postData,$url);
        return $res;
    }

}