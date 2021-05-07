<?php

namespace common\components;

use Yii;
use yii\base\Component;

class Helpper extends Component
{
    //验证手机验证码是否正确
    public static function vali_mobile_code($mobile, $code, &$msg, $prefix = '')
    {
        $token = Yii::$app->session['token'];
        $key = $prefix . $mobile . $token;
        $cache = Yii::$app->cache;
        if (!$code) {
            $msg = '请输入验证码';
            return false;
        }
        $precode = $cache->get($key);
        if (!$precode) {
            $msg = '请先获取验证码';
            return false;
        }
        if ($code != $precode) {
            $msg = '验证码不正确';
            return false;
        }
        $cache->delete($key);
        return true;
    }

    public static function multisort($arrays, $sort_key, $sort_order = SORT_ASC, $sort_type = SORT_NUMERIC)
    {
        if (is_array($arrays)) {
            foreach ($arrays as $array) {
                if (is_array($array)) {
                    $key_arrays[] = $array[$sort_key];
                } else {
                    return false;
                }
            }
        } else {
            return false;
        }
        array_multisort($key_arrays, $sort_order, $sort_type, $arrays);
        return $arrays;
    }

    public static function check_os()
    {
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone') || strpos($_SERVER['HTTP_USER_AGENT'], 'iPad')) {
            return 'ios';
        } else if (strpos($_SERVER['HTTP_USER_AGENT'], 'Android')) {
            return 'android';
        } else {
            return 'android';
        }
    }
}