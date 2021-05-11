<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2019\4\22 0022
 * Time: 9:49
 */

namespace common\components;

use common\models\Car_wash_pinganhcz;
use Yii;
use common\components\Openssl;

class Pinganhcznew
{
    public $access_token = ''; //平安平台token
    protected $key='';    //AES加密key
    protected $userName = ''; //注册平台的手机号
    protected $company = 31; //客户公司名称
    protected $url;
    protected $merchartShop;
    protected $num = 1; //获取access_token最大次数

    public function __construct()
    {
        $this->userName = Yii::$app->params['pinganhcz']['userName'];
        $this->key = Yii::$app->params['pinganhcz']['key'];
        $this->url = Yii::$app->params['pinganhcz']['url'];
        $this->merchartShop = Yii::$app->params['pinganhcz']['merchart_shop'];
        $this->access_token = $this->getToken();
    }

    /**
     * 写入日志
     * @param $status
     * @param $msg
     * @param $data
     */
    public function log($status,$msg,$data,$result='')
    {
        $path = Yii::$app->basePath.'/web/log/pinganhcz/'.date('Y-m').'/';
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
     * 返回Get请求参数，
     * @return string
     */
    public function getRequest()
    {
        $baseUrl = Yii::$app->request->queryString;
        parse_str($baseUrl,$getData);
        $data = $getData['encryptJsonStr'];
//        $res = $this->decrypt($data);
//
//        return $res;
        return $getData;
    }

    /**
     * 返回Post请求参数
     * @return string
     */
    public function postRequest()
    {
        $post = yii::$app->getRequest()->getRawBody();
        $data = json_decode($post,true);
        $data = $data['encryptJsonStr'];
        //$res = $this->decrypt($data);

        return $data;
    }

    /**
     * 获取access_token
     * @return bool|mixed
     */
    protected function getToken()
    {
        $token = Yii::$app->cache->get('pinganhcz_access_token'.$this->userName);
        if(!$token){
            $token = $this->setToken();
        }

        return $token;
    }

    /**
     * 从平安接口获取access_token
     * @return bool
     */
    public function setToken()
    {
        $pinganhcz = Yii::$app->params['pinganhcz'];
        $url = '/oauth/oauth2/access_token';
        $params = [
            'client_id' => $pinganhcz['client_id'],
            'grant_type' => 'client_credentials',
            'client_secret' => $pinganhcz['client_secret'],
        ];
        $urlParams = http_build_query($params);
        $url = $this->url.$url.'?'.$urlParams;
        $res = $this->curl_get($url,$this->setHeaders());

        if($res){
            $resArr = json_decode($res, true);
        } else {
            $resArr['ret'] = '';
        }
        //如果获取token失败则重新获取，重新获取最大次数为10次,如果达到最大请求次数，则向平安好车主绑定的手机发送一条警告信息
        if($resArr['ret'] != 0){
            if($this->num <= 3){
                $this->num++;
                self::setToken();
            }else{
                W::sendSms($pinganhcz['userName'],'云车驾到系统警报，获取平安access_token失败，已达到最大请求次数，错误码为'.$resArr['ret'].'请开发人员尽快处理');
                return false;
            }
        }
        //expires_in:有效期 例如 ：60 (单位：分钟)
        $expires = $resArr['data']['expires_in']*8;
        Yii::$app->cache->set('pinganhcz_access_token'.$this->userName,$resArr['data']['access_token'],$expires);

        return $resArr['data']['access_token'];
    }



    /**
     * 盛大3des加密
     * @param $params
     * @return string
     */
    public function encrypt($params)
    {
        $key = Yii::$app->params['shengda_key'];
        $crypt3des = new Encrypt3Des($key, '23456789');
        $params = $params.'|'.$key;

        return $crypt3des->encrypt($params);
    }

    /**
     * 盛大3des解密
     * @param $data
     * @return string
     */
    public function decrypt($data)
    {
        $key = Yii::$app->params['shengda_key'];
        $crypt3des = new Encrypt3Des($key, '23456789');
        $deData = $crypt3des->decrypt($data);
        $str = strstr($deData,'|',true);
        if($str){
            return json_decode($str,true);
        }

        return false;
    }

    /**
     * 以json格式返会数据
     * @param $result
     * @return array
     */
    public function json_pingan($result)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return ['ret'=>0,'msg'=>'','data'=>$result];
    }

