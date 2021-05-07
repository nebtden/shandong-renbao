<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/4/17
 * Time: 17:04
 */


namespace common\components;
use Yii;

class PiccInterface
{
    public $token = ''; //Picc平台token
    protected $key='';    //AES加密key
    protected $userName = ''; //
    protected $company = 31; //客户公司名称
    protected $url;
    protected $merchartShop;
    protected $num = 1; //获取access_token最大次数
    protected $passWord = '';
    protected $aeskey = '';
    protected $signKey = ''; //获取access_token最大次数


    public function __construct()
    {
        $this->userName = Yii::$app->params['PICC']['userName'];
        $this->passWord = Yii::$app->params['PICC']['passWord'];
        $this->aeskey = Yii::$app->params['PICC']['aeskey'];
        $this->url = Yii::$app->params['PICC']['Url'];
        $this->signKey = Yii::$app->params['PICC']['signKey'];

        $this->token = $this->getToken();

    }

    /**
     * MD5加密后再DES加密
     * 请求前的参数加密  @param $str
     * 加密后的参数     @return string
     */
    public function Desenstr($str)
    {
        $sign   =  strtoupper(md5($str));
        $obj    =  new AESUtil();
        $desstr =  $obj->encrypt($sign,$this->aeskey);

        return $desstr;
    }
    /**
     * DES加密
     * 请求前的参数加密  @param $str
     * 加密后的参数     @return string
     */
    public function Desstr($str)
    {

        $obj    =  new AESUtil();
        $desstr =  $obj->encrypt($str,$this->aeskey);
        return $desstr;
    }
    /**
     * DES解密
     * 请求前的参数加密  @param $str
     * 加密后的参数     @return string
     */
    public function Desdestr($str)
    {

        $obj    =  new AESUtil();
        $desstr =  $obj->decrypt($str,$this->aeskey);
        return $desstr;
    }


    /**
     * MD5加密
     * 请求前的参数加密  @param $str
     * 加密后的参数     @return string
     */
    public function Md5str($data)
    {

        $obj    =  new AESUtil();
        $md5str = $obj->sign($data,$this->signKey);
        $md5sign = $obj->md5str($md5str);
        return $md5sign;
    }

    /**
     * 获取token
     * @return bool|mixed
     */
    protected function getToken()
    {
        $token = Yii::$app->cache->get('picc_token'.$this->userName);
        $picc_refreshToken = Yii::$app->cache->get('picc_refreshToken'.$this->userName);
        if(!$token){
            if( !empty($picc_refreshToken)){
                $token = $this->refreshToken($picc_refreshToken);
            }else{
                $token = $this->setToken();
            }
        }
        return $token;
    }

    /**
     * 从中国人保财险token
     * @return bool
     */
    public function setToken()
    {

        $url = '/api/token/create';
        $obj    =  new AESUtil();

        $params = [
            'userName' => $this->userName,
            'passWord' => self::Desenstr($this->passWord),
            'timeStamp' => $obj->getMillisecond(),
        ];

        $params['sign'] = $this->Md5str($params);
        $params = json_encode($params,true);

        $res = W::http_post($this->url.$url,$params,$this->setHeaders());

        if($res){
            $resArr = json_decode($res, true);
            $this->log($resArr['code'],$resArr['msg'],$params,$res);
        }

        if($resArr['code'] == '1000000'){
            $expires = 86400-10;//单位秒
            Yii::$app->cache->set('picc_token'.$this->userName,$obj->decrypt($resArr['data']['token'],$this->aeskey),$expires);
            $refresh_expires = 86400*30-10;
            Yii::$app->cache->set('picc_refreshToken'.$this->userName,$obj->decrypt($resArr['data']['refreshToken'],$this->aeskey),$refresh_expires);
        }


        return $obj->decrypt($resArr['data']['token'],$this->aeskey);
    }

    /**
     * 从中国人保财险刷新token
     * @return bool
     */
    public function refreshToken($refreshToken)
    {

        $url = '/api/token/refresh';
        $obj    =  new AESUtil();
        $piccparams = Yii::$app->params['PICC'];
        $params = [
            'clientId' => $piccparams['clientId'],
            'clientSecret' => self::Desstr($piccparams['clientSecret']),
            'refreshToken' => $refreshToken,
            'timeStamp' => $obj->getMillisecond(),
        ];

        $params['sign'] = $this->Md5str($params);
        $params = json_encode($params,true);
        $res = W::http_post($this->url.$url,$params,$this->setHeaders());

        if($res){
            $resArr = json_decode($res, true);
            $this->log($resArr['code'],$resArr['msg'],$params,$res);
        }

        if($resArr['code'] == '1000000'){
            $expires = 86400-10;//单位秒
            Yii::$app->cache->set('picc_token'.$this->userName,$obj->decrypt($resArr['data']['token'],$this->aeskey),$expires);
            $refresh_expires = 86400*30-10;
            Yii::$app->cache->set('picc_refreshToken'.$this->userName,$obj->decrypt($resArr['data']['refreshToken'],$this->aeskey),$refresh_expires);
        }

        return $obj->decrypt($resArr['data']['token'],$this->aeskey);
    }

