<?php

namespace common\components;

use GuzzleHttp\Client;
use Yii;


ini_set('date.timezone','Asia/Shanghai');


class EDaiJia
{
    public static $id = 'edaijia';

    const COUPON_RECHARGE = "customer/coupon/recharge/bind";
    const COUNPON_LIST = "customer/coupon/list";
    const COUNPON_BIND = "customer/coupon/binding";
    const COUNPON_ALLINFO = 'oapi/customer/coupon/allInfo';

    const NEARBY_DRIVER = "driver/idle/list";
    //下单
    const ORDER_COMMIT = "order/commit";
    const ORDER_INFO = "order/impInfo";
    //拉取订单信息
    const ORDER_POLLING = "order/polling";
    //获取为我服务的司机信息
    const ORDER_DRIVERS = 'customer/info/drivers';
    //获取当前订单的司机的位置
    const ORDER_DRIVER_POSITION = "driver/position";
    //获取订单费用详情
    const ORDER_ORDERPAY = "order/orderpay";
    //预估费用
    const ORDER_COSTESTIMATE = 'order/costestimate';
    //取消订单
    const ORDER_CANCEL = 'order/cancel';
    //获取token
    const AUTHEN_TOKEN = "customer/getAuthenToken";

    const H5_DEVELOP = "http://h5.d.edaijia.cn/app/index.html";
    const H5_NORMAL = "http://h5.edaijia.cn/app/index.html";

    const VER = '3.4';

    //gpsType
    const GPSTYPE_BAIDU = 'baidu';

    private $is_dev = false;

    /**
     * 接口地址前缀
     * @var stringF
     */
    private $host = '';
    private $appkey = null;
    private $secret = null;
    private $from = null;

    /**
     * 公钥
     * @var null
     */
    private $public_key = null;

    /**
     * 错误码
     * @var int
     */
    public $errCode = 0;
    /**
     * 错误描述
     * @var string
     */
    public $errMsg = 'ok';


    public function __construct()
    {
        $lcfg = Yii::$app->params['Ecar'];
        $this->appkey =  $lcfg['appkey'];
        $this->secret =  $lcfg['secret'];
        $this->from =   $lcfg['from'];
        $this->host = $lcfg['url'];
        $this->public_key = $lcfg['key'];
    }

    //因为非前置模式，只能写日志到文件。。
    private function log($url, $input, $return)
    {
//        $log = new RequestLog();
//        $log->url = $url;
//        $log->input = $input;
//        $log->return = $return;
//
//        $returnData = \GuzzleHttp\json_decode($return, true);
//        if (isset($returnData['code']) && $returnData['code']==0) {
//            $log->status = 1;
//        } else {
//            $log->status = 0;
//        }
//        $log->company = self::$id;
//        $log->c_time = time();
//        $log->save();
        $returnData = \GuzzleHttp\json_decode($return, true);
        if (isset($returnData['code']) && $returnData['code'] == 0) {
            $status = 1;
        } else {
            $status = 0;
        }

        (new DianDian())->requestlog($url,$input,$return,self::$id,$status,'EDaiJia');

        if (isset($returnData['code']) && $returnData['code']==0) {
//            Yii::error("url is :$url\n input is :$input\n return is :$return", __METHOD__);
        } else {
            Yii::error("url is :$url\n input is :$input\n return is :$return", __METHOD__);
        }

    }




    /**
     * 获得签名
     * @param array $data 参与签名的其他数据
     * @param int $timestamp 时间 格式:2015-07-18 18:18:20(十分钟有效)
     * @param null $ver 接口版本号   必填
     * @return array 带有签名的数据
     */
    public function get_sign($data = [], $ver = null, $timestamp = 0)
    {
        $appkey = $this->appkey;
        $secret = $this->secret;
        $from = $this->from;
        if (!$ver) {
            $ver = self::VER;
        }
        if (!$timestamp) $timestamp = date("Y-m-d H:i:s", time());

        $data['appkey'] = $appkey;
        $data['timestamp'] = $timestamp;
        $data['ver'] = $ver;
        $data['from'] = $from;

        ksort($data);

        $str = $secret;
        foreach ($data as $key => $val) {
            $str .= ($key . $val);
        }
        $str .= $secret;
        $sign = substr(md5($str), 0, 30);
        $data['sig'] = $sign;
        return $data;
    }




