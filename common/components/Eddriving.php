<?php

namespace common\components;

use Yii;
use common\models\CarCoupon;

class Eddriving
{
    //const HOST = "http://open.d.api.edaijia.cn/";
    const DEVELOP = "http://open.d.api.edaijia.cn/";
    const NORMAL = "http://open.api.edaijia.cn/";
    const COUPON_RECHARGE = "customer/coupon/recharge/bind";
    const COUNPON_LIST = "customer/coupon/list";
    const COUNPON_BIND = "customer/coupon/binding";
    const COUNPON_ALLINFO = 'oapi/customer/coupon/allInfo';
    //获取周边空闲的司机
    const NEARBY_DRIVER = "driver/idle/list";

    //下单
    const ORDER_COMMIT = "order/commit";
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

    const PUBLIC_KEY_TEST = 'MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCNmoPK9NIm5mrxNaEVBd9Seqgu7ur/rHiS18fJm8YxvGSa0S/JcDHGKCVILLh31LlMYiDwO0T5JddE+rxQ2Z7ADoKPoqf+vMzIr1xXxgtmow5fOfuyjmvSefM5eM7fj5k3xTQmyQQ9us9yn3x/BsvKI2IhJMtvE8ltQUowSsgNLQIDAQAB';
    const PUBLIC_KEY_PROD = 'MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCdwcgkEI5KLNmPBLxE2liPl/9UokSmd6szru9wHPs6D6JdNk4cmIDJPpV+UslberqnG/IMHl0pf0jbU6HKb+KvpE5EAZoJqJOg76chSQtxdB/zSqr1zgDT+VaoRgol/rDmIA3SLAAw5sDp7eia9eYuKSUuYkf4dRSpX933jb16CwIDAQAB';

    //gpsType
    const GPSTYPE_BAIDU = 'baidu';

    /**
     * 是否为开发环境
     * @var bool
     */
    private $is_dev = false;

    /**
     * 接口地址前缀
     * @var stringF
     */
    private $host = '';
    /**
     * appkey
     * @var null|string
     */
    private $appkey = null;
    /**
     * secret
     * @var null|string
     */
    private $secret = null;

    /**
     * 来源渠道
     * @var null
     */
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

    /**
     * Eddriving constructor.
     * @param string $appkey
     * @param string $secret
     */
    public function __construct($appkey = '', $secret = '', $from = '')
    {
        $lcfg = Yii::$app->params['Ecar'];
        $this->appkey = $appkey ? $appkey : $lcfg['appkey'];
        $this->secret = $secret ? $secret : $lcfg['secret'];
        $this->from = $from ? $from : $lcfg['from'];
        $this->host =  $lcfg['url'];
        $this->public_key =  $lcfg['key'];
    }

    /**
     * 切换环境
     * @param bool $dev true，开发环境，false，生产环境
     * @return Eddriving
     */
    public function set_env($dev = false)
    {
        $this->is_dev = $dev;
        if ($dev) {
            $this->host = self::DEVELOP;
            $this->public_key = self::PUBLIC_KEY_TEST;
        } else {
            $this->host = self::NORMAL;
            $this->public_key = self::PUBLIC_KEY_PROD;
        }
        return $this;
    }

