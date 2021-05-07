<?php
namespace common\components;

/**
 * 手机归属地查询
 * Class PhoneArea
 * @package common\components
 */
class PhoneArea
{
    public static function S360($mobile = ''){
        if(!$mobile) return false;
        $url = 'https://cx.shouji.360.cn/phonearea.php?number='.$mobile;
        $json = W::http_get($url);
        if($json){
            $result = json_decode($json,true);
            if((int)$result['code'] !== 0){
                return false;
            }else{
                $data = $result['data'];
                return $data;
            }
        }
        return false;
    }
}