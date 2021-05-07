<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2019\5\27 0027
 * Time: 10:11
 */

namespace common\components;
use Yii;

class JuheExp
{
    /**
     * 快递接口
     * @param array $params
     */
    public static function deliver($params = []){
        static $queryUrl = 'http://v.juhe.cn/exp/index';
        static $comUrl = 'http://v.juhe.cn/exp/com';
        //$appkey = '53f10d5eabdde5acacf51dbf1b6473e1';
        $appkey = '086b9a0a43a04290084e7ecfa1dba740';

        $key = md5('juhe_deliver'.$appkey.$params['com'].$params['no']);
        $content = \Yii::$app->cache->get($key);
        if($content){
            $rs = json_decode($content,true);
            if($rs['error_code']){
                $content = null;
            }
        }
        if(!$content){
            if($params){
                $params = array(
                    'key' => $appkey,
                    'com'  => $params['com'],
                    'no' => $params['no']
                );
                $content = W::http_post($queryUrl,$params);
                $rs = json_decode($content,true);
                if(!$rs['error_code']) \Yii::$app->cache->set($key,$content,12*3600);
            }else{
                $params = 'key='.$appkey;
                $content = W::http_post($comUrl,$params);
                $rs = json_decode($content,true);
                if(!$rs['error_code']) \Yii::$app->cache->set($key,$content,24*3600*7);
            }
        }
        return json_decode($content,true);
    }

}