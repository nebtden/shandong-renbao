<?php
/**
 * Created by IntelliJ IDEA.
 * User: didi
 * Date: 2018/5/31
 * Time: 下午4:13
 */

namespace php;


class DidiBizUtil
{

    //生成系统参数,需要登陆
    public function _genPlatParamsByUid($ttid, $appKey, $api, $apiVersion, $token, $userId) {
            $aPlatParams['ttid'] = $ttid;
            $aPlatParams['api'] = $api;
            $aPlatParams['apiVersion'] = $apiVersion;
            $aPlatParams['appKey'] = $appKey;
            $aPlatParams['timestamp'] = floor(microtime(true)*1000);
            $aPlatParams['userId'] = '1326163679';
            $aPlatParams['userRole'] = 1;
            $aPlatParams['appVersion'] = '1.0.0';
            $aPlatParams['osType'] = 3;
            $aPlatParams['osVersion'] = 'ttid_server';
            $aPlatParams['hwId'] = 'ttid_server';
            $aPlatParams['mobileType'] = 'ttid_server';
            $aPlatParams['token'] = $token;
            $aPlatParams['userId'] = $userId;
            return $aPlatParams;
    }


}