    /**
     * 设置请求头
     * @param array $header
     * @return array
     */
    protected function setHeaders($header=[])
    {
        $headers = [
            'Content-Type: application/json;charset=utf-8',
            'accept: application/json;charset=utf-8'
        ];
        if($header){
            $headers = array_merge($headers,$header);
        }
        return $headers;
    }


    /**
     * 写入日志
     * @param $status
     * @param $msg
     * @param $data
     */
    public function log($status,$msg,$data,$result='')
    {

        $path = Yii::$app->basePath.'/web/log/picc/'.date('Y-m').'/';
        if(!is_dir($path)){
            mkdir($path,0755);
        }
        $file = fopen($path.date('Y-m-d').'.log','a+');
        $content = '======================'.date('Y-m-d H:i:s').'======================'.PHP_EOL;
        $content.= 'status:'.$status.PHP_EOL;
        $content.= 'message:'.$msg.PHP_EOL;
        $content.= 'data:'.$data.PHP_EOL;
        $content.= 'result:'.$result.PHP_EOL;
        fwrite($file,$content);
        fclose($file);
    }
    /**
     * 写入日志
     * @param $status
     * @param $msg
     * @param $data
     */
    public function noticelog($data,$result='')
    {

        $path = Yii::$app->basePath.'/web/log/piccnotice/'.date('Y-m').'/';
        if(!is_dir($path)){
            mkdir($path,0755);
        }
        $file = fopen($path.date('Y-m-d').'.log','a+');
        $content = '======================'.date('Y-m-d H:i:s').'======================'.PHP_EOL;
        $content.= 'data:'.$data.PHP_EOL;
        $content.= 'result:'.$result.PHP_EOL;
        fwrite($file,$content);
        fclose($file);
    }

    /**
     * 第三方凭证码锁定接口
     * @param array $data
     * @return array $resArr
     */
    public function cuponnoLock($data)
    {
        $url = '/api/cuponnoInfo/cuponnoLock';
        $obj    =  new AESUtil();
        $piccparams = Yii::$app->params['PICC'];
        $params = [
            'timeStamp' => $obj->getMillisecond(),
            'data' =>[
                'cuponNo' => self::Desstr($data['cuponNo']),
                'outerOrderId' => self::Desstr($data['outerOrderId']),
                'orderNo' => self::Desstr($data['orderNo']),
                'channel' => self::Desstr($piccparams['channel']),
                'connectPhone' => self::Desstr($data['connectPhone']),
                'username' => self::Desstr($data['userName'])
            ],
            'uuid' => $piccparams['uuid'],
            'token' => $this->token
        ];
        $params['sign'] = $this->Md5str($params);
        $params = json_encode($params,true);

        $res = W::http_post($this->url.$url,$params,$this->setHeaders());

        if($res){
            $resArr = json_decode($res, true);
            $this->log($resArr['code'],$resArr['msg'],$params,$res);
        }
        return $resArr;
    }

    /**
     * 订单状态通知接口
     * @param array $data
     * @return array $resArr
     */
    public function syncOrderInfo($data)
    {
        $url = '/api/orderInfo/syncOrderInfo';
        $obj    =  new AESUtil();
        $piccparams = Yii::$app->params['PICC'];
        $params = [
            'timeStamp' => $obj->getMillisecond(),
            'data' =>[
                'orderNo' => self::Desstr($data['orderNo']),
                'cuponNo' => self::Desstr($data['cuponNo']),
                'channel' => self::Desstr($piccparams['channel']),
                'username' => self::Desstr($data['userName']),
                'outerOrderId' => self::Desstr($data['outerOrderId']),
                'status' => self::Desstr($data['status']),
                'connectPhone' => self::Desstr($data['connectPhone'])
            ],
            'uuid' => $piccparams['uuid'],
            'token' =>$this->token
        ];
        $params['sign'] = $this->Md5str($params);
        $params = json_encode($params,true);
        $res = W::http_post($this->url.$url,$params,$this->setHeaders());

        if($res){
            $resArr = json_decode($res, true);
            $this->log($resArr['code'],$resArr['msg'],$params,$res);
        }

        return $resArr;
    }


}