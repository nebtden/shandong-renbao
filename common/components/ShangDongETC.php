<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019\5\9 0009
 * Time: 10:37
 */

namespace common\components;

use Yii;
use GuzzleHttp\Client;

class ShangDongETC
{
    private $version = 1.0;
    private $appid = '';
    private $url = '';
    private $privateContent='';
    private $publicContent='';

    public function __construct()
    {
        $this->http = new Client();
        $this->data = [
            'headers' => [
                'Content-Type' => 'application/json',
            ]
        ];
        $this->url = Yii::$app->params['shandong_etc']['url'];
        $this->appid = Yii::$app->params['shandong_etc']['appid'];
        $this->privateContent = Yii::$app->params['shandong_etc']['rsa_pri'];
        $this->publicContent=Yii::$app->params['shandong_etc']['rsa_pub'];
    }

    /**
     * 获取快递信息
     * @param string $company //快递公司代号
     * @param string $number //快递单号
     * @return array|mixed
     */
    public function express($company,$number)
    {
        $params =[
            'com' => $company,
            'no' =>$number
        ];
        $express = JuheExp::deliver($params);

        return $express;
    }

    /**
     * 提交订单
     * @param $params
     * @return mixed
     */
    public function orderSubmit($params)
    {
        $data = [
            'biz_id' => 'etc.order.submit',
            'waste_sn' => $_SERVER['REQUEST_TIME'].$params['user_id'],
            'params' => $params
        ];

        $url = $this->url . '?' . $this->getParam($data);
        return $this->urlget($url, $this->data);
    }

    /**
     * 取消订单
     * @param $params
     * @return mixed
     */
    public function cancelOrder($params)
    {
        $data = [
            'biz_id' => 'etc.order.cancel',
            'waste_sn' => $_SERVER['REQUEST_TIME'].$params['user_id'],
            'params' => $params
        ];

        $url = $this->url . '?' . $this->getParam($data);
        return $this->urlget($url, $this->data);
    }


    public function etcBill($params)
    {

        $data = [
            'biz_id' => 'etc.bill.query',
            'waste_sn' => $_SERVER['REQUEST_TIME'].$params['user_id'],
            'params' => $params
        ];

        $url = $this->url . '?' . $this->getParam($data);
        return $this->urlget($url, $this->data);
    }

    protected function getParam($getdata = [])
    {
        $query = [
            'versions' => $this->version,
            'appid' => $this->appid
        ];
        $query['data'] = json_encode($getdata);
        $query['sign'] = $this->createSign($query['data']);

        $urlParam = http_build_query($query);

        return $urlParam;
    }

    /**
     * 私钥签名
     * @param string $data 需要签名的数据
     * @return string
     */
    protected function createSign($data)
    {
        if(!is_string($data)){
            return false;
        }
        openssl_sign(base64_encode($data),$signature,$this->getPrivateKey(),OPENSSL_ALGO_SHA256);

        return base64_encode($signature);
    }

    /**
     * 验证签名
     * @param string $key 公钥字符串
     * @param string $data 要签名的数据
     * @param string $alg 签名算法
     * @param string $sign 签名
     * @return bool
     */
    public function verifySign($data,$sign)
    {
        if(!is_string($data) || !is_string($sign)){
            return false;
        }

        return (boolean) openssl_verify(base64_encode($data),base64_decode($sign),$this->getPublicKey(),OPENSSL_ALGO_SHA256);
    }

    /**
     * 获取私钥
     * @return bool|resource
     */
    protected function getPrivateKey()
    {
        $str = chunk_split($this->privateContent, 64, "\n");
        $key = "-----BEGIN RSA PRIVATE KEY-----\n$str-----END RSA PRIVATE KEY-----\n";

        return openssl_get_privatekey($key);
    }

    /**
     * 获取公钥
     * @return resource
     */
    protected function getPublicKey()
    {
        $str = chunk_split($this->publicContent, 64, "\n");
        $key = "-----BEGIN PUBLIC KEY-----\n$str-----END PUBLIC KEY-----\n";

        return openssl_get_privatekey($key);
    }


    protected function urlget($url, $request_data = [])
    {
        $client = new Client();
        $request_data = array_merge($request_data, ['verify' => true]);

        $res = $client->request('GET', $url, $request_data);

        $return_data = $res->getBody()->getContents();
        $return = \GuzzleHttp\json_decode($return_data, true);
        $this->log($url, $request_data, $return_data);
        return $return;
    }

    /**
     * 记录山东ETC接口调用日志
     * @param $url
     * @param $input
     * @param $return
     */
    private function log($url, $postData, $return, $type = '')
    {
        $content = "--------------" . date('Y-m-d H:i:s') . "--------------" . PHP_EOL;
        $content .= 'url:' . $url . PHP_EOL;
        $content .= 'input:' . json_encode($postData,JSON_UNESCAPED_UNICODE) . PHP_EOL;
        $content .= 'return:' . $return . PHP_EOL;
        $content .= PHP_EOL;
        $path = Yii::$app->getBasePath() . '/web/log/sdETC/' . date('Y-m') . '/';
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
        $fopenLog = fopen($path . $type . date('Ymd') . '.log', 'a+');
        fwrite($fopenLog, $content);
        fclose($fopenLog);
    }
}