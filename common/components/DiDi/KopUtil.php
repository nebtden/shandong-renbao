<?php
/**
 * Created by IntelliJ IDEA.
 * User: didi
 * Date: 2018/5/31
 * Time: 下午4:13
 */

namespace php;


class KopUtil
{
//    请求KOP
    public function _request($aPlatParams,$aBizParams, $appSecret) {
        $result = [];
        $aPlatParams['sign'] = $this->_openAppSign($aPlatParams, $aBizParams, $appSecret);
        //sUrl
        $sUrl = 'https://test.kuaidadi.com:9002/gateway';
        $sUrl .= '?' . http_build_query($aPlatParams);
        //request
        $sBizParams = json_encode($aBizParams);
        $aRet = $this->_post($sUrl, $sBizParams);
        if (!empty($aRet) && $aRet['errno'] == 0 && isset($aRet['ret'])) {
            $result = json_decode($aRet['ret'], true);
        } else {
            echo "Request kop tripcomment failed.||param=%s||res=%s", json_encode($aBizParams), json_encode($aRet);
        }
        return $result;
    }

    //  简单的post请求；
    public function _post($url, $sBizParams) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $sBizParams);
        curl_setopt($ch, CURLOPT_TIMEOUT_MS, 200);
        curl_setopt($ch, CURLOPT_NOSIGNAL, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, 100);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-type: text/plain']);
        $rep_contents = curl_exec($ch);
        return $rep_contents;
    }

//    开放APP，算签
    public function _openAppSign(array $aPlatParams, array $aBizParams, $appSecret) {
        //对业务参数，按照key进行升序排列
        ksort($aBizParams);
        $bizMerges = $this->mergeKeyValue($aBizParams, $appSecret);

        //对系统参数，按照key进行降序排列
        krsort($aPlatParams);
        $sysMerges = $this->mergeKeyValue($aPlatParams, $appSecret);
        return md5($this->calculate($bizMerges, $sysMerges));
    }

    private function mergeKeyValue(array $bizParams, $appSecret) {
        $sSign = $appSecret;
        foreach ($bizParams as $k => $v) {
            $sSign .= $k . $v;
        }
        return $sSign . $appSecret;
    }

    private function calculate($bizMerge, $sysMerge) {
        $aSign = '';
        $bizLength = strlen($bizMerge);
        $sysLength = strlen($sysMerge);
        $length = $bizLength > $sysLength ? $bizLength : $sysLength;
        for($i=0; $i < $length; $i++) {
            $r = 0;
            if($i < $bizLength && $i < $sysLength) {
                $r = $bizMerge[$i] & $sysMerge[$i];
            } else if($i < $bizLength) {
                $r = $bizMerge[$i] & 'l';
            } else {
                $r = $sysMerge[$i] & 'l';
            }
            $aSign .= ord($r);
        }
        return $aSign;
    }
}