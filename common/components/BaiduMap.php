<?php

namespace common\components;

use Yii;
use yii\base\Component;

class BaiduMap extends Component
{
    public static function getCityByIp()
    {
        $ip = Yii::$app->request->userIP;
        //$ip = '58.20.54.172';
        $key = 'city_' . $ip;
        $city = Yii::$app->cache->get($key);
        if ($city === false) {
            $appkey = Yii::$app->params['BmapServer'];
            $url = 'http://api.map.baidu.com/location/ip?ak=' . $appkey . "&ip=" . $ip;
            $rs = W::http_get($url);
            $rs = json_decode($rs, true);
            if (!$rs['status']) {
                $address = $rs['address'];
                $address = explode("|", $address);
                $city = $address[2];
                Yii::$app->cache->set($key, $city, 1800);
            }
        }
        return $city;
    }

    /**
     * 地点检索
     * @param string $query 要查询的地点
     * @param string $region 检索范围
     * @param string $citylimit
     * @return array|bool
     */
    public static function search($query = '', $region = '全国', $citylimit = 'false')
    {
        if (!$query) return false;
        if (!$region) $region = "全国";
        $ak = Yii::$app->params['BmapServer'];
        $url = "http://api.map.baidu.com/place/v2/search?query={$query}&region={$region}&city_limit={$citylimit}&output=json&ak=" . $ak;
        $list = [];
        $rs = W::http_get($url);
        $rs = json_decode($rs, true);
        if ($rs['results']) {
            foreach ($rs['results'] as $val) {
                if (!isset($val['location'])) continue;
                $list[] = [
                    'name' => $val['name'],
                    'lng' => $val['location']['lng'],
                    'lat' => $val['location']['lat'],
                    'address' => $val['address']
                ];
            }
        }
        return $list;
    }

    /**
     * 坐标转换
     * @param array point = [0=> 114.21892734521,29.575429778924]
     * @return array|bool [0=>[x=>经度,y=>纬度]]
     */
    public static function coords($point = [])
    {
        $str = implode(";", $point);
        $ak = Yii::$app->params['BmapServer'];
        $url = 'http://api.map.baidu.com/geoconv/v1/?coords=' . $str . '&from=3&ak=' . $ak;
        $list = [];
        $rs = W::http_get($url);
        $rs = json_decode($rs, true);
        if (!$rs['status']) {
            $list = $rs['result'];
        } else {
            return false;
        }
        return $list;
    }

    /**
     * 逆地理编码
     * @param string $lng
     * @param string $lat
     * @return array|bool
     */
    public static function geocoder($lat,$lng)
    {
        $location = $lat . ',' . $lng;
        $ak = Yii::$app->params['BmapServer'];
        $url = 'http://api.map.baidu.com/geocoder/v2/?location='.$location.'&output=json&ak='.$ak;
        $list = [];
        $res = W::http_get($url);
        $res = json_decode($res,true);
        if($res['status'] == 0){
            $list = $res['result']['addressComponent'];
        } else {
            return false;
        }
        return $list;
    }

    /**
     * @param $lat1
     * @param $lng1
     * @param $lat2
     * @param $lng2
     * @return int
     */
    public static function getDistance($lat1, $lng1, $lat2, $lng2){

        // 将角度转为狐度
        $radLat1 = deg2rad($lat1);// deg2rad()函数将角度转换为弧度
        $radLat2 = deg2rad($lat2);
        $radLng1 = deg2rad($lng1);
        $radLng2 = deg2rad($lng2);

        $a = $radLat1 - $radLat2;
        $b = $radLng1 - $radLng2;

        $s = 2 * asin(sqrt(pow(sin($a / 2), 2)+cos($radLat1) * cos($radLat2) * pow(sin($b / 2), 2))) * 6378.137;

        return $s;

    }
    /**
     * @param $lat1
     * @param $lon1
     * @param $lat2
     * @param $lon2
     * @param float $radius  星球半径 KM
     * @return float
     */
    public static function distance($lat1, $lon1, $lat2,$lon2,$radius = 6378.137)
    {
        $rad = floatval(M_PI / 180.0);

        $lat1 = floatval($lat1) * $rad;
        $lon1 = floatval($lon1) * $rad;
        $lat2 = floatval($lat2) * $rad;
        $lon2 = floatval($lon2) * $rad;

        $theta = $lon2 - $lon1;

        $dist = acos(sin($lat1) * sin($lat2) + cos($lat1) * cos($lat2) * cos($theta));

        if ($dist < 0 ) {
            $dist += M_PI;
        }
        return $dist = $dist * $radius;
    }
}