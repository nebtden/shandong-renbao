<?php

namespace common\components;

use Yii;

class Juhe
{
    /**
     * 快递接口
     * @param array $params
     */
    public static function deliver($params = [])
    {
        static $queryUrl = 'http://v.juhe.cn/exp/index';
        static $comUrl = 'http://v.juhe.cn/exp/com';
        $appkey = '53f10d5eabdde5acacf51dbf1b6473e1';

        $key = md5('juhe_deliver' . $appkey . $params['com'] . $params['no']);
        $content = \Yii::$app->cache->get($key);
        if (!$content) {
            if ($params) {
                $params = array(
                    'key' => $appkey,
                    'com' => $params['com'],
                    'no' => $params['no']
                );
                $content = W::http_post($queryUrl, $params);
                \Yii::$app->cache->set($key, $content, 12 * 3600);
            } else {
                $params = 'key=' . $appkey;
                $content = W::http_post($comUrl, $params);
                \Yii::$app->cache->set($key, $content, 24 * 3600 * 7);
            }
        }
        return json_decode($content, true);
    }
}