    /**
     * 获得免登录的用户token
     * @param string $phone 手机号码
     * @param bool $update
     * @return bool|mixed|string
     */
    public function get_authen_token($phone,$update = false)
    {
        $key = 'edd_auth_token_' . $phone;
        if($this->is_dev){
            $key = 'cdev_'.$key;
        }else{
            $key = 'cprod_'.$key;
        }
        $expire = 24 * 3600 - 100;
        if(!$update){
            $token = Yii::$app->cache->get($key);
            if ($token) return $token;
        }


        $randomkey = $this->randomkey();
        $data['randomkey'] = $randomkey;
        $data['phone'] = $phone;
        $data['os'] = 'ios';
        $data['udid'] = uniqid();
        $data = $this->get_sign($data);
        $param = '';
        foreach ($data as $k => $val) {
            if ($param) {
                $param .= '&' . $k . '=' . $val;
            } else {
                $param .= $k . '=' . $val;
            }
        }
        $encrypt = RSA::encryptByPublicKey($param, $this->public_key);
        $url = $this->host . self::AUTHEN_TOKEN . "?appkey=" . $data['appkey'] . '&data=' . $encrypt;
        $res = W::http_get($url);
        if ($res) {
            $result = json_decode($res, true);
            $this->errMsg = $result['message'];
            $this->errCode = $result['code'];
            if (0 === (int)$result['code']) {
                $token_data = $result['data'];
                $token = AES::decrypt($token_data, $randomkey);
                if (stripos($token, 'token=') === 0) {
                    $token = explode('=', $token)[1];
                    Yii::$app->cache->set($key, $token, $expire);
                    return $token;
                }
            }
        }
        return false;
    }

    protected function randomkey($len = 16)
    {
        $str = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $chars = str_split($str, 1);
        $random_keys = array_rand($chars, $len);
        $random = '';
        foreach ($random_keys as $k) {
            $random .= $chars[$k];
        }
        return $random;
    }


    /**
     * 拉取订单信息
     * @param $token
     * @param $bookingId
     * @param $bookingType
     * @return bool
     */
    public function get_order_info( $bookingId, $phone, $pollingCount = 1)
    {
        $data = [
            'bookingId' => $bookingId,
            'phone'=>$phone,
            'channel'=>$this->appkey,
        ];
        $data = $this->get_sign($data);
        $url = $this->host.self::ORDER_INFO;
        $url = $url .'?'.http_build_query($data);;
        $result = $this->urlget($url);
        return $result;

    }

    public function get_order_detail($token,$order_id){
        $data = [
            'token'=>$token,
            'orderId' => $order_id,
            'needAll'=>0,
        ];
        $data = $this->get_sign($data);
        $url = $this->host.'order/detail';
        $url = $url .'?'.http_build_query($data);;
        $result = $this->urlget($url);
        return $result;

    }


    public function order_info( $bookingId, $phone, $pollingCount = 1)
    {
        $data = [
            'bookingId' => $bookingId,
            'phone'=>$phone,
            'channel'=>$this->appkey,
        ];
        $data = $this->get_sign($data);
        $url = $this->host.self::ORDER_INFO;
        $url = $url .'?'.http_build_query($data);;
        $result = $this->urlget($url);
        return $result;

    }


    public function polling_info($token,$bookingId,$bookingType,$pollingCount=1){
        $data = [
            'token'=>$token,
            'bookingId' => $bookingId,
            'bookingType'=>$bookingType,
            'pollingStart'=>date('Y-m-d H:i:s',time()),
            'pollingCount'=>$pollingCount,
        ];
        $data = $this->get_sign($data);
        $url = $this->host.self::ORDER_POLLING;
        $url = $url .'?'.http_build_query($data);
        $result = $this->urlget($url);
        return $result;
    }

    public function getDriverInfo($dirver_id){
        $data = [
            'driverID'=>$dirver_id,
        ];
        $data = $this->get_sign($data);
        $url = $this->host.'driver/info/get';
        $url = $url .'?'.http_build_query($data);;
        $result = $this->urlget($url);
        return $result;
    }




    protected function urlget($url, $request_data = [])
    {
        $client = new Client();

        $res = $client->request('GET', $url, $request_data);

        $return_data = $res->getBody()->getContents();
        $return = \GuzzleHttp\json_decode($return_data, true);
        $this->log($url, json_encode($request_data), $return_data);
        return $return;
    }


}