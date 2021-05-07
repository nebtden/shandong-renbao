<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018\8\23 0023
 * Time: 15:33
 */

namespace common\components;

use common\models\CarCompany;
use common\models\CarCoupon;
use common\models\CarCouponPackage;
use common\models\FansAccount;
use GuzzleHttp\Client;
use Yii;

class EJin
{
    protected $http = '';
    protected $app_key = '';
    protected $app_secret = '';
    protected $url = '';

    //因为此接口需要多次循环调用,当抛出错误的时候，直接使用log写入日志表，会被rollback
    public $messages = [];

    public function __construct()
    {
        $this->http = new Client();
    }

    private function log($url, $input, $return)
    {
        (new DianDian())->requestlog($url, $input, $return,'ejin','ejin');

        $return_data = \GuzzleHttp\json_decode($return, true);
        if (isset($return_data['success']) && $return_data['success']) {
            Yii::error("url is :$url\n input is :$input\n return is :$return", __METHOD__);
        } else {
            Yii::error("url is :$url\n input is :$input\n return is :$return", __METHOD__);
        }
    }

    protected function getSign($postData)
    {
        ksort($postData);
        $str = '';
        foreach ($postData as $k => $v) {
            $str .= $k . $v;
        }
        $sign = md5($this->app_key . $str . $this->app_secret);
        return $sign;
    }

    protected function urlpost($url, $data)
    {
        $res = $this->http->request('POST', $url, $data);
        $return_data = $res->getBody()->getContents();
        $return = \GuzzleHttp\json_decode($return_data, true);
        $this->log($url, json_encode($data), $return_data);
        return $return;
    }

    /**
     * 优惠券兑换通知
     * @param $coupon
     */
    public function chargeCouponNotice($coupon)
    {
        $res = (new CarCompany())->table()->where(['id' => $coupon[0]['companyid']])->one();//公司信息
        //需要包回调并且有回调地址时才发通知
        if ($res && $res['is_pnotice'] && $res['package_url']) {
            $package = (new CarCouponPackage())->table()->where(['id' => $coupon[0]['package_id']])->one();//券包信息
            $data['mobile'] = $coupon[0]['mobile'];
            $data['activetime'] = $coupon[0]['active_time'];
            $data['packagepwd'] = $package['package_pwd'];
            $data['packagesn'] = $package['package_sn'];
            $this->app_key = $res['appkey'];//分配给接入方的key
            $this->app_secret = $res['secret'];//分配给接入方的secret
            $sign = $this->getSign($data);
            $this->url = $res['package_url'] . '?sign=' . $sign;//接入方提供的url
            $header = [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'body' => json_encode($data),
            ];
            $this->urlpost($this->url, $header);
        }
    }

    /**
     * 使用优惠券回调通知
     * @param $couponid
     */
    public function useCouponNotice($insorder)
    {
        $couponid = $insorder['couponid'];
        $coupon = (new CarCoupon())->table()->where(['id'=>$couponid])->one();
        if($coupon && $coupon['companyid']){
            $res = (new CarCompany())->table()->where(['id' => $coupon['companyid']])->one();//公司信息
            //需要使用券回调并且有回调地址时才发通知
            if ($res && $res['is_cnotice'] && $res['coupon_url']) {
                $fans_account = FansAccount::find()->where(['uid' => $insorder['uid']])->one();
                $package = (new CarCouponPackage())->table()->where(['id' => $coupon['package_id']])->one();//券包信息

                $data['mobile'] = $fans_account['mobile'];
                $data['orderid'] = $insorder['orderid'];
                $data['carnum'] = $insorder['carnum'];
                $data['ctime'] = $insorder['c_time'];
                $data['couponsn'] = $coupon['coupon_sn'];
                $data['packagepwd'] = $package['package_pwd'];
                $data['packagesn'] = $package['package_sn'];

                $this->app_key = $res['appkey'];//分配给接入方的key
                $this->app_secret = $res['secret'];//分配给接入方的secret
                $sign = $this->getSign($data);
                $this->url = $res['coupon_url'] . '?sign=' . $sign;//接入方提供的url

                $header = [
                    'headers' => [
                        'Content-Type' => 'application/json',
                    ],
                    'body' => json_encode($data),
                ];
                $this->urlpost($this->url, $header);
            }
        }
    }
}