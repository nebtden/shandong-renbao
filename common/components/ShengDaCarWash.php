<?php
/**
 * 盛大洗车接口
 * @time: 2018-11-01
 * @author: chenbh
 */

namespace common\components;

use GuzzleHttp\Client;
use Yii;
use common\components\Encrypt3Des;

class ShengDaCarWash
{
    protected $key;
    protected $key3Des;

    public function __construct()
    {
        $this->url = Yii::$app->params['shengda_url'];
        $this->key = Yii::$app->params['shengda_key'];
        $this->key3Des = Yii::$app->params['shengda_key3Des'];
        $this->sourceApp = Yii::$app->params['shengda_sourceApp'];
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
     * post提交
     * @param $url
     * @param null $data
     * @return mixed
     */
    protected function httpPost($url,$postData='')
    {
        $client = new Client();
        $options = [
            'headers' => [
                "Content-type"=> "application/json",
                "Accept"=>"application/json"
            ],
            'body' => $postData
        ];

        $res = $client->request('post',$url,$options);
        $data = $res->getBody()->getContents();
        //$this->log($url,$postData,$data);
        $data = json_decode($data,true);

        if($data['encryptJsonStr']){
            $data['encryptJsonStr'] = $this->decrypt($data['encryptJsonStr']);
        }

        $this->log($url,$postData,$data['encryptJsonStr']);
        return $data;
    }

    /**
     * curl get提交
     * @param $url
     * @return bool|mixed
     */
    public function httpGet($url)
    {
        $client = new Client();

        $res = $client->request('get',$url);
        $data = $res->getBody()->getContents();
        $data = json_decode($data,true);
        if($data['encryptJsonStr']){
            $data['encryptJsonStr'] = $this->decrypt($data['encryptJsonStr']);
        }
        $dataStr = json_encode($data,JSON_UNESCAPED_UNICODE);
        $this->log($url,'',$dataStr);

        return $data;
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
     * 获取url参数
     * @param array $getData
     * @param string $postData
     * @return string
     */
    public function getPram($getData=[], $postData='')
    {
        $timestamp =microtime(true)*10000;

        $sourceApp = $this->sourceApp;
        if(!empty($getData)){
            $crypt = json_encode($getData).$postData;
        }else{
            $crypt = $postData;
        }
        $digest = $this->encrypt($crypt);
        $query = [
            'sourceApp' => $sourceApp,
            'timestamp' => $timestamp,
            'encryptJsonStr' => $digest,
        ];
        $query = array_merge($getData, $query);
        $urlParam = http_build_query($query);

        return $urlParam;
    }


    /**
     * url sign检测
     * @param $urlParam
     * @param $postData
     * @return bool
     */
    public function checkMd5($urlParam,$postData)
    {
        parse_str($urlParam,$params);
        $sign = $params['sign'];
        $newMd5 = strtoupper(md5($postData.'|'.$this->key));
        if($newMd5 != $sign){
            return false;
        }
        return true;
    }

    /**
     * 创建订单
     * @param $postData
     * @return mixed
     */
    public function receiveOrder($postData)
    {
        $url = $this->url . 'car!receiveOrder';
        $postData = json_encode($postData);
        $url = $url . '?' . $this->getPram([],$postData);

        return $this->httpPost($url, $postData);

    }

    /**
     * 门店查询
     * @param $postData
     * @return mixed
     */
    public function merchantDistanceList($postData)
    {
        $url = $this->url. 'enterpriseRange!merchantDistanceList';
        $postData = json_encode($postData);
        $url = $url . '?' . $this->getPram([],$postData);

        return $this->httpPost($url, $postData);
    }

    /**
     * 取消订单
     * @param $postData
     * @return mixed
     */
    public function cancelOrder($postData)
    {
        $url = $this->url. 'etc!cancelOrder';
        $postData = json_encode($postData);
        $url = $url . '?' . $this->getPram([],$postData);

        return $this->httpPost($url, $postData);
    }

}