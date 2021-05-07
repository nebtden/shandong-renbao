<?php
/**
 * 盛大洗车接口
 * @time: 2018-11-01
 * @author: chenbh
 */

namespace common\components;

use Yii;
use GuzzleHttp\Client;
use common\components\Encrypt3Des;

class ShengDaSegway
{
    public function __construct()
    {
        $this->redis = Yii::$app->redis;
        $this->expire = 60 * 60 * 24 * 30;
        $this->http = new Client();
        $this->url = Yii::$app->params['segway_url'];
        $this->data = [
            'headers' => [
                'Content-Type' => 'application/json',
            ]
        ];
    }

    /**
     * 记录盛大代步接口调用日志
     * @param $url
     * @param $input
     * @param $return
     */
    public function log($url, $postData, $return, $type = '')
    {
        $content = "--------------" . date('Y-m-d H:i:s') . "--------------" . PHP_EOL;
        $content .= 'url:' . $url . PHP_EOL;
        $content .= 'input:' . $postData . PHP_EOL;
        $content .= 'return:' . $return . PHP_EOL;
        $content .= PHP_EOL;
        $path = Yii::$app->getBasePath() . '/web/log/segway/' . date('Y-m') . '/';
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
        $fopenLog = fopen($path . $type . date('Ymd') . '.log', 'a+');
        fwrite($fopenLog, $content);
        fclose($fopenLog);
    }

    protected function urlpost($url, $data)
    {
        $res = $this->http->request('POST', $url, $data);
        $return_data = $res->getBody()->getContents();
        $return = \GuzzleHttp\json_decode($return_data, true);
        $this->log($url, json_encode($data), $return_data);
        return $return;
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

    /**
     * 创建订单
     * @param $postData
     * @return mixed
     */
    public function createOrder($postData)
    {
        $url = $this->url . 'driving-order-service-test/external_terminal/thirdParty_order/create';
        $postData = json_encode($postData);
        $this->data['body'] = $postData;
        return $this->urlpost($url, $this->data);

    }

    /**
     * 取消订单
     * @param $postData
     * @return mixed
     */
    public function cancelOrder($postData)
    {
        $url = $this->url . 'driving-order-service-test/external_terminal/order/cancel';
        $postData = json_encode($postData);
        $this->data['body'] = $postData;
        return $this->urlpost($url, $this->data);
    }

    public function findOrder($getData)
    {
        $url = $this->url . 'driving-order-service-test/external_terminal/thirdParty_order/find';
        $urlParam = http_build_query($getData);
        $url = $url . '?' . $urlParam;
        $orderInfo = $this->urlget($url, $this->data);
        return $orderInfo;
    }

    /**
     * 门店查询
     * @param $postData
     * @return mixed
     */
    public function findStore($data)
    {
        $resList = $this->redis->hmget('storeList', $data['provinceCode'] . '_' . $data['cityCode'] . '_' . $data['countyCode']);
        $storeList = $resList[0];
        if (!$storeList) {
            $postData = [
                'secondarySupplierType' => $data['secondarySupplierType'],
                'provinceCode' => $data['provinceCode'],
                'cityCode' => $data['cityCode'],
                'countyCode' => $data['countyCode'],
            ];
            $getData = [
                'pageNum' => $data['pageNum'],
                'pageSize' => $data['pageSize'],
            ];
            $urlParam = http_build_query($getData);
            $url = $this->url . 'driving-supplier-service-test/secondary_supplier/query_page?' . $urlParam;
            $postData = json_encode($postData);
            $this->data['body'] = $postData;
            $storeList = $this->urlpost($url, $this->data);
            if ($storeList['resultCode'] === '0000') {
                $storeList = json_encode($storeList['data']['dataList'],JSON_UNESCAPED_UNICODE);
                $this->redis->hmset('storeList', $data['provinceCode'] . '_' . $data['cityCode'] . '_' . $data['countyCode'], $storeList);
            }
        }
        return json_decode($storeList);
    }

    /**
     * 查询所有省份
     * @param $postData
     * @return mixed
     */
    public function getProvince($postData = [])
    {
        $resList = $this->redis->get('provinceList');
        if (!$resList) {
            $url = $this->url . 'driving-system-service-test/area/getAllProvince';
            $postData = json_encode($postData);
            $this->data['body'] = $postData;
            $provinceList = $this->urlpost($url, $this->data);
            $resList = null;
            if ($provinceList['resultCode'] === '0000') {
                $resList = json_encode($provinceList['provinceList'],JSON_UNESCAPED_UNICODE);
                $this->redis->setex('provinceList', $this->expire, $resList);
            }
        }
        return json_decode($resList);

    }

    /**
     * 根据省份code获得城市
     * @param $getData
     * @return mixed
     */
    public function getCity($getData)
    {
        $resList = $this->redis->hmget('cityList', $getData['provinceCode']);
        $cityList = $resList[0];
        if (!$cityList) {
            $url = $this->url . 'driving-system-service-test/area/getCityByProvince';
            $urlParam = http_build_query($getData);
            $url = $url . '?' . $urlParam;
            $cityList = $this->urlget($url, $this->data);
            if ($cityList['resultCode'] === '0000') {
                $cityList = json_encode($cityList['cityList'],JSON_UNESCAPED_UNICODE);
                $this->redis->hmset('cityList', $getData['provinceCode'], $cityList);
            }
        }
        return json_decode($cityList);
    }

    /**
     * @param $getData
     * @return mixed
     */
    public function getRegion($getData)
    {
        $resList = $this->redis->hmget('countyList', $getData['cityCode']);
        $countyList = $resList[0];
        if (!$countyList) {
            $url = $this->url . 'driving-system-service-test/area/getCountyByCity';
            $urlParam = http_build_query($getData);
            $url = $url . '?' . $urlParam;
            $countyList = $this->urlget($url, $this->data);
            if ($countyList['resultCode'] === '0000') {
                $countyList = json_encode($countyList['countyList'],JSON_UNESCAPED_UNICODE);
                $this->redis->hmset('countyList', $getData['cityCode'], $countyList);
            }
        }
        return json_decode($countyList);
    }
}