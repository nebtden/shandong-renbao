<?php

namespace common\components;

use Yii;
use yii\base\Component;

/**
 * 获取汽车品牌与相应的车型
 * Class CarCateType
 * @package common\components
 */
class CarCateType extends Component
{
    const BRAND  = "http://api.jisuapi.com/car/brand?appkey=";
    const MODEL  = "http://api.jisuapi.com/car/type?appkey=";
    const SERIES = "https://api.jisuapi.com/car/car?appkey=";
    //获得所有汽车品牌
    public static function Brand()
    {
        $appkey = Yii::$app->params['jisuapi'];
        $expires = 30 * 24 * 3600;
        $cache = Yii::$app->cache;
        $key = "alxg_car_brand_list";
        $list = $cache->get($key);
        if ($list === false) {
            $url = self::BRAND . $appkey;
            $res = W::http_get($url);
            $res = json_decode($res, true);
            if ((int)$res['status'] !== 0) {
                return [];
            }
            $temp = $res['result'];
            foreach ($temp as $brand) {
                $list[$brand['initial']][] = [
                    'id' => $brand['id'],
                    'name' => $brand['name'],
                    'initial' => $brand['initial'],
                    'logo' => $brand['logo'],
                ];
            }
            $cache->set($key, $list, $expires);
        }
        return $list;
    }

    public static function Type($id = 0)
    {
        if (!$id) return [];
        $appkey = Yii::$app->params['jisuapi'];
        $expires = 30 * 24 * 3600;
        $cache = Yii::$app->cache;
        $key = "alxg_car_type_parent_" . $id;
        $list = $cache->get($key);
        if ($list === false) {
            $url = self::MODEL . $appkey . "&parentid=" . $id;
            $res = W::http_get($url);
            $res = json_decode($res, true);
            if ((int)$res['status'] !== 0) {
                return [];
            }
            $list = $res['result'];
            $cache->set($key, $list, $expires);
        }
        return $list;
    }

    public static function Info($id = 0)
    {
        if (!$id) return [];
        $appkey = Yii::$app->params['jisuapi'];
        $expires = 30 * 24 * 3600;
        $cache = Yii::$app->cache;
        $key = "alxg_car_info_parent_" . $id;
        $list = $cache->get($key);
        if ($list === false) {
            $url = self::SERIES . $appkey . "&parentid=" . $id;
            $res = W::http_get($url);
            $res = json_decode($res, true);
            if ((int)$res['status'] !== 0) {
                return [];
            }
            $list = $res['result']['list'];
            $cache->set($key, $list, $expires);
        }
        return $list;
    }

}