    /**
     * 设置本地操作错误
     * @param string $msg 错误描述
     */
    protected function set_local_error($msg = '')
    {
        $this->errMsg = $msg;
        $this->errCode = -1;
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
     * 绑定优惠券
     * @param string $bonusNumber 优惠券号码
     * @param string $phone 绑定手机号码
     * @param string $password 优惠券密码|非必须
     * @return array|bool
     */
    public function coupon_bind($bonusNumber = '', $phone = '', $password = '')
    {
        $data = [];
        if (!$bonusNumber) {
            $this->set_local_error('优惠券号码不能为空');
            return false;
        }
        $data['bonusNumber'] = $bonusNumber;
        if (!$phone) {
            $this->set_local_error('手机号码不能为空');
            return false;
        }
        $data['phone'] = $phone;
        if ($password) $data['password'] = $password;
        $data['channel'] = 'dinghan';

        $data = $this->get_sign($data);
        $url = $this->host . self::COUNPON_BIND;
        $res = W::http_post($url, $data);
        $res = json_decode($res, true);

        $this->errCode = $res['code'];
        $this->errMsg = $res['message'];
        if ((int)$res['code'] === 0) {
            return ['bindid' => $res['bindId'], 'bonusid' => $res['bonusId']];
        }
        return false;
    }

    /**
     * 查询优惠券信息（不限）
     * 根据 bindInfo 的 status 值判定，1 未使用，2 已使用，3 已过期。
     * 如果 bindInfo 为空则是未绑定
     * @param string $sn 优惠券号码
     * @param string $phone 手机号码
     * @return bool
     */
    public function coupon_allinfo($sn = '', $phone = '')
    {
        $data = [];
        if (!$sn) {
            $this->set_local_error('优惠券号码不能为空');
            return false;
        }
        $data['sn'] = $sn;
        if (!$phone) {
            $this->set_local_error('手机号码不能为空');
            return false;
        }
        $data['phone'] = $phone;

        $data = $this->get_sign($data);

        $query = http_build_query($data);
        $url = $this->host . self::COUNPON_ALLINFO . "?" . $query;
        $res = W::http_get($url);

        $res = json_decode($res, true);

        $this->errCode = $res['code'];
        $this->errMsg = $res['message'];
        if ((int)$res['code'] === 0) {
            return $res['data'];
        }
        return false;
    }

    public function get_nearby_drivers($lng = 0, $lat = 0, $gpsType = self::GPSTYPE_BAIDU)
    {
        $data = [];
        $data['udid'] = uniqid();
        $data['gpsType'] = $gpsType;
        $data['longitude'] = $lng;
        $data['latitude'] = $lat;

        $data = $this->get_sign($data);

        $query = http_build_query($data);
        $url = $this->host . self::NEARBY_DRIVER . "?" . $query;

        $res = W::http_get($url);
        $res = json_decode($res, true);
        return $res;
    }

    /**
     * 获取H5页面地址
     * @param string $phone 用户手机号
     * @param string $sn 优惠券号码
     * @param array $coupon 优惠券
     * @return string
     */
    public function get_h5_url($phone = '', $sn = '', &$coupon)
    {
        $data = [];
        $data['from'] = $this->from;
        if ($phone) $data['phone'] = $phone;
        if ($sn) $data['sn'] = $sn;
        if (!$this->check_sn($sn, $phone, $coupon)) {
            return 'javascript:;';
        }
        $query = http_build_query($data);
        $host = ($this->is_dev) ? self::H5_DEVELOP : self::H5_NORMAL;
        $url = $host . '?' . $query;
        return $url;
    }

    /**
     * 检查卡券是否已使用或过期
     * @param string $sn
     * @param string $phone
     * @param $coupon
     * @return bool
     */
    public function check_sn($sn = '', $phone = '', &$coupon)
    {
        $key = "ecar_coupon_" . $sn . $phone;
        $cache = Yii::$app->cache;
        $res = $cache->get($key);
        if ($res === false) {
            $res = $this->coupon_allinfo($sn, $phone);
        }
        if ($res) {
            //暂时不使用缓存
            //$cache->set($key,$res);
            //如果出现未绑定的情况，直接让这张券用不了
            if (empty($res['bind_info'])) {
                return false;
            } else {
                $data = $res['bind_info'];
                $obj = new CarCoupon();
                if ($data['status'] == 1) {
                    if ($coupon['status'] != $data['status']) {
                        $coupon['status'] = $data['status'];
                        $obj->myUpdate($coupon);
                    }
                    return true;
                }
                if ($data['status'] == 2) {
                    //已使用
                    $coupon['status'] = 2;
                    $coupon['use_time'] = strtotime($data['used_time']);
                    $obj->myUpdate($coupon);
                    return false;
                } elseif ($data['status'] == 3) {
                    //已过期
                    $coupon['status'] = 3;
                    $obj->myUpdate($coupon);
                    return false;
                }
            }
        }
        return true;
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
        $data['os'] = Helpper::check_os();
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


    /**
     * 一键下单
     * @param $phone
     * @param $address
     * @param $longitude
     * @param $latitude
     * @param string $bonus_sn
     * @param float $destinationLat
     * @param float $destinationLng
     * @param string $destinationAddress
     * @param string $gpsType
     * @return bool
     */
    public function commit_order($token,$phone, $address, $longitude, $latitude, $bonus_sn = '', $destinationLat = 0.0, $destinationLng = 0.0, $destinationAddress = '', $gpsType = self::GPSTYPE_BAIDU)
    {
        $data = [
            'phone' => $phone,
            'contactPhone' => $phone,
            'address' => $address,
            'number' => 1,
            'longitude' => $longitude,
            'latitude' => $latitude,
            'gpsType' => $gpsType,
            'token' => $token,
        ];
        if ($bonus_sn) {
            $data['bonus_sn'] = $bonus_sn;
            $data['isUseBonus'] = 1;
        }
        if ($destinationAddress) {
            $data['destinationLat'] = $destinationLat;
            $data['destinationLng'] = $destinationLng;
            $data['destinationAddress'] = $destinationAddress;
        }
        $data = $this->get_sign($data);
        $url = $this->host . self::ORDER_COMMIT;
        $res = W::http_post($url, $data);
        if ($res) {
            $res = json_decode($res, true);
            $this->errCode = $res['code'];
            $this->errMsg = $res['message'];
            if (0 === (int)$res['code']) {
                return $res['data'];
            }
        }
        return false;
    }

    /**
     * 预估费用
     * @param $token
     * @param $startLat
     * @param $startLng
     * @param $endLat
     * @param $endLng
     * @param string $bonusSn
     * @param string $gpsType
     * @return bool
     */
    public function costestimate($token, $startLat, $startLng, $endLat, $endLng, $bonusSn = '', $gpsType = self::GPSTYPE_BAIDU)
    {
        $data = [
            'token' => $token,
            'startLat' => $startLat,
            'startLng' => $startLng,
            'endLat' => $endLat,
            'endLng' => $endLng,
            'gpsType' => $gpsType,
        ];
        if ($bonusSn) {
            $data['bonusSn'] = $bonusSn;
        }
        $data = $this->get_sign($data);
        $query = http_build_query($data);
        $url = $this->host . self::ORDER_COSTESTIMATE . '?' . $query;
        $res = W::http_get($url);
        if ($res) {
            $res = json_decode($res, true);
            $this->errCode = $res['code'];
            $this->errMsg = $res['message'];
            if (0 === (int)$res['code']) {
                return $res['data'];
            }
        }
        return false;
    }

    /**
     * 拉取订单信息
     * @param $token
     * @param $bookingId
     * @param $bookingType
     * @return bool
     */
    public function order_polling($token, $bookingId, $bookingType, $pollingCount = 1)
    {
        $data = [
            'token' => $token,
            'bookingType' => $bookingType,
            'bookingId' => $bookingId,
            'pollingStart' => date("Y-m-d H:i:s"),
            'pollingCount' => $pollingCount
        ];
        $data = $this->get_sign($data);
        $query = http_build_query($data);
        $url = $this->host . self::ORDER_POLLING . '?' . $query;
        $res = W::http_get($url);
        if ($res) {
            $res = json_decode($res, true);
            $this->errCode = $res['code'];
            $this->errMsg = $res['message'];
            if (0 === (int)$res['code']) {
                return $res['data'];
            }
        }
        return false;
    }

    /**
     * 获取为我服务的司机
     * @param $token
     * @param int $pollingCount
     * @param string $gpsType
     * @return bool
     */
    public function info_drivers($token, $pollingCount = 1, $gpsType = self::GPSTYPE_BAIDU)
    {
        $data = [
            'token' => $token,
            'pollingCount' => $pollingCount,
            'gpsType' => $gpsType
        ];
        $data = $this->get_sign($data);
        $query = http_build_query($data);
        $url = $this->host . self::ORDER_DRIVERS . "?" . $query;
        $res = W::http_get($url);
        if ($res) {
            $res = json_decode($res, true);
            $this->errCode = $res['code'];
            $this->errMsg = $res['message'];
            if (0 === (int)$res['code']) {
                return $res['data'];
            }
        }
        return false;
    }

    /**
     * 获取当前订单司机位置与订单状态
     * @param $token
     * @param $bookingId
     * @param $driverId
     * @param $orderId
     * @param int $pollingCount
     * @param string $gpsType
     * @return bool
     */
    public function driver_position($token, $bookingId, $driverId, $orderId, $pollingCount = 1, $gpsType = self::GPSTYPE_BAIDU)
    {
        $data = [
            'token' => $token,
            'bookingId' => $bookingId,
            'driverId' => $driverId,
            'orderId' => $orderId,
            'pollingCount' => $pollingCount,
            'gpsType' => $gpsType
        ];
        $data = $this->get_sign($data);
        $query = http_build_query($data);
        $url = $this->host . self::ORDER_DRIVER_POSITION . "?" . $query;
        $res = W::http_get($url);
        if ($res) {
            $res = json_decode($res, true);
            $this->errCode = $res['code'];
            $this->errMsg = $res['message'];
            if (0 === (int)$res['code']) {
                return $res['data'];
            }
        }
        return false;
    }

    /**
     * 获得订单费用详情
     * @param $token
     * @param $orderId
     * @param string $phone
     * @return bool
     */
    public function orderpay($token, $orderId, $phone = '')
    {
        $data = [
            'token' => $token,
            'orderId' => $orderId
        ];
        if ($phone) $data['phone'] = $phone;

        $data = $this->get_sign($data);
        $query = http_build_query($data);
        $url = $this->host . self::ORDER_ORDERPAY . '?' . $query;
        $res = W::http_get($url);
        if ($res) {
            $res = json_decode($res, true);
            $this->errCode = $res['code'];
            $this->errMsg = $res['message'];
            if (0 === (int)$res['code']) {
                return $res['data'];
            }
        }
        return false;
    }

    /**
     * 取消订单
     * @param $token
     * @param $bookingId
     * @param string $reasonCode
     * @param string $reasonDetail
     * @param string $orderId
     * @return bool
     */
    public function order_cancel($token, $bookingId, $reasonCode = '', $reasonDetail = '', $orderId = '')
    {
        $data = [
            'token' => $token,
            'bookingId' => $bookingId
        ];
        if ($reasonCode) {
            $data['reasonCode'] = $reasonCode;
        }
        if ($reasonDetail) {
            $data['reasonDetail'] = $reasonDetail;
        }
        if ($orderId) {
            $data['orderId'] = $orderId;
        }
        $data = $this->get_sign($data);
        $url = $this->host . self::ORDER_CANCEL;
        $res = W::http_post($url,$data);
        if($res){
            $res = json_decode($res,true);
            $this->errCode = $res['code'];
            $this->errMsg = $res['message'];
            if(0 === (int)$res['code']){
                return $res['data'];
            }
        }
        return false;
    }
}