    /**
     * 设置url参数
     * @param array $getData
     * @return string
     */
    protected function getParam($getData=[])
    {
        $time = (string)sprintf('%.0f', microtime(true)*1000);
        $params = [
            'access_token' => $this->access_token,
            'userName' => $this->userName,
            'request_id' => $time
        ];
        if($getData){
            $query = array_merge($params,$getData);
        }else{
            $query = $params;
        }
        ksort($query);

        return  '?'.http_build_query($query);
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
     * curl get请求
     * @param $url
     * @param $headers
     * @return bool|string
     */
    protected function curl_get($url,$header)
    {
        $oCurl = curl_init();
        if (stripos($url, "https://") !== FALSE) {
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
        }
        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
        if($header){
            curl_setopt($oCurl, CURLOPT_HTTPHEADER, $header);
        }

        $sContent = curl_exec($oCurl);
        $aStatus = curl_getinfo($oCurl);
        curl_close($oCurl);

        if (intval($aStatus["http_code"]) == 200) {
            return $sContent;
        } else {
            return false;
        }
    }

    /**
     * get请求
     * @param $url
     * @param array $param
     * @param array $headers
     * @return bool|mixed|string
     */
    protected function httpGet($url,$param=[],$headers=[])
    {
        $url = $this->url.$url;
        $url = $url.$this->getParam($param);
        $res = $this->curl_get($url,$this->setHeaders($headers));

        return $res;
    }

    /**
     * post请求
     * @param $url
     * @param array $param
     * @param array $header
     * @return bool|mixed|string
     */
    protected function httpPost($url,$param=[],$data,$header=[])
    {
        $url = $this->url.$url;
        $url = $url.$this->getParam($param);
        $res = W::http_post($url,$data,$this->setHeaders($header));

        return $res;
    }
    /**
     * 通过用户手机号查询权益
     * @param $mobile  手机
     * @return bool|mixed|string
     */
    public function profit($mobile)
    {
        //$url = '/open/appsvr/property/api/new/partner/coupons';
        $url = '/open/vassPartner/appsvr/property/api/new/partner/coupons';
        $urlParam = [
            'customerMobile' => $mobile,
            //'merchartShop' => $this->merchartShop
        ];

        return  $this->httpGet($url,$urlParam);
    }

    /**
     * 通过兑换码查询卡卷详情
     * @param $code
     * @return bool|mixed|string
     */
    public function couponDetail($code,$merchartShop,$third_outlet_no)
    {

        $url = '/open/vassPartner/appsvr/property/api/new/partner/v2/coupondetail';
        $url.='?redemptionCode='.$code.'&outletNo='.$merchartShop.'&thirdOutletNo='.$third_outlet_no.'&access_token='.$this->getToken().'&request_id='.time().'&userName='.$this->userName;

        $urlParam = [
            'redemptionCode' => $code,
            'outletNo' => $merchartShop,
            'thirdOutletNo'=>$third_outlet_no
        ];
        $data = json_encode($urlParam);
        $openSsl = new Openssl($this->key,'','AES-128-ECB');
        $data = $openSsl->encrypt($data);

        $res =  $this->httpPost($url,'',$data);
        return  $res;
    }

    /**
     * 冻结卡券 核销前先冻结卡券
     * @param $code
     * @param $productId
     * @return bool|mixed|string
     */
    public function freezeCoupon($code,$productId)
    {
        //$url = '/open/appsvr/property/api/partner/freezecoupon';
        $url = 'open/vassPartner/appsvr/property/api/new/partner/freezecoupon';
        $urlParam = [
            'couponNo' => $code,
            'productId' => $productId
        ];

        return $this->httpGet($url,$urlParam);
    }

    /**
     * 解冻卡券 核销完成后解冻卡券
     * @param $code
     * @return bool|mixed|string
     */
    public function unFreezeCoupon($code)
    {
        //$url = 'open/appsvr/property/api/partner/unfreezecou';
        $url = '/open/vassPartner/appsvr/property/api/new/partner/unfreezecoupon';
        $urlParam = [
            'couponNo' => $code
        ];

        return $this->httpGet($url,$urlParam);
    }



    /**
     * 平安好车主通过兑换码核销卡卷
     * @param $coupon
     * @param $order_id
     * @param $shop_id
     * @param $mobile
     * @param $code
     * @param $product_id
     * @param $washOrder
     * @return bool
     */
    public function redemption($params,$couponDetail)
    {

        $url = '/open/vassPartner/appsvr/property/api/new/partner/redemption';
        $data = [
            'couponNo' => $couponDetail['coupon_code'],
            'partnerOrderId' => $params['partnerOrder'],
            'outletId' => $couponDetail['merchant_shop'],
            'productId' => $couponDetail['product_id'],
            'timestamp' => (string)sprintf('%.0f', microtime(true)*1000)
        ];
        $data = json_encode($data);
        $datalog = $data;
        $openSsl = new Openssl($this->key,'','AES-128-ECB');
        $data = $openSsl->encrypt($data);
        $responData = [
            'data' => $data,
            'customerMobile' => $couponDetail['mobile'],
        ];
        $responData = json_encode($responData);
        $res = $this->httpPost($url,'',$responData);

        $this->log(500,'平安接口返回信息',$datalog.'--'.$data,$res);
        return $res;
    }


}