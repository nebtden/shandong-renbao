<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018\8\23 0023
 * Time: 15:33
 */

namespace common\components;

use GuzzleHttp\Client;
use Yii;

class DianDian
{
    protected $http = '';
    public static $id = 'diandian';
    protected $redis = '';
    protected $url = '';
    protected $key = '';
    protected $secret = '';


    /**
     * 点点的状态，对应于我们的状态关系如下
     * @var array 订单状态：1-未处理 2-处理中 3-已处理 4-已关闭
     *     数据库状态，订单状态：0或者1处理中 2成功3失败  4充值锁定中 (也是处理中)  9未确认)'
     */
    public static $order_status = [
        1 => ORDER_HANDLING,
        2 => ORDER_HANDLING,
        3 => ORDER_SUCCESS,
        4 => ORDER_FAIL,
    ];


    //因为此接口需要多次循环调用,当抛出错误的时候，直接使用log写入日志表，会被rollback
    public $messages = [];

    public function __construct()
    {

        $this->redis = Yii::$app->redis;
        $this->http = new Client();
        $this->url = \Yii::$app->params['ddyc_url'];
        $this->key = \Yii::$app->params['ddyc_app_key'];
        $this->secret = \Yii::$app->params['ddyc_app_secret'];

    }

    //因为非前置模式，只能写日志到文件。。
    private function log($url, $input, $return)
    {
//        $log = new RequestLog();
//        $log->url = $url;
//        $log->input = $input;
//        $log->return = $return;
//
////        $returnData = \GuzzleHttp\json_decode($return, true);
////        if (isset($returnData['success']) && $returnData['success']) {
////            $log->status = SUCCESS_STATUS;
////        } else {
////            $log->status = ERROR_STATUS;
////        }
//        $log->company = self::$id;
//        $log->c_time = time();
//        $log->save();
        $this->requestlog($url, $input, $return, self::$id, '', 'dianidan');

        $return_data = \GuzzleHttp\json_decode($return, true);
        if (isset($return_data['success']) && $return_data['success']) {
            Yii::error("url is :$url\n input is :$input\n return is :$return", __METHOD__);
        } else {
            Yii::error("url is :$url\n input is :$input\n return is :$return", __METHOD__);
        }
    }

    public function requestlog($url, $input, $return, $company, $status, $type)
    {
        $savePath = Yii::$app->getBasePath() . '/web/log/requestlog/' . date('Y-m') . '/';
        if (!is_dir($savePath)) {
            mkdir($savePath, 0777, true);
            chmod($savePath,0777);
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


    protected function getParam($getdata = [], $postData = '')
    {
        $micro_time = microtime(true);
        $time = intval($micro_time * 1000);
        $query = ['timestamp' => $time, 'app_key' => $this->key,];
        $query = array_merge($query, $getdata);
        ksort($query);

        $urlParam = http_build_query($query);
        $sign = strtoupper(md5(strrev($this->key . $this->secret . $urlParam . $postData)));


        return $urlParam . '&sign=' . $sign;
    }


    protected function urlget($url, $request_data = [])
    {
        $client = new Client();
        $request_data = array_merge($request_data, ['verify' => true]);

        $res = $client->request('GET', $url, $request_data);

        $return_data = $res->getBody()->getContents();
        $return = \GuzzleHttp\json_decode($return_data, true);
        $this->log($url, json_encode($request_data), $return_data);
        return $return;
    }

    protected function urlpost($url, $data)
    {

        $res = $this->http->request('POST', $url, $data);
        $return_data = $res->getBody()->getContents();
        $return = \GuzzleHttp\json_decode($return_data, true);
        $this->log($url, json_encode($data), $return_data);
        return $return;


    }

    public function checkToken($urlparam, $postData)
    {
        parse_str($urlparam, $query);
        ksort($query);
        $sign = $query['sign'];
        unset($query['sign']);

        ksort($query);
        $urlParam = http_build_query($query);

        $my_sign = strtoupper(md5(strrev($this->key . $this->secret . $urlParam . $postData)));
        if ($sign == $my_sign) {
            return true;
        } else {
            return false;
        }
    }

    protected function token($user_id)
    {
        $token_key = 'token_' . $user_id;
        $token = $this->redis->get($token_key);
        if (!$token) {
            $token = $this->getToken($user_id);
        }
        return $token;
    }


    //token get,token don't have expire time
    public function getToken($user_id)
    {
        $url = $this->url . '/user/assign/token';

        $postData = json_encode([
            "authCode" => $user_id
        ]);

        $url = $url . '?' . $this->getParam([], $postData);

        $data = [
            'headers' => [
                'Content-Type' => 'application/json',
                'app_key' => $this->key
            ],
            'body' => $postData
        ];
        $result = $this->urlpost($url, $data);
        if (isset($result['success']) and $result['success'] == 1) {
            $token_key = 'token_' . $user_id;
            $this->redis->set($token_key, $result['data']['token']);
        }
        return $result['data']['token'];
    }

    public function pay_pre($user_id, $data)
    {
        $url = $this->url . '/pay/trade/pre';
        $postData = json_encode($data);
        $url = $url . '?' . $this->getParam([], $postData);


        $token = $this->token($user_id);

        $request_data = [
            'headers' => [
                'Content-Type' => 'application/json',
                'token' => $token,
                'app_key' => $this->key
            ],
            'body' => $postData
        ];
        return $this->urlpost($url, $request_data);
    }


    public function getBrandList()
    {
        $url = $this->url . '/basedata/brand/list';
        $url = $url . '?' . $this->getParam();
        return $this->urlget($url);
    }

    public function brands()
    {
        $expires = 30 * 24 * 3600;
        $cache = Yii::$app->cache;
        $brand_key = 'diandian_brand_list_';
        $brands = $cache->get($brand_key);
        if (!$brands) {
            $result = $this->getBrandList();
            //对获取的结果进行字母排序

            $brands = [];
            foreach ($result as $brand) {
                $cache->set('diandian_brand__' . $brand['brandId'], $brand, $expires);
                $brands[$brand['alphaCode']][] = ['id' => $brand['brandId'], 'name' => $brand['brandName'], 'icon' => $brand['icon']];
            }
            $cache->set($brand_key, $brands, $expires);
            return $brands;
        }
        return $brands;
    }

    public function getSerieslist($brand_id)
    {
        $url = $this->url . '/basedata/series/list';
        $url = $url . '?' . $this->getParam([
                'brandId' => $brand_id
            ]);
        return $this->urlget($url);

    }

    public function Series($brand_id)
    {
        $expires = 30 * 24 * 3600;
        $cache = Yii::$app->cache;
        $series_key = 'diandian_series_list__' . $brand_id;
        $series = $cache->get($series_key);
        if (!$series) {
            $result = $this->getSerieslist($brand_id);
            //对获取的结果进行字母排序

            $series = [];
            foreach ($result as $brand) {
                $series[] = ['id' => $brand['seriesId'], 'name' => $brand['seriesName']];
            }
            $cache->set($series_key, $series, $expires);
        }
        return $series;
    }

    public function getModellist($series_id)
    {
        $url = $this->url . '/basedata/model/list';
        $url = $url . '?' . $this->getParam([
                'seriesId' => $series_id
            ]);
        return $this->urlget($url);
    